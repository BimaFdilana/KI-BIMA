<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Toko\JabatanModel;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $founderPermissions = [
            'remove.money',
            'add.money',
            'accept.changemoney',
            'reject.changemoney',
            'view.changemoney',
            'create.changemoney',
            'edit.changemoney',
            'delete.changemoney',
        ];


        // Create system-wide permissions
        $dashboardPermissions = [
            'access.dashboard',
            'access.reports',
            'access.analytics',

            'view.users',
            'create.users',
            'edit.users',
            'delete.users',

            'view.roles',
            'create.roles',
            'edit.roles',
            'delete.roles',
            'view.permissions',
            'create.permissions',
            'edit.permissions',
            'delete.permissions',
        ];

        $paymentAndTokoPermissions = [
            'manage.payments',
            'view.payments',
            'create.payments',
            'edit.payments',
            'delete.payments',

            'manage.pesanan',
            'view.pesanan',
            'create.pesanan',
            'edit.pesanan',
            'delete.pesanan',

            'view.toko',
            'edit.toko',
            'delete.toko',
            'manage.toko'
        ];

        $barangPermissions = [
            // Barang Permission
            'view.barang',
            'create.barang',
            'edit.barang',
            'delete.barang',
            'export.barang',
            'import.barang',
            'manage.barang',

            'view.barang.toko',
            'export.barang.toko',
            'manage.barang.toko',

            'view.barang.ki',
            'create.barang.ki',
            'edit.barang.ki',
            'delete.barang.ki',
            'export.barang.ki',
            'import.barang.ki',
            'manage.barang.ki',

            'view.barang.io',
            'export.barang.io',
            'import.barang.io',
            'manage.barang.io',

            'view.paylatter',
            'create.paylatter',
            'edit.paylatter',
            'delete.paylatter',

            'view.infaq',
            'create.infaq',
            'edit.infaq',
            'delete.infaq',

            'view.barang.master',
            'export.barang.master',
            'import.barang.master',
            'manage.barang.master',

            'create.barang.master',
            'edit.barang.master',
            'delete.barang.master',

            'manage.discounts',

            //Notification Permission
            'manage.notifications',
            'send.notifications',
            'view.notifications',
            'subscribe.notifications',
            'unsubscribe.notifications',
            'viewall.notifications',
        ];

        //System Permission
        $systemPermissions = [
            'manage.settings',
            'configure.system',
            'audit.logs.view',
            'audit.logs.manage',
            'backup.system',
            'restore.system',
            'view.logs',
            'manage.api',
            'access.api',
            'view.debug',
            'view.manage',
            'manage.system',
        ];
$websitePermissions = [
    'view.artikel',
    'create.artikel',
    'edit.artikel',
    'delete.artikel',

    'view.pesan',
    'delete.pesan',

    'view.produk',
    'create.produk',
    'edit.produk',
    'delete.produk',

    'view.faq',
    'create.faq',
    'edit.faq',
    'delete.faq',
];
        // Create toko permissions
        $tokoPermissions = [
            'toko.view',

            'toko.manage.orders',
            'toko.view.orders',

            'toko.manage.inventory',
            'toko.view.inventory',

            'toko.view.finances',
            'export.toko.finances',

            'toko.invite',
            'toko.manage.staff',
            'toko.view.staff',

            'toko.edit',
            'toko.purchase.wholesale',
            'toko.view.analytics',

            'toko.pos',
            'guest.accept.invite',


            'toko.create',


        ];

        $allPermissions = array_merge($systemPermissions, $tokoPermissions, $founderPermissions, $paymentAndTokoPermissions, $barangPermissions, $dashboardPermissions);

        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create system roles
        $systemRoles = [
            'founder' => array_merge($systemPermissions, $founderPermissions, $dashboardPermissions, $paymentAndTokoPermissions, $barangPermissions),
            'programmer' => $allPermissions,
            'admin' => [
                $barangPermissions,
                $dashboardPermissions,
                $paymentAndTokoPermissions,
                $tokoPermissions,
            ],

            'accounting' => array_diff($dashboardPermissions, $paymentAndTokoPermissions),

            'operator' => array_diff($paymentAndTokoPermissions, ['access.dashboard']),

            'guest' => [
                'guest.accept.invite',
                'view.barang.ki',
                'toko.create',
            ],

            'shop' => ['view.barang.ki', 'toko.pos'],
            'kasir' => ['view.barang.ki', 'toko.pos'],
        ];

        foreach ($systemRoles as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }

        // Create jabatan for toko
        $jabatanList = [
            [
                'name' => 'Pemilik Toko',
                'slug' => 'pemilik-toko',
                'level' => 5,
                'description' => 'Pemilik toko dengan akses penuh',
                'can_invite_users' => true,
                'can_manage_inventory' => true,
                'can_view_reports' => true,
                'can_manage_orders' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'level' => 4,
                'description' => 'Manager toko dengan akses luas',
                'can_invite_users' => true,
                'can_manage_inventory' => true,
                'can_view_reports' => true,
                'can_manage_orders' => true,
            ],
            [
                'name' => 'Supervisor',
                'slug' => 'supervisor',
                'level' => 3,
                'description' => 'Supervisor dengan akses menengah',
                'can_invite_users' => false,
                'can_manage_inventory' => true,
                'can_view_reports' => true,
                'can_manage_orders' => true,
            ],
            [
                'name' => 'Kasir',
                'slug' => 'kasir',
                'level' => 2,
                'description' => 'Kasir dengan akses terbatas',
                'can_invite_users' => false,
                'can_manage_inventory' => false,
                'can_view_reports' => false,
                'can_manage_orders' => true,
            ],
        ];

        foreach ($jabatanList as $jabatan) {
            JabatanModel::create($jabatan);
        }
        
    }
}
