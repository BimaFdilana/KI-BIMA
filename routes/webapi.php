<?php
// routes/webapi.php - Web API Routes dengan prefix 'webapi'

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminApprovalController;

Route::get('/test', function () {
    return response()->json(['message' => 'Ini dari webapi']);
});

// ============ APPROVAL SYSTEM ROUTES ============

Route::middleware(['auth:api'])->group(function () {

    // -------- TOKO VERIFICATION --------
    Route::prefix('approvals/toko')->group(function () {
        // Get pending toko registrations
        Route::get('/pending', [AdminApprovalController::class, 'getPendingToko'])
            ->name('api.approvals.toko.pending')
            ->middleware('can:approve-toko');

        // Get toko detail
        Route::get('/{tokoId}', [AdminApprovalController::class, 'getTokoDetail'])
            ->name('api.approvals.toko.detail')
            ->middleware('can:view-toko');

        // Approve toko
        Route::post('/{tokoId}/approve', [AdminApprovalController::class, 'approveToko'])
            ->name('api.approvals.toko.approve')
            ->middleware('can:approve-toko');

        // Reject toko
        Route::post('/{tokoId}/reject', [AdminApprovalController::class, 'rejectToko'])
            ->name('api.approvals.toko.reject')
            ->middleware('can:reject-toko');
    });

    // -------- PAYMENT VERIFICATION --------
    Route::prefix('approvals/payments')->group(function () {
        // Get pending payments
        Route::get('/pending', [AdminApprovalController::class, 'getPendingPayments'])
            ->name('api.approvals.payments.pending')
            ->middleware('can:verify-payment');

        // Confirm/Verify payment
        Route::post('/{paymentId}/confirm', [AdminApprovalController::class, 'confirmPayment'])
            ->name('api.approvals.payments.confirm')
            ->middleware('can:confirm-payment');

        // Reject payment
        Route::post('/{paymentId}/reject', [AdminApprovalController::class, 'rejectPayment'])
            ->name('api.approvals.payments.reject')
            ->middleware('can:reject-payment');
    });

    // -------- DASHBOARD SUMMARY --------
    Route::get('/approvals/summary', [AdminApprovalController::class, 'getApprovalSummary'])
        ->name('api.approvals.summary')
        ->middleware('can:view-approvals');
});
