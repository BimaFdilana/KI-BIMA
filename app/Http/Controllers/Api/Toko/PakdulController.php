<?php

namespace App\Http\Controllers\Api\Toko;

use App\Http\Controllers\Controller;
use App\Services\Toko\PakDulService;
use App\Services\Toko\TokoService;
use App\Services\ValidatorService;
use App\Models\PakDul\PayLatterAccount;
use App\Models\PakDul\PayLatterTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class PakDulController extends Controller
{
    protected $pakDulService;
    protected $tokoService;
    protected $validatorService;

    public function __construct(
        PakDulService $pakDulService,
        TokoService $tokoService,
        ValidatorService $validatorService
    ) {
        $this->pakDulService = $pakDulService;
        $this->tokoService = $tokoService;
        $this->validatorService = $validatorService;
    }

    /**
     * Check if user has PayLatter account
     */
    public function checkAccount(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $toko = $this->validatorService->validateStore($user);

            if ($toko instanceof JsonResponse) {
                return $toko;
            }

            $account = PayLatterAccount::where('user_id', $user->id)
                ->where('toko_id', $toko->id)
                ->first();

            $accountData = null;
            if ($account) {
                $accountData = [
                    'id' => $account->id,
                    'credit_limit' => $account->credit_limit,
                    'available_credit' => $account->available_credit,
                    'used_credit' => $account->used_credit,
                    'status' => $account->status,
                    'credit_score' => $account->calculateCreditScore()
                ];
            }

            return $this->successResponse([
                'has_account' => !is_null($account),
                'account' => $accountData
            ], 'Account check completed');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to check account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create new PayLatter account
     */
    public function createAccount(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $toko = $this->validatorService->validateStore($user);

            if ($toko instanceof JsonResponse) {
                return $toko;
            }

            $account = $this->pakDulService->initializeAccount($user, $toko);

            return $this->successResponse([
                'account' => [
                    'id' => $account->id,
                    'credit_limit' => $account->credit_limit,
                    'available_credit' => $account->available_credit,
                    'used_credit' => $account->used_credit,
                    'status' => $account->status,
                    'created_at' => $account->created_at
                ]
            ], 'PayLatter account created successfully', 201);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to create account: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Get account data and summary
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $toko = $this->validatorService->validateStore($user);

            if ($toko instanceof JsonResponse) {
                return $toko;
            }

            $summary = $this->pakDulService->getAccountSummary($user, $toko);

            if (!$summary) {
                return $this->errorResponse('PayLatter account not found', 404);
            }

            return $this->successResponse($summary, 'Account data retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to get account data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get payment history
     */
    public function getPaymentHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $toko = $this->validatorService->validateStore($user);

            if ($toko instanceof JsonResponse) {
                return $toko;
            }

            $account = PayLatterAccount::where('user_id', $user->id)
                ->where('toko_id', $toko->id)
                ->first();

            if (!$account) {
                return $this->errorResponse('PayLatter account not found', 404);
            }

            $query = $account->transactions()->with('payments');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $transactions = $query->orderBy('created_at', 'desc')
                ->paginate($request->limit ?? 10);

            $formattedTransactions = $this->formatTransactions($transactions);

            return $this->successResponse([
                'transactions' => $formattedTransactions,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'last_page' => $transactions->lastPage()
                ]
            ], 'Payment history retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to get payment history: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process payment
     */
    public function payment(Request $request): JsonResponse
    {
        $validationRules = [
            'transaction_code' => 'required|exists:paylatter_transactions,transaction_code',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_details' => 'required|array',
        ];

        $valid = $this->validatorService->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }

        try {
            $user = Auth::user();
            $toko = $this->validatorService->validateStore($user);

            if ($toko instanceof JsonResponse) {
                return $toko;
            }

            $transaction = PayLatterTransaction::where('transaction_code', $request->transaction_code)->first();

            // Ensure transaction belongs to authenticated user
            if ($transaction->account->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized access to transaction', 403);
            }

            $payment = $this->pakDulService->processPayment(
                $transaction,
                $request->amount,
                $request->payment_method,
                $request->payment_details
            );

            return $this->successResponse([
                'payment' => [
                    'payment_code' => $payment->payment_code,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'paid_at' => $payment->paid_at,
                    'status' => $payment->status
                ],
                'transaction' => [
                    'remaining_amount' => $transaction->fresh()->remaining_amount,
                    'status' => $transaction->fresh()->status
                ]
            ], 'Payment processed successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to process payment: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Check eligibility for PayLatter
     */
    public function checkEligibility(Request $request): JsonResponse
    {
        $validationRules = [
            'amount' => 'required|numeric|min:1'
        ];

        $valid = $this->validatorService->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }

        try {
            $user = Auth::user();
            $toko = $this->validatorService->validateStore($user);

            if ($toko instanceof JsonResponse) {
                return $toko;
            }

            $canUse = $this->pakDulService->canUsePaylatter($user, $toko, $request->amount);

            $account = PayLatterAccount::where('user_id', $user->id)
                ->where('toko_id', $toko->id)
                ->first();

            return $this->successResponse([
                'eligible' => $canUse,
                'account_status' => $account ? $account->status : 'no_account',
                'available_credit' => $account ? $account->available_credit : 0,
                'requested_amount' => $request->amount,
                'has_overdue' => $account ? $this->hasOverdueTransactions($account) : false
            ], 'Eligibility check completed');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to check eligibility: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get transaction details
     */
    public function getTransaction(Request $request): JsonResponse
    {
        $validationRules = [
            'transaction_code' => 'required|exists:paylatter_transactions,transaction_code'
        ];

        $valid = $this->validatorService->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }

        try {
            $user = Auth::user();
            $transaction = PayLatterTransaction::with(['payments', 'account'])
                ->where('transaction_code', $request->transaction_code)
                ->first();

            // Ensure transaction belongs to authenticated user
            if ($transaction->account->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized access to transaction', 403);
            }

            $transactionData = [
                'id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'order_id' => $transaction->order_id,
                'principal_amount' => $transaction->principal_amount,
                'interest_amount' => $transaction->interest_amount,
                'penalty_amount' => $transaction->penalty_amount,
                'total_amount' => $transaction->total_amount,
                'paid_amount' => $transaction->paid_amount,
                'remaining_amount' => $transaction->remaining_amount,
                'due_date' => $transaction->due_date,
                'grace_period_end' => $transaction->grace_period_end,
                'status' => $transaction->status,
                'paid_at' => $transaction->paid_at,
                'created_at' => $transaction->created_at,
                'payments' => $transaction->payments->map(function ($payment) {
                    return [
                        'payment_code' => $payment->payment_code,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'paid_at' => $payment->paid_at,
                        'status' => $payment->status
                    ];
                })
            ];

            return $this->successResponse(['transaction' => $transactionData], 'Transaction details retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to get transaction details: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Format transactions for response
     */
    private function formatTransactions($transactions)
    {
        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'order_id' => $transaction->order_id,
                'principal_amount' => $transaction->principal_amount,
                'interest_amount' => $transaction->interest_amount,
                'penalty_amount' => $transaction->penalty_amount,
                'total_amount' => $transaction->total_amount,
                'paid_amount' => $transaction->paid_amount,
                'remaining_amount' => $transaction->remaining_amount,
                'due_date' => $transaction->due_date,
                'grace_period_end' => $transaction->grace_period_end,
                'status' => $transaction->status,
                'paid_at' => $transaction->paid_at,
                'created_at' => $transaction->created_at,
                'payments' => $transaction->payments->map(function ($payment) {
                    return [
                        'payment_code' => $payment->payment_code,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'paid_at' => $payment->paid_at,
                        'status' => $payment->status
                    ];
                })
            ];
        });
    }

    /**
     * Check if account has overdue transactions
     */
    private function hasOverdueTransactions(PayLatterAccount $account): bool
    {
        return $account->transactions()
            ->where('status', 'overdue')
            ->exists();
    }

    /**
     * Success response helper
     */
    private function successResponse($data, $message = 'Success', $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode, ['Content-Type' => 'application/json']);
    }

    /**
     * Error response helper
     */
    private function errorResponse($message, $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode, ['Content-Type' => 'application/json']);
    }
}
