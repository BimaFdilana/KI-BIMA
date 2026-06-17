<?php

use App\Http\Controllers\Admin\{
    AdminApprovalController,
    DashboardController,
    AnalyticsDashboardController,
    PaymentTokoController,
};
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Barang\{
    BarangController,
    BarangKIController,
    BarangTokoController,
    CategoryController,
    MasterController,
    SatuanItemController,
    SubcategoryController,
    BrandController,
    TypeItemController,
};
use App\Models\Artikel;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\FonnteController;
use App\Http\Controllers\Infaq\InfaqDataTableController;
use App\Http\Controllers\PayLatter\PayLatterDataTableController;
use App\Http\Controllers\InfaqManagementController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Toko\TokoController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified.device', 'role:programmer|admin|founder|accounting|operator', 'verified.phone'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('role_or_permission:access.dashboard');
        Route::get('/analytics', [AnalyticsDashboardController::class, 'index'])->name('dashboard.analytics')->middleware('role_or_permission:access.dashboard');
        Route::get('/advanced-analytics', function () {
            return view('analytics.dashboard');
        })->name('dashboard.advanced-analytics')->middleware('role_or_permission:access.analytics');
        Route::get('/approval', [AdminApprovalController::class, 'index'])->name('dashboard.approval')->middleware('role_or_permission:access.analytics');

        Route::get('/api/analytics', [AnalyticsDashboardController::class, 'apiIndex']);
        Route::get('/api/best-buy-and-sell', [AnalyticsDashboardController::class, 'getBestTokoBuyAndSell']);
        Route::get('/api/overview', [DashboardController::class, 'apiOverview']);
    });

    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index')->middleware('role_or_permission:view.barang');
        Route::post('store', [BarangController::class, 'store'])->name('store')->middleware('role_or_permission:create.barang');
        Route::get('show/{barang}', [BarangController::class, 'show'])->name('show')->middleware('role_or_permission:view.barang');
        Route::get('show-barang/{sku}', [BarangController::class, 'show_barang'])->name('show-barang')->middleware('role_or_permission:view.barang');
        Route::put('update/{id}', [BarangController::class, 'update'])->name('update')->middleware('role_or_permission:edit.barang');
        Route::post('destroy/{barang}', [BarangController::class, 'destroy'])->name('destroy')->middleware('role_or_permission:delete.barang');

        Route::get('getsku', [BarangController::class, 'getsku'])->name('getsku');
        Route::get('tambah-barang', [BarangController::class, 'tambah_barang'])->name('tambah-barang')->middleware('role_or_permission:create.barang');
        Route::get('edit-barang/{sku}', [BarangController::class, 'edit_barang'])->name('edit-barang')->middleware('role_or_permission:edit.barang');

        Route::post('import', [BarangController::class, 'import'])->name('import')->middleware('role_or_permission:import.barang');
        Route::post('template', [BarangController::class, 'downloadTemplate'])->name('template')->middleware('role_or_permission:import.barang');
        Route::get('/import-status/{jobId}', [BarangController::class, 'checkImportStatus'])->name('import-status');

        Route::get('export', [BarangController::class, 'export'])->name('export')->middleware('role_or_permission:export.barang');
        Route::get('export-pdf', [BarangController::class, 'exportPdf'])->name('export-pdf')->middleware('role_or_permission:export.barang');
        Route::get('get-satuan-by-type', [BarangController::class, 'getSatuanByType'])->name('get-satuan-by-type')->middleware('role_or_permission:view.barang');


        Route::prefix('toko')->name('toko.')->group(function () {
            Route::get('/', [BarangTokoController::class, 'index'])->name('index')->middleware('role_or_permission:view.barang.toko');
            Route::get('penjualan', [BarangTokoController::class, 'penjualanToko'])
                ->name('penjualan')
                ->middleware('role_or_permission:view.barang.toko');
            Route::post('export/{format}', [BarangTokoController::class, 'export'])->name('export')->middleware('role_or_permission:export.barang.toko');
        });

        Route::prefix('ki')->name('ki.')->middleware('role_or_permission:view.barang.ki')->group(function () {
            Route::get('/', [BarangKIController::class, 'index'])->name('index');
            Route::get('data', [BarangKIController::class, 'getBarangKI'])->name('data')->middleware('role_or_permission:view.barang.ki');
            Route::get('get-data-from-same-expired', [BarangKIController::class, 'getDataFromSameExpired'])->name('get-data-from-same-expired')->middleware('role_or_permission:view.barang.ki');
            Route::get('detail/{barcode}', [BarangKIController::class, 'detail'])->name('detail')->middleware('role_or_permission:view.barang.ki');
            Route::get('tambah-barang', [BarangKIController::class, 'tambah_barang'])->name('tambah-barang')->middleware('role_or_permission:create.barang.ki');
            Route::post('find-barcode', [BarangKIController::class, 'findBarcode'])->name('find-barcode')->middleware('role_or_permission:create.barang.ki');

            Route::post('get-barang-same-id', [BarangKIController::class, 'getBarangSameId'])->name('get-barang-same-id')->middleware('role_or_permission:view.barang.ki');
            Route::post('get-satuan-convert-barang', [BarangKIController::class, 'getSatuanConvertBarang'])->name('get-satuan-convert-barang')->middleware('role_or_permission:view.barang.ki');

            Route::post('export/{format}', [BarangKIController::class, 'export'])->name('export')->middleware('role_or_permission:export.barang.ki');
            Route::post('store', [BarangKIController::class, 'store'])->name('store')->middleware('role_or_permission:create.barang.ki');

            Route::get('/download-template', [BarangKiController::class, 'downloadTemplate'])->name('download-template');
            Route::post('/import', [BarangKiController::class, 'import'])->name('import');
            Route::post('/preview', [BarangKiController::class, 'preview'])->name('preview');
            Route::get('/import-status', [BarangKiController::class, 'checkImportStatus'])->name('import-status');


            Route::post('add-stock', [BarangKIController::class, 'addStock'])->name('add-stock')->middleware('role_or_permission:edit.barang.ki');
            Route::put('update/{barangki}', [BarangKIController::class, 'update'])->name('update')->middleware('role_or_permission:edit.barang.ki');
            Route::post('destroy/{barangki}', [BarangKIController::class, 'destroy'])->name('destroy')->middleware('role_or_permission:delete.barang.ki');
            Route::post('/batch', [BarangKIController::class, 'batchAction'])->name('batch')->middleware('role_or_permission:manage.barang.ki');
        });

        Route::prefix('master')->name('master.')->middleware('role_or_permission:view.barang.master')->group(function () {
            Route::get('/', [MasterController::class, 'index'])->name('index');
            Route::get('/categories-data', [MasterController::class, 'getCategories'])->name('categories')->middleware('role_or_permission:manage.barang.master');
            Route::get('/satuan-items-data', [MasterController::class, 'getSatuanItems'])->name('satuanItems')->middleware('role_or_permission:manage.barang.master');
            Route::get('/brands-data', [MasterController::class, 'getBrands'])->name('brands')->middleware('role_or_permission:manage.barang.master');
            Route::get('/type-items-data', [MasterController::class, 'getTypeItems'])->name('typeItems')->middleware('role_or_permission:manage.barang.master');
            Route::get('/sub-categories-data', [MasterController::class, 'getSubCategories'])->name('subCategories')->middleware('role_or_permission:manage.barang.master');
            Route::post('/update-satuan-item', [MasterController::class, 'updateSatuanItem'])->name('updateSatuanItem')->middleware('role_or_permission:manage.barang.master');
        });
    });
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('role_or_permission:view.users');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/detail/{username}', [UserController::class, 'detailIndex'])->name('detail')->middleware('role_or_permission:view.users');
        Route::post('check-username', [UserController::class, 'checkUsername'])->name('check-username')->middleware('role_or_permission:view.users');
        Route::post('check-phone-number', [UserController::class, 'checkPhoneNumber'])->name('check-phone')->middleware('role_or_permission:view.users');
        Route::post('reset-password/{username}', [UserController::class, 'resetPassword'])->name('reset-password')->middleware('role_or_permission:edit.users');
        Route::post('suspend/{username}', [UserController::class, 'suspend'])->name('suspend')->middleware('role_or_permission:edit.users');
        Route::post('delete/{username}', [UserController::class, 'delete'])->name('delete')->middleware('role_or_permission:edit.users');
        Route::get('{userId}/role-permission-data', [UserController::class, 'getRolePermissionData'])->name('role-permission-data');
        Route::post('{userId}/update-role-permission', [UserController::class, 'updateRolePermission'])->name('update-role-permission')->middleware('role_or_permission:edit.users');
        Route::post('tokosearch', [UserController::class, 'tokosearch'])->name('tokosearch');
        Route::post('permissionsearch', [UserController::class, 'searchPermission'])->name('permissionsearch');

        Route::put('update/{user}', [UserController::class, 'update'])->name('update')->middleware('role_or_permission:edit.users');
        Route::put('update-api/{username}', [UserController::class, 'updateApi'])->name('update-api')->middleware('role_or_permission:edit.users');

        Route::get('show/{username}', [UserController::class, 'show'])->name('show')->middleware('role_or_permission:view.users');
    });

    Route::prefix('master/category')->name('master.category.')->group(function () {
        Route::post('store', [CategoryController::class, 'store'])->name('store')->middleware('role_or_permission:create.barang.master');
        Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit')->middleware('role_or_permission:edit.barang.master');
        Route::put('update/{id}', [CategoryController::class, 'update'])->name('update')->middleware('role_or_permission:edit.barang.master');
        Route::delete('destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy')->middleware('role_or_permission:delete.barang.master');
    });

    Route::prefix('master/subcategory')->name('master.subcategory.')->group(function () {
        Route::post('store', [SubcategoryController::class, 'store'])->name('store')->middleware('role_or_permission:create.barang.master');
        Route::get('edit/{id}', [SubcategoryController::class, 'edit'])->name('edit')->middleware('role_or_permission:edit.barang.master');
        Route::put('update/{id}', [SubcategoryController::class, 'update'])->name('update')->middleware('role_or_permission:edit.barang.master');
        Route::delete('destroy/{id}', [SubcategoryController::class, 'destroy'])->name('destroy')->middleware('role_or_permission:delete.barang.master');
    });

    Route::prefix('master/satuan-item')->name('master.satuan-item.')->group(function () {
        Route::post('store', [SatuanItemController::class, 'store'])->name('store')->middleware('role_or_permission:create.barang.master');
        Route::get('edit/{id}', [SatuanItemController::class, 'edit'])->name('edit')->middleware('role_or_permission:edit.barang.master');
        Route::put('update/{id}', [SatuanItemController::class, 'update'])->name('update')->middleware('role_or_permission:edit.barang.master');
        Route::delete('destroy/{id}', [SatuanItemController::class, 'destroy'])->name('destroy')->middleware('role_or_permission:delete.barang.master');
    });

    Route::prefix('master/brand')->name('master.brand.')->group(function () {
        Route::post('store', [BrandController::class, 'store'])->name('store')->middleware('role_or_permission:create.barang.master');
        Route::get('edit/{id}', [BrandController::class, 'edit'])->name('edit')->middleware('role_or_permission:edit.barang.master');
        Route::put('update/{id}', [BrandController::class, 'update'])->name('update')->middleware('role_or_permission:edit.barang.master');
        Route::delete('destroy/{id}', [BrandController::class, 'destroy'])->name('destroy')->middleware('role_or_permission:delete.barang.master');
    });

    Route::prefix('master/type-item')->name('master.type-item.')->group(function () {
        Route::post('store', [TypeItemController::class, 'store'])->name('store')->middleware('role_or_permission:create.barang.master');
        Route::get('edit/{id}', [TypeItemController::class, 'edit'])->name('edit')->middleware('role_or_permission:edit.barang.master');
        Route::put('update/{id}', [TypeItemController::class, 'update'])->name('update')->middleware('role_or_permission:edit.barang.master');
        Route::delete('destroy/{id}', [TypeItemController::class, 'destroy'])->name('destroy')->middleware('role_or_permission:delete.barang.master');
    });

    // web.php

    Route::prefix('toko')->name('toko.')->group(function () {
        Route::get('/', [TokoController::class, 'index'])->name('index');
        Route::post('/store', [TokoController::class, 'store'])->name('store');

        Route::post('/check-user-toko', [TokoController::class, 'checkUserToko'])->name('check-user-toko');
        Route::get('/payment', [PaymentTokoController::class, 'index'])->name('payment.index')->middleware('role_or_permission:view.payments');
        Route::get('/payment/load-more', [PaymentTokoController::class, 'loadMore'])->name('payment.load-more')->middleware('role_or_permission:view.payments');
        Route::get('/payment/show/{pesanan}', [PaymentTokoController::class, 'show'])->name('payment.show')->middleware('role_or_permission:view.payments');

        Route::get('/{tokoId}/users-wost', [TokoController::class, 'getUserWithoutOrSameToko'])->name('get-users-wost');
        Route::get('/users-wot', [TokoController::class, 'getUserWithoutToko'])->name('get-users-wot');

        Route::post('/{id}/employees', [TokoController::class, 'addEmployee'])->name('employees.add');
        Route::delete('/{id}/employees', [TokoController::class, 'removeEmployee'])->name('employees.remove');
        Route::patch('/{id}/employees/{userId}/jabatan', [TokoController::class, 'updateEmployeeJabatan'])->name('employees.update-jabatan');

        Route::put('/{id}/products/{barangId}', [TokoController::class, 'updateProduct'])->name('products.update');
        Route::delete('/{id}/products/{barangId}', [TokoController::class, 'deleteProduct'])->name('products.delete');

        Route::get('/{id}/edit', [TokoController::class, 'edit'])->name('edit');
        Route::get('/{id}', [TokoController::class, 'show'])->name('show');
        Route::put('/{id}', [TokoController::class, 'update'])->name('update');
        Route::delete('/{id}', [TokoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::patch('/{id}/click', [NotificationController::class, 'markAsClicked'])->name('notifications.click');
        Route::patch('/{id}/download', [NotificationController::class, 'markAsDownloaded'])->name('notifications.download');
        Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Settings
        Route::post('/settings', [NotificationController::class, 'updateSettings'])->name('notifications.settings');

        // Subscriptions
        Route::post('/subscribe', [NotificationController::class, 'subscribe'])->name('notifications.subscribe');
        Route::post('/unsubscribe', [NotificationController::class, 'unsubscribe'])->name('notifications.unsubscribe');
    });

    Route::prefix('file-manager')->group(function () {
        Route::get('/', [FileManagerController::class, 'index'])->name('file-manager.index');
        Route::post('/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
        Route::post('/create-folder', [FileManagerController::class, 'createFolder'])->name('file-manager.create-folder');
        Route::post('/replace', [FileManagerController::class, 'replace'])->name('file-manager.replace');
        Route::delete('/delete', [FileManagerController::class, 'delete'])->name('file-manager.delete');
        Route::get('/download', [FileManagerController::class, 'download'])->name('file-manager.download');
    });

    // Debug Routes
    Route::prefix('debug')->middleware('role:programmer')->group(function () {
        Route::get('/notification-templates', function () {
            return view('admin.debug.notification-templates');
        })->name('debug.notification-templates');
    });
});



Route::get('/{any?}', [PageController::class, 'home'])
    ->name('home')
    ->where('any', '^(beranda|home)?$');

// Device Management Routes
Route::group(['prefix' => 'fonnte', 'as' => 'fonnte.'], function () {
    Route::get('/', [FonnteController::class, 'index'])->name('dashboard');
    Route::post('/add-device', [FonnteController::class, 'addDevice'])->name('add-device');
    Route::post('/request-qr', [FonnteController::class, 'requestQR'])->name('request-qr');
    Route::post('/disconnect-device', [FonnteController::class, 'disconnectDevice'])->name('disconnect-device');
    Route::post('/request-delete-otp', [FonnteController::class, 'requestDeleteOTP'])->name('request-delete-otp');
    Route::post('/delete-device', [FonnteController::class, 'deleteDevice'])->name('delete-device');
    Route::post('/send-message', [FonnteController::class, 'sendMessage'])->name('send-message');
    Route::post('/check-connection', [FonnteController::class, 'checkConnection'])->name('check-connection');
});

// Infaq Routes - Protected by auth middleware
Route::middleware(['auth', 'verified.device', 'role:programmer|admin|founder|accounting|operator', 'verified.phone'])
    ->prefix('infaq')
    ->name('infaq.')
    ->group(function () {
        // Infaq Dashboard
        Route::get('/', [InfaqManagementController::class, 'index'])
            ->name('dashboard')
            ->middleware('role_or_permission:view.infaq');

        Route::get('/{infaqId}/donations', [InfaqManagementController::class, 'getInfaqDonationHistory'])
            ->middleware('role_or_permission:view.infaq');

        // Infaq List Routes
        Route::prefix('list')->name('list.')->group(function () {
            Route::get('/', [InfaqDataTableController::class, 'infaqListIndex'])
                ->name('index')
                ->middleware('role_or_permission:view.infaq');

            Route::get('/create', [InfaqDataTableController::class, 'createInfaqList'])
                ->name('create')
                ->middleware('role_or_permission:create.infaq');

            Route::post('/', [InfaqDataTableController::class, 'storeInfaqList'])
                ->name('store')
                ->middleware('role_or_permission:create.infaq');

            Route::get('/{infaqList}', [InfaqDataTableController::class, 'showInfaqList'])
                ->name('show')
                ->middleware('role_or_permission:view.infaq');

            Route::get('/{infaqList}/edit', [InfaqDataTableController::class, 'editInfaqList'])
                ->name('edit')
                ->middleware('role_or_permission:edit.infaq');

            Route::put('/{infaqList}', [InfaqDataTableController::class, 'updateInfaqList'])
                ->name('update')
                ->middleware('role_or_permission:edit.infaq');

            Route::delete('/{infaqList}', [InfaqDataTableController::class, 'destroyInfaqList'])
                ->name('destroy')
                ->middleware('role_or_permission:delete.infaq');
        });

        // Infaq History Routes
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('/', [InfaqDataTableController::class, 'infaqHistoryIndex'])
                ->name('index')
                ->middleware('role_or_permission:view.infaq');

            Route::get('/{infaqHistory}', [InfaqDataTableController::class, 'showInfaqHistory'])
                ->name('show')
                ->middleware('role_or_permission:view.infaq');

            Route::get('/{infaqHistory}/edit', [InfaqDataTableController::class, 'editInfaqHistory'])
                ->name('edit')
                ->middleware('role_or_permission:edit.infaq');

            Route::put('/{infaqHistory}', [InfaqDataTableController::class, 'updateInfaqHistory'])
                ->name('update')
                ->middleware('role_or_permission:edit.infaq');

            Route::delete('/{infaqHistory}', [InfaqDataTableController::class, 'destroyInfaqHistory'])
                ->name('destroy')
                ->middleware('role_or_permission:delete.infaq');
        });

        // Infaq Image Routes
        Route::prefix('image')->name('image.')->group(function () {
            Route::get('/', [InfaqDataTableController::class, 'infaqImageIndex'])
                ->name('index')
                ->middleware('role_or_permission:view.infaq');

            Route::get('/create', [InfaqDataTableController::class, 'createInfaqImage'])
                ->name('create')
                ->middleware('role_or_permission:create.infaq');

            Route::post('/', [InfaqDataTableController::class, 'storeInfaqImage'])
                ->name('store')
                ->middleware('role_or_permission:create.infaq');

            Route::get('/{infaqImage}/edit', [InfaqDataTableController::class, 'editInfaqImage'])
                ->name('edit')
                ->middleware('role_or_permission:edit.infaq');

            Route::put('/{infaqImage}', [InfaqDataTableController::class, 'updateInfaqImage'])
                ->name('update')
                ->middleware('role_or_permission:edit.infaq');

            Route::delete('/{infaqImage}', [InfaqDataTableController::class, 'destroyInfaqImage'])
                ->name('destroy')
                ->middleware('role_or_permission:delete.infaq');
        });
    });

// PayLatter Routes - Protected by auth middleware
Route::middleware(['auth', 'verified.device', 'role:programmer|admin|founder|accounting|operator', 'verified.phone'])
    ->prefix('paylatter')
    ->name('paylatter.')
    ->group(function () {

        // PayLatter Account Routes
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/', [PayLatterDataTableController::class, 'accountIndex'])
                ->name('index')
                ->middleware('role_or_permission:view.paylatter');

            Route::get('/{payLatterAccount}', [PayLatterDataTableController::class, 'showAccount'])
                ->name('show')
                ->middleware('role_or_permission:view.paylatter');

            Route::get('/{payLatterAccount}/edit', [PayLatterDataTableController::class, 'editAccount'])
                ->name('edit')
                ->middleware('role_or_permission:edit.paylatter');

            Route::put('/{payLatterAccount}', [PayLatterDataTableController::class, 'updateAccount'])
                ->name('update')
                ->middleware('role_or_permission:edit.paylatter');
        });

        // PayLatter Transaction Routes
        Route::prefix('transaction')->name('transaction.')->group(function () {
            Route::get('/', [PayLatterDataTableController::class, 'transactionIndex'])
                ->name('index')
                ->middleware('role_or_permission:view.paylatter');

            Route::get('/{payLatterTransaction}', [PayLatterDataTableController::class, 'showTransaction'])
                ->name('show')
                ->middleware('role_or_permission:view.paylatter');

            Route::get('/{payLatterTransaction}/edit', [PayLatterDataTableController::class, 'editTransaction'])
                ->name('edit')
                ->middleware('role_or_permission:edit.paylatter');

            Route::put('/{payLatterTransaction}', [PayLatterDataTableController::class, 'updateTransaction'])
                ->name('update')
                ->middleware('role_or_permission:edit.paylatter');
        });
    });

// Additional routes for AJAX requests
Route::post('/ajax/send-message', [MessageController::class, 'sendMessage'])->name('ajax.send.message');

route::get('/home2', function () {
    return view('guest.home2');
});

route::get('/produk', function () {
    return view('guest.produk');
})->name('guest.produk');

Route::get('/belanja', function () {
    return view('guest.belanja');
})->name('belanja');

// Tambahkan ini di routes/web.php
Route::get('/layanan/jualan-detail', function () {
    return view('guest.jualan_detail'); // Mengarah ke file blade baru yang akan kita buat
})->name('jualan.detail');

// Tambahkan ini di routes/web.php
Route::get('/layanan/laporan-detail', function () {
    return view('guest.laporan_detail'); // Mengarah ke folder guest
})->name('laporan.detail');

// Tambahkan ini di routes/web.php
Route::get('/layanan/analisis-detail', function () {
    return view('guest.analisis_detail'); // Mengarah ke folder guest
})->name('analisis.detail');

// Tambahkan ini di routes/web.php
Route::get('/layanan/komunitas-detail', function () {
    return view('guest.komunitas_detail'); // Mengarah ke folder guest
})->name('komunitas.detail');

// Tambahkan ini di routes/web.php
Route::get('/layanan/informasi-detail', function () {
    return view('guest.informasi_detail'); // Mengarah ke folder guest
})->name('informasi.detail');

// Information Routes
Route::get('/information', function () {
    return view('guest.information.index');
})->name('information.index');

Route::get('/informations/{id}', [WebController::class, 'show'])->name('web.informations.show');

Route::middleware('web')->prefix('webapi')->group(base_path('routes/webapi.php'));
require __DIR__ . '/auth.php';
__DIR__ . '/auth.php';


Route::get('/hubungi-kami', [PageController::class, 'hubungiKami'])->name('hubungi.kami');
Route::get('/artikel', [PageController::class, 'artikel'])->name('artikel');

use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\PesanController;

Route::prefix('admin')->group(function () {

    Route::resource('artikel', ArtikelController::class)->except(['show']);

    Route::get('/pesan', [PesanController::class, 'index'])
        ->name('pesan.index');

});

Route::get('/artikel', [PageController::class, 'artikel'])->name('artikel');

Route::get('/artikel/{slug}', [PageController::class, 'show'])->name('artikel.show');

Route::get('/hubungi-kami', [PageController::class, 'hubungiKami'])->name('hubungi-kami');
Route::post('/kirim.pesan', [PageController::class, 'kirimPesan'])
    ->name('kirim.pesan');

    // FAQ
Route::view('/faq', 'faq')->name('faq');

use App\Http\Controllers\Admin\PerlengkapanFisikController;
use App\Http\Controllers\Admin\PlugInController;

Route::resource(
    'admin/perlengkapan-fisik',
    PerlengkapanFisikController::class
);

Route::resource(
    'admin/plug-in',
    PlugInController::class
);
Route::get('/produk', [WebController::class, 'produk'])
    ->name('produk');

Route::get(
    '/perlengkapan/{id}',
    [WebController::class, 'detailPerlengkapan']
)->name('perlengkapan.detail');

Route::get(
    '/plugin/{id}',
    [WebController::class, 'detailPlugin']
)->name('plugin.detail');

Route::resource('perlengkapan', PerlengkapanFisikController::class);


//detile artikel
Route::get('/artikel/{id}', [ArtikelController::class, 'show'])
    ->name('artikel.show');

Route::get('/artikel/{slug}', [ArtikelController::class, 'show'])
    ->name('artikel.show');
    
Route::get('/admin/pesan', [PesanController::class, 'index'])
    ->name('pesan.index');

Route::put('/admin/pesan/{id}/read', [PesanController::class, 'markAsRead'])
    ->name('pesan.read');

Route::put('/admin/pesan/{id}/read', [PesanController::class, 'markAsRead'])
    ->name('pesan.read');

Route::get('/admin/pesan', [PesanController::class, 'index'])
    ->name('pesan.index');

Route::put('/admin/pesan/{id}/read', [PesanController::class, 'markAsRead'])
    ->name('pesan.read');


    // produk
use App\Http\Controllers\Admin\ProductController;

Route::prefix('admin')->group(function () {

    Route::resource('product', ProductController::class);

});

// new 
// =============================
// PRODUCT
// =============================

Route::prefix('admin')->group(function () {

    Route::resource('product', ProductController::class);

});

Route::get('/produk', [WebController::class, 'produk'])
    ->name('produk');

Route::get(
    '/produk/{id}',
    [WebController::class, 'detailProduct']
)->name('produk.detail');

use App\Http\Controllers\Admin\EbookController;

Route::resource(
    'admin/ebook',
    EbookController::class
);

Route::get(
    '/ebook/{id}',
    [WebController::class, 'detailEbook']
)->name('ebook.detail');


Route::get('/faq', [WebController::class, 'faq'])
    ->name('faq');

use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LaporanController;

Route::prefix('admin')->group(function () {

    Route::resource('faq', FaqController::class);

    Route::resource('laporan', LaporanController::class);

});