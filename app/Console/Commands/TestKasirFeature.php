<?php

namespace App\Console\Commands;

use App\Models\Auth\UserModel;
use App\Models\Toko\KasirProfile;
use App\Models\Toko\KasirShift;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoUserModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestKasirFeature extends Command
{
    protected $signature = 'test:kasir {--user-id=1}';
    protected $description = 'Test the kasir feature flow';

    public function handle()
    {
        $userId = $this->option('user-id');
        $user = UserModel::find($userId);

        if (!$user) {
            $this->error("User with ID $userId not found");
            return 1;
        }

        $this->info("Testing Kasir Feature for user: {$user->name}");
        $this->line('');

        // 1. Check if user has kasir role
        $this->info('✓ Checking kasir role...');
        if (!$user->hasRole('kasir')) {
            $this->warn('User does not have kasir role');
        } else {
            $this->line('✓ User has kasir role');
        }

        // 2. Check toko assignment
        $this->info('✓ Checking toko assignment...');
        $tokoUser = TokoUserModel::where('user_id', $userId)->first();
        if (!$tokoUser) {
            $this->error('User is not assigned to any toko');
            return 1;
        }
        $this->line("✓ User is assigned to toko ID: {$tokoUser->toko_id}");

        // 3. Check kasir profile
        $this->info('✓ Checking kasir profile...');
        $profile = KasirProfile::where('user_id', $userId)->where('toko_id', $tokoUser->toko_id)->first();
        if (!$profile) {
            $this->error('Kasir profile not found');
        } else {
            $this->line('✓ Kasir profile exists');
        }

        // 4. Check active shift
        $this->info('✓ Checking active shift...');
        $activeShift = KasirShift::where('kasir_id', $userId)
            ->where('toko_id', $tokoUser->toko_id)
            ->whereNull('closed_at')
            ->first();

        if ($activeShift) {
            $this->line("✓ Active shift found: ID {$activeShift->id}");
            $this->line("  - Opened at: {$activeShift->opened_at}");
            $this->line("  - Shift awal: Rp " . number_format($activeShift->shift_awal, 0, ',', '.'));
            $this->line("  - Total transaksi tunai: Rp " . number_format($activeShift->total_transaksi_tunai, 0, ',', '.'));
        } else {
            $this->warn('No active shift found');

            // 5. List recent shifts
            $this->info('✓ Recent shifts:');
            $shifts = KasirShift::where('kasir_id', $userId)
                ->where('toko_id', $tokoUser->toko_id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            if ($shifts->isEmpty()) {
                $this->line('  No shifts found');
            } else {
                foreach ($shifts as $shift) {
                    $status = $shift->closed_at ? 'CLOSED' : 'OPEN';
                    $this->line("  - ID {$shift->id}: $status (opened: {$shift->opened_at})");
                    if ($shift->closed_at) {
                        $this->line("    Balance: Rp " . number_format($shift->shift_balance ?? 0, 0, ',', '.'));
                        $this->line("    Discrepancy: {$shift->discrepancy_status}");
                    }
                }
            }
        }

        $this->line('');
        $this->info('✓ Kasir feature test completed');

        return 0;
    }
}
