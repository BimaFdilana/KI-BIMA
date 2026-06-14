<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolePermissionSeeder::class,
            UserRoleSeeder::class,
            NotificationSystemSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            KriteriaAndSubkriteriaSeeder::class,
            SatuanItemSeeder::class,
            BarangSeeder::class, // disabled - data barang KI dikosongkan
            DataTokoSeeder::class,
            Pakdul::class,
            InfaqSeeder::class,
            InformationSeeder::class,
            KomunitasSeeder::class,
            AdditionalMasterDataSeeder::class
        ]);
    }
}
