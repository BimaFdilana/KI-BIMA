<?php

namespace Database\Factories\Toko;

use App\Models\Auth\UserModel;
use App\Models\Toko\TokoPaymentProgress;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class TokoPaymentProgressFactory extends Factory
{
    protected $model = TokoPaymentProgress::class;

    public function definition(): array
    {
        $statuses = [
            TokoPaymentProgress::STATUS_PAID,
            TokoPaymentProgress::STATUS_PENDING,
            TokoPaymentProgress::STATUS_FAILED,
            TokoPaymentProgress::STATUS_UNKNOWN,
            TokoPaymentProgress::STATUS_SUCCESS,
            TokoPaymentProgress::STATUS_DELIVERY,
            TokoPaymentProgress::STATUS_CANCELLED,
            TokoPaymentProgress::STATUS_REFUND_REQUESTED,
            TokoPaymentProgress::STATUS_REFUNDED,
        ];

        $statusDescriptions = [
            TokoPaymentProgress::STATUS_PAID => 'Pesanan telah dibuat',
            TokoPaymentProgress::STATUS_PENDING => 'Menunggu pembayaran',
            TokoPaymentProgress::STATUS_FAILED => 'Pembayaran telah dikonfirmasi',
            TokoPaymentProgress::STATUS_UNKNOWN => 'Pesanan sedang diproses',
            TokoPaymentProgress::STATUS_SUCCESS => 'Pesanan dalam pengiriman',
            TokoPaymentProgress::STATUS_DELIVERY => 'Pesanan telah sampai di tujuan',
            TokoPaymentProgress::STATUS_CANCELLED => 'Pesanan telah selesai',
            TokoPaymentProgress::STATUS_REFUND_REQUESTED => 'Pesanan dibatalkan',
            TokoPaymentProgress::STATUS_REFUND_REQUESTED => 'Permintaan pengembalian dana',
            TokoPaymentProgress::STATUS_REFUNDED => 'Dana telah dikembalikan'
        ];

        $status = $this->faker->randomElement($statuses);

        return [
            'status' => $status,
            'keterangan' => $statusDescriptions[$status] ?? 'Status baru diperbarui',
            'user_id' => UserModel::all()->random()->id,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function withStatus(string $status)
    {
        return $this->state(function (array $attributes) use ($status) {
            $statusDescriptions = [
                TokoPaymentProgress::STATUS_PAID => 'Pesanan telah dibuat',
                TokoPaymentProgress::STATUS_PENDING => 'Menunggu pembayaran',
                TokoPaymentProgress::STATUS_FAILED => 'Pembayaran telah dikonfirmasi',
                TokoPaymentProgress::STATUS_UNKNOWN => 'Pesanan sedang diproses',
                TokoPaymentProgress::STATUS_SUCCESS => 'Pesanan dalam pengiriman',
                TokoPaymentProgress::STATUS_DELIVERY => 'Pesanan telah sampai di tujuan',
                TokoPaymentProgress::STATUS_CANCELLED => 'Pesanan telah selesai',
                TokoPaymentProgress::STATUS_REFUND_REQUESTED => 'Permintaan pengembalian dana',
                TokoPaymentProgress::STATUS_REFUNDED => 'Dana telah dikembalikan'
            ];

            return [
                'status' => $status,
                'keterangan' => $statusDescriptions[$status] ?? 'Status diperbarui'
            ];
        });
    }
}
