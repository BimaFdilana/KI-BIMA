<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Services\Message\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Display the message sending interface
     */
    public function index()
    {
        $devices = Device::where('status', 'connected')->get();
        $page_title = 'WhatsApp Messages';

        return view('messages.index', compact('devices', 'page_title'));
    }

    /**
     * Show the form for sending messages
     */
    public function showSendForm()
    {
        $devices = Device::where('status', 'connected')->get();
        $page_title = 'Send WhatsApp Message';

        return view('messages.send', compact('devices', 'page_title'));
    }

    /**
     * Send a WhatsApp message
     */
    public function sendMessage(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'target' => 'required|string|min:10|max:15',
                'message' => 'required|string|max:4096',
                'device_token' => 'required|string',
            ], [
                'target.required' => 'Target phone number is required',
                'target.min' => 'Phone number must be at least 10 digits',
                'target.max' => 'Phone number must not exceed 15 digits',
                'message.required' => 'Message is required',
                'message.max' => 'Message must not exceed 4096 characters',
                'device_token.required' => 'Device token is required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $target = $request->input('target');
            $message = $request->input('message');
            $deviceToken = $request->input('device_token');

            // Cek apakah device token valid
            $device = Device::where('token', $deviceToken)->first();
            if (!$device) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid device token'
                ], 401);
            }

            // Kirim pesan menggunakan FonnteService
            $response = $this->fonnteService->sendWhatsAppMessage($target, $message, $deviceToken);

            // Log the response for debugging
            Log::info('WhatsApp message response', [
                'target' => $target,
                'response' => $response
            ]);

            // Periksa respons dari API
            if (!$response['status'] || (isset($response['data']['status']) && !$response['data']['status'])) {
                $errorReason = $response['data']['reason'] ?? $response['error'] ?? 'Unknown error occurred';

                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send message',
                    'error' => $errorReason
                ], 500);
            }

            // Simpan ke database jika diperlukan
            $this->saveMessageToDatabase($device->id, $target, $message, 'sent', $response['data'] ?? []);

            return response()->json([
                'status' => true,
                'message' => 'Message sent successfully!',
                'data' => $response['data'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send bulk messages
     */
    public function sendBulkMessages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'targets' => 'required|array|min:1|max:100',
            'targets.*' => 'required|string|min:10|max:15',
            'message' => 'required|string|max:4096',
            'device_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $targets = $request->input('targets');
        $message = $request->input('message');
        $deviceToken = $request->input('device_token');

        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($targets as $target) {
            try {
                $response = $this->fonnteService->sendWhatsAppMessage($target, $message, $deviceToken);

                if ($response['status']) {
                    $results[] = [
                        'target' => $target,
                        'status' => 'sent',
                        'message_id' => $response['data']['id'] ?? null
                    ];
                    $successCount++;
                } else {
                    $results[] = [
                        'target' => $target,
                        'status' => 'failed',
                        'error' => $response['error'] ?? 'Unknown error'
                    ];
                    $failCount++;
                }

                // Delay untuk menghindari rate limiting
                usleep(500000); // 0.5 detik

            } catch (\Exception $e) {
                $results[] = [
                    'target' => $target,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                $failCount++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Bulk message completed. Success: {$successCount}, Failed: {$failCount}",
            'summary' => [
                'total' => count($targets),
                'success' => $successCount,
                'failed' => $failCount
            ],
            'results' => $results
        ]);
    }

    /**
     * Get message history
     */
    public function getMessageHistory(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $deviceId = $request->input('device_id');

        $query = Message::with('device')->orderBy('created_at', 'desc');

        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        $messages = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $messages
        ]);
    }

    /**
     * Get message status
     */
    public function getMessageStatus($messageId)
    {
        $message = Message::find($messageId);

        if (!$message) {
            return response()->json([
                'status' => false,
                'message' => 'Message not found'
            ], 404);
        }

        // Jika diperlukan, cek status dari Fonnte API
        if ($message->fonnte_message_id) {
            $response = $this->fonnteService->getMessageStatus($message->fonnte_message_id);

            if ($response['status']) {
                // Update status di database
                $message->update([
                    'status' => $response['data']['status'] ?? $message->status,
                    'delivery_status' => $response['data']['delivery_status'] ?? null
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'data' => $message
        ]);
    }

    /**
     * Handle webhook from Fonnte
     */
    public function handleFonnteWebhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('Fonnte webhook received', $data);

            // Proses webhook data sesuai kebutuhan
            // Misalnya update status pesan, simpan pesan masuk, dll.

            return response()->json([
                'status' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing Fonnte webhook', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error processing webhook'
            ], 500);
        }
    }

    /**
     * Handle callback from Fonnte
     */
    public function handleCallback(Request $request)
    {
        return $this->handleFonnteWebhook($request);
    }

    /**
     * Admin: Get message logs
     */
    public function adminLogs(Request $request)
    {
        $this->authorize('admin'); // Pastikan hanya admin yang bisa akses

        $messages = Message::with(['device'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.messages.logs', compact('messages'));
    }

    /**
     * Save message to database
     */
    private function saveMessageToDatabase($deviceId, $target, $message, $status, $responseData = [])
    {
        try {
            Message::create([
                'device_id' => $deviceId,
                'target' => $target,
                'message' => $message,
                'status' => $status,
                'fonnte_message_id' => $responseData['id'] ?? null,
                'response_data' => json_encode($responseData),
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving message to database', [
                'error' => $e->getMessage(),
                'device_id' => $deviceId,
                'target' => $target
            ]);
        }
    }

    /**
     * Validate headers for API requests
     */
    protected function validateHeaders($authorizationHeader, $deviceToken)
    {
        if (empty($authorizationHeader)) {
            return response()->json([
                'status' => false,
                'message' => 'Authorization header is required'
            ], 401);
        }

        if ($authorizationHeader !== $deviceToken) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Device Authorization Token!'
            ], 401);
        }

        return null;
    }
}
