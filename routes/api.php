<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AnalyticsDashboardController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\NotificationAPI;
use App\Http\Controllers\Api\Auth\UserProfileController;
use App\Http\Controllers\Api\Infaq\InfaqController;
use App\Http\Controllers\Api\KI\BarangApi;
use App\Http\Controllers\Api\KI\TransactionStatusController;
use App\Http\Controllers\Api\Toko\{
    TokoSellingController,
    KasirController,
    TokoDataController,
    PakDulController,
    CheckoutController,
    KeranjangTokoController,
    BiayaOperasionalController,
    LabaRugiController,
    TokoSalesReportController,
    TokoApiController,
    InvitationController,
    QuickShoppingController
};
use App\Http\Controllers\Api\Information\{
    InformationController,
    InformationCategoryController,
    InformationCommentController
};

use App\Http\Controllers\Api\Komunitas\{
    KomunitasPostController,
    KomunitasCommentController
};

// ==========================================
// Basic Authenticated User
// ==========================================

Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

Route::prefix('komunitas')->group(function () {

    // ============ POST ROUTES ============
    Route::prefix('posts')->group(function () {
        // Public routes
        Route::get('/', [KomunitasPostController::class, 'getPosts']);
        Route::get('/{id}', [KomunitasPostController::class, 'getPost']);

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [KomunitasPostController::class, 'createPost']);
            Route::put('/{id}', [KomunitasPostController::class, 'updatePost']);
            Route::delete('/{id}', [KomunitasPostController::class, 'deletePost']);
            Route::post('/{postId}/like', [KomunitasPostController::class, 'toggleLike']);

            // User's own posts
            Route::get('/my-posts', [KomunitasPostController::class, 'getMyPosts']);
            Route::get('/liked', [KomunitasPostController::class, 'getLikedPosts']);

            // Comments for post
            Route::get('/{postId}/comments', [KomunitasCommentController::class, 'getComments']);
            Route::post('/{postId}/comments', [KomunitasCommentController::class, 'createComment']);
        });
    });

    // ============ COMMENT ROUTES ============
    Route::prefix('comments')->group(function () {
        // Public routes
        Route::get('/{commentId}', [KomunitasCommentController::class, 'getComment']);
        Route::get('/{commentId}/replies', [KomunitasCommentController::class, 'getReplies']);
        Route::get('/user/{userId}', [KomunitasCommentController::class, 'getUserComments']);

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::put('/{commentId}', [KomunitasCommentController::class, 'updateComment']);
            Route::delete('/{commentId}', [KomunitasCommentController::class, 'deleteComment']);
            Route::post('/{commentId}/like', [KomunitasCommentController::class, 'toggleLike']);
            Route::get('/liked', [KomunitasCommentController::class, 'getLikedComments']);
        });
    });
});

Route::prefix('informations')->group(function () {
    // Public routes
    Route::get('/', [InformationController::class, 'index'])
        ->name('informations.index');

    Route::get('/search/advanced', [InformationController::class, 'advancedSearch'])
        ->name('informations.search.advanced');

    Route::get('/search/suggestions', [InformationController::class, 'searchSuggestions'])
        ->name('informations.search.suggestions');

    Route::get('/trending', [InformationController::class, 'trending'])
        ->name('informations.trending');

    Route::get('/{information}', [InformationController::class, 'show'])
        ->name('informations.show');

    Route::post('/{information}/share', [InformationController::class, 'incrementShareCount'])
        ->name('informations.share');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [InformationController::class, 'store'])
            ->name('informations.store');

        Route::put('/{information}', [InformationController::class, 'update'])
            ->name('informations.update');

        Route::delete('/{information}', [InformationController::class, 'destroy'])
            ->name('informations.destroy');

        Route::get('/my-informations', [InformationController::class, 'myInformations'])
            ->name('informations.my');
    });
});

Route::prefix('information-categories')->group(function () {
    Route::get('/', [InformationCategoryController::class, 'index'])
        ->name('categories.index');

    Route::get('/{category}', [InformationCategoryController::class, 'show'])
        ->name('categories.show');

    Route::get('/{category}/informations', [InformationCategoryController::class, 'informations'])
        ->name('categories.informations');
});

Route::prefix('informations/{information}/comments')->group(function () {
    // Public comment viewing
    Route::get('/', [InformationCommentController::class, 'index'])
        ->name('comments.index');

    Route::get('/{comment}/replies', [InformationCommentController::class, 'replies'])
        ->name('comments.replies');

    // Protected comment actions
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [InformationCommentController::class, 'store'])
            ->name('comments.store');

        Route::put('/{comment}', [InformationCommentController::class, 'update'])
            ->name('comments.update');

        Route::delete('/{comment}', [InformationCommentController::class, 'destroy'])
            ->name('comments.destroy');
    });
});




Route::middleware('auth:sanctum')->prefix('profile')->group(function () {

    Route::get('/', [UserProfileController::class, 'show']);
    Route::post('/', [UserProfileController::class, 'update']);
    Route::post('/password', [UserProfileController::class, 'updatePassword']);
    Route::get('/completion', [UserProfileController::class, 'checkCompletion']);

    Route::get('/roles-permissions', [UserProfileController::class, 'getRolesAndPermissions']);

    Route::get('/toko-info', [UserProfileController::class, 'getTokoInfo']);
    Route::get('/available-toko-jabatan', [UserProfileController::class, 'getAvailableTokoJabatan']);
});

// ==========================================
// Auth Routes
// ==========================================

Route::prefix('auth')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->middleware('throttle:5,1');
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:10,1');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('verify-phone', [AuthController::class, 'verifyPhone']);
        Route::post('resend-phone-verification', [AuthController::class, 'resendPhoneVerification']);
        Route::post('send-verification-code', [AuthController::class, 'sendVerificationCode']);
        Route::post('verify-device', [AuthController::class, 'verifyDevice']);
        Route::post('verify-recovery-code', [AuthController::class, 'verifyRecoveryCode']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::post('/forgot-password', [AuthController::class, 'requestPasswordReset'])
    ->middleware('throttle:5,1');
Route::post('/verify-password-reset-otp', [AuthController::class, 'verifyPasswordResetOtp'])
    ->middleware('throttle:5,1');
Route::post('/reset-password', [AuthController::class, 'resetPasswordWithOtp'])
    ->middleware('throttle:3,1');

// ==========================================
// Analytics - Requires Store Membership
// ==========================================

Route::prefix('analytics')->middleware(['auth:sanctum', 'verified.device.api', 'role:shop', 'toko:member'])->group(function () {
    Route::get('/', [AnalyticsDashboardController::class, 'apiIndex']);
    Route::get('/best-buyer', [AnalyticsDashboardController::class, 'getBestBuyer']);
    Route::get('/margin', [AnalyticsDashboardController::class, 'getMargin']);
    Route::get('/best-buy-and-sell', [AnalyticsDashboardController::class, 'getBestTokoBuyAndSell']);

    // Sales reports - Requires permission to view reports
    Route::get('/sales-report', [TokoSalesReportController::class, 'index'])
        ->middleware('toko:can_view_reports');

    Route::get('/best-master-data', [AnalyticsDashboardController::class, 'getBestMasterData']);
});

// ==========================================
// Toko - Public Access (Create Store)
// ==========================================

Route::prefix('toko')->middleware(['auth:sanctum', 'verified.device.api'])->group(function () {
    Route::post('/create', [TokoApiController::class, 'createToko'])->middleware('role_or_permission:toko.create');
});

// ==========================================
// KI (Kedai Indonesia) - Basic Store Access
// ==========================================

Route::prefix('ki')->middleware(['auth:sanctum', 'verified.device.api', 'role_or_permission:view.barang.ki'])->group(function () {
    Route::get('/barang', [BarangApi::class, 'viewBarang']);
    Route::get('/category', [BarangApi::class, 'category']);
});

// ==========================================
// Invitation Management - Consolidated Routes
// ==========================================

Route::prefix('invitation')->middleware(['auth:sanctum', 'verified.device.api'])->group(function () {
    // Accept/handle invitation - Requires guest permission
    Route::post('/handle/{id}', [InvitationController::class, 'handleInvitation'])
        ->middleware('role_or_permission:guest.accept.invite');

    // Send invitations - Requires can_invite permission
    Route::post('/send', [InvitationController::class, 'sendInvitation'])
        ->middleware('toko:can_invite');

    // Cancel invitations - Manager level or above
    Route::post('/cancel/{id}', [InvitationController::class, 'cancelInvitation'])
        ->middleware('toko:manager');

    // View invitation data - Manager level or above
    Route::get('/data', [InvitationController::class, 'data'])
        ->middleware('toko:manager');

    // Search users - Manager level or above
    Route::get('/search-user', [InvitationController::class, 'searchUser'])
        ->middleware('toko:manager');

    // Search jabatan data - Manager level or above
    Route::get('/search-jabatan', [InvitationController::class, 'searchJabatan'])
        ->middleware('toko:manager');
});

// ==========================================
// Store Shopping Cart - Supervisor Level Access
// ==========================================

Route::prefix('toko/keranjang')->middleware(['auth:sanctum', 'verified.device.api', 'role:shop', 'toko:can_manage_inventory'])->group(function () {
    Route::get('/', [KeranjangTokoController::class, 'viewKeranjang']);
    Route::post('/', [KeranjangTokoController::class, 'storeKeranjang']);
    Route::delete('/clear', [KeranjangTokoController::class, 'clearKeranjang']);
    Route::patch('/update', [KeranjangTokoController::class, 'updateKeranjang']);

    // Checkout requires inventory management permission
    Route::post('/checkout', [CheckoutController::class, 'processPayment']);
    Route::patch('/checkout/{transactionId}', [CheckoutController::class, 'updatePaymentStatus']);
    Route::post('/quick-shopping', [QuickShoppingController::class, 'getQuickShoppingList']);
    Route::post('/quick-shopping/add', [QuickShoppingController::class, 'addQuickShoppingItem']);
});

// ==========================================
// Store Selling - Cashier Level Access
// ==========================================

Route::prefix('toko/selling')->middleware(['auth:sanctum', 'verified.device.api', 'toko:can_manage_orders'])->group(function () {
    Route::post('/scan', [TokoSellingController::class, 'scanBarcode']);
    Route::post('/', [TokoSellingController::class, 'store']);
    Route::get('/data', [TokoDataController::class, 'tokoSelling']);
});

// Midtrans Notification (Webhook)
Route::post('/midtrans/notification', [CheckoutController::class, 'handleNotification']);

// routes/api.php
Route::middleware('auth:sanctum', 'verified.device.api')->group(function () {
    Route::prefix('infaq')->group(function () {
        Route::get('/list', [InfaqController::class, 'infaqList']);
        Route::post('/donate', [InfaqController::class, 'createDonation']);
        Route::get('/history', [InfaqController::class, 'donationHistory']);
        Route::patch('/donation/{id}/status', [InfaqController::class, 'updateDonationStatus']);
        Route::get('/statistics', [InfaqController::class, 'statistics']);
        Route::get('/donation/{id}', [InfaqController::class, 'donationDetail']);
    });
});

// ==========================================
// Store Data Management
// ==========================================

Route::prefix('toko/data')->middleware(['auth:sanctum', 'verified.device.api'])->group(function () {
    // Staff data - Manager level or above
    Route::get('/karyawan', [TokoDataController::class, 'tokoKaryawan'])
        ->middleware('toko:manager');

    Route::post('/karyawan-fire', [InvitationController::class, 'fireKaryawan'])
        ->middleware('toko:manager');

    Route::post('/karyawan-promote', [InvitationController::class, 'promoteKaryawan'])
        ->middleware('toko:manager');

    // Payment data - Staff level or above
    Route::get('/payment', [TokoDataController::class, 'tokoPayment'])
        ->middleware('toko:manager');

    Route::get('/payment/{transactionId}', [TokoDataController::class, 'tokoPaymentDetail'])
        ->middleware('toko:manager');

    Route::get('/barang', [TokoDataController::class, 'tokoBarang'])
        ->middleware('toko:can_manage_orders');

    Route::get('/conversion', [TokoDataController::class, 'conversion'])
        ->middleware('toko:can_manage_orders');
});

// ==========================================
// Pakdul (Payment/Credit System) - Staff Level
// ==========================================

Route::prefix('toko/pakdul')->middleware(['auth:sanctum', 'verified.device.api', 'role:shop', 'toko:can_manage_inventory'])->group(function () {

    // Account Management
    Route::get('/check-account', [PakDulController::class, 'checkAccount'])->name('pakdul.check-account');
    Route::post('/create-account', [PakDulController::class, 'createAccount'])->name('pakdul.create-account');
    Route::get('/data', [PakDulController::class, 'data'])->name('pakdul.data');

    // Eligibility Check
    Route::post('/check-eligibility', [PakDulController::class, 'checkEligibility'])->name('pakdul.check-eligibility');

    // Payment Management
    Route::post('/payment', [PakDulController::class, 'payment'])->name('pakdul.payment');
    Route::get('/payment-history', [PakDulController::class, 'getPaymentHistory'])->name('pakdul.payment-history');

    // Transaction Details
    Route::get('/transaction', [PakDulController::class, 'getTransaction'])->name('pakdul.transaction');
});


// ==========================================
// Transaction Status Management
// ==========================================

Route::prefix('transaction-status')->middleware(['auth:sanctum', 'verified.device.api'])->group(function () {
    Route::post('payment/bulk-action', [TransactionStatusController::class, 'bulkItemAction'])
        ->middleware('toko:can_manage_orders');
    Route::post('payment/batch-update', [TransactionStatusController::class, 'batchUpdatePaymentItems'])
        ->middleware('toko:can_manage_orders');
});

// ==========================================
// Store Management Routes
// ==========================================

Route::prefix('toko')->middleware(['auth:sanctum', 'verified.device.api', 'role:shop', 'toko:can_view_reports'])->group(function () {
    Route::get('/laporan-penjualan', [TokoSalesReportController::class, 'getLaporanPenjualan']);
    Route::get('/laporan-penjualan-optimized', [TokoSalesReportController::class, 'getLaporanPenjualanOptimized']);
    Route::get('/sales-data/chart', [TokoSalesReportController::class, 'getSalesData']);
});

// ==========================================
// Biaya Operasional (Operational Costs)
// ==========================================

Route::prefix('toko/biaya-operasional')->middleware(['auth:sanctum', 'verified.device.api', 'role:shop', 'toko:can_view_reports'])->group(function () {
    Route::get('/', [BiayaOperasionalController::class, 'index']);
    Route::post('/', [BiayaOperasionalController::class, 'store']);
    Route::get('/{id}', [BiayaOperasionalController::class, 'show']);
    Route::put('/{id}', [BiayaOperasionalController::class, 'update']);
    Route::delete('/{id}', [BiayaOperasionalController::class, 'destroy']);
});

// ==========================================
// Laporan Laba Rugi (Income Statement / Profit & Loss)
// ==========================================

Route::prefix('toko/laba-rugi')->middleware(['auth:sanctum', 'verified.device.api', 'role:shop', 'toko:can_view_reports'])->group(function () {
    Route::get('/', [LabaRugiController::class, 'index']);
    Route::get('/summary', [LabaRugiController::class, 'summary']);
});

// ==========================================
// Level-based Access Examples
// ==========================================

Route::prefix('toko/{toko}/admin')->middleware(['auth:sanctum', 'verified.device.api'])->group(function () {

    // Level 1+ (Staff and above)
    Route::get('/basic-data', [TokoApiController::class, 'basicData'])
        ->middleware('toko:level,1');

    // Level 2+ (Cashier and above)
    Route::get('/sales-data', [TokoApiController::class, 'salesData'])
        ->middleware('toko:level,2');

    // Level 3+ (Supervisor and above)
    Route::get('/inventory-reports', [TokoApiController::class, 'inventoryReports'])
        ->middleware('toko:level,3');

    // Level 4+ (Manager and above)
    Route::get('/financial-reports', [TokoApiController::class, 'financialReports'])
        ->middleware('toko:level,4');

    // Level 5 (Owner only)
    Route::get('/owner-dashboard', [TokoApiController::class, 'ownerDashboard'])
        ->middleware('toko:level,5');
});

// ==========================================
// Position-based Access Examples
// ==========================================

Route::prefix('toko/{toko}/position')->middleware(['auth:sanctum', 'verified.device.api'])->group(function () {

    // Specific position requirements
    Route::get('/manager-only', [TokoApiController::class, 'managerOnly'])
        ->middleware('toko:jabatan,manager');

    Route::get('/supervisor-only', [TokoApiController::class, 'supervisorOnly'])
        ->middleware('toko:jabatan,supervisor');

    Route::get('/kasir-only', [TokoApiController::class, 'kasirOnly'])
        ->middleware('toko:jabatan,kasir');
});

// ==========================================
// Notifications
// ==========================================

Route::prefix('notifications')->middleware(['auth:sanctum', 'verified.device.api'])->group(function () {
    Route::get('/', [NotificationAPI::class, 'notificationView']);
    Route::get('/unread-count', [NotificationAPI::class, 'getUnreadCount']);
    Route::post('/test', [NotificationAPI::class, 'sendTestNotification']);
    Route::put('/read-all', [NotificationAPI::class, 'markAllAsRead']);
    Route::put('/{id}/read', [NotificationAPI::class, 'markAsRead']);
    Route::put('/{id}/clicked', [NotificationAPI::class, 'markAsClicked']);
});

// ==========================================
// Shift Management (Kasir & Owner)
// ==========================================
Route::prefix('toko/shift')->middleware(['auth:sanctum', 'verified.device.api', 'role:kasir', 'throttle:10,1'])->group(function () {
    Route::post('open', [KasirController::class, 'openShift']);
    Route::post('close', [KasirController::class, 'closeShift']);
    Route::get('active', [KasirController::class, 'activeShift']);
    Route::get('latest-closing', [KasirController::class, 'latestClosing']);
    Route::get('history', [KasirController::class, 'shiftHistory']);
});

// ==========================================
// Kasir Management (Owner Only)
// ==========================================
Route::prefix('toko/kasir')->middleware(['auth:sanctum', 'verified.device.api', 'role:shop'])->group(function () {
    Route::post('create', [KasirController::class, 'createKasir']);
    Route::get('list', [KasirController::class, 'listKasir']);
});

// ==========================================
// Utilities / Dropdown Routes
// ==========================================

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/barang/list', fn() => response()->json([
        'success' => true,
        'data' => \App\Models\BarangModel::select('id', 'name')->get()
    ]))->name('api.barang.list');

    Route::get('/satuan/list', fn() => response()->json([
        'success' => true,
        'data' => \App\Models\SatuanItem::select('id', 'name')->get()
    ]))->name('api.satuan.list');
});
