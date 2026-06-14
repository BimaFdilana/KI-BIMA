<?php

namespace App\Events;

use App\Models\Toko\TokoPayment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentVerificationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public int $paymentId;
    public string $transactionId;

    /**
     * Create a new event instance.
     */
    public function __construct(TokoPayment $payment, string $action)
    {
        $this->action = $action; // 'confirmed' or 'rejected'
        $this->paymentId = $payment->id;
        $this->transactionId = $payment->transaction_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('approval-dashboard');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'payment.verification.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'payment',
            'action' => $this->action,
            'payment_id' => $this->paymentId,
            'transaction_id' => $this->transactionId,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
