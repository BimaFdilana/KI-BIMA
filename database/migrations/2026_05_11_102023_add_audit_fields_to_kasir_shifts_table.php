<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('kasir_shifts', function (Blueprint $table) {
            $table->decimal('discrepancy_amount', 15, 2)->nullable()->after('shift_balance');
            $table->enum('discrepancy_status', ['pending_verification', 'verified', 'adjusted'])->nullable()->after('discrepancy_amount');
            $table->foreignId('verified_by')->nullable()->constrained('users')->after('discrepancy_status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_notes')->nullable()->after('verified_at');
        });
    }

    public function down(): void {
        Schema::table('kasir_shifts', function (Blueprint $table) {
            // Drop foreign key first if it exists
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['verified_by']);
            }
            $table->dropColumn(['discrepancy_amount', 'discrepancy_status', 'verified_by', 'verified_at', 'verification_notes']);
        });
    }
};
