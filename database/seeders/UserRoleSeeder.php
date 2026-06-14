<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\UserModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\JabatanModel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system users with different roles
        $founder = UserModel::create([
            'name' => 'Founder User',
            'username' => 'founder',
            'email' => 'founder@example.com',
            'password' => Hash::make('founder'),
            'phone_number' => '081234567890',
            'address' => 'Jalan Founder No. 1, Jakarta',
            'ktp_number' => '1234567890123456',
            'ktp_name' => 'Founder User',
            'ktp_address' => 'Jalan Founder No. 1, Jakarta',
            'ktp_image' => 'ktp_verifications/founder.jpg',
            'ktp_verified' => true,
            'profile_completed' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $founder->assignRole('founder');

        $programmer = UserModel::create([
            'name' => 'Programmer User',
            'username' => 'programmer',
            'email' => 'programmer@example.com',
            'password' => Hash::make('programmer'),
            'phone_number' => '081234567891',
            'address' => 'Jalan Programmer No. 2, Jakarta',
            'ktp_number' => '1234567890123457',
            'ktp_name' => 'Programmer User',
            'ktp_address' => 'Jalan Programmer No. 2, Jakarta',
            'ktp_image' => 'ktp_verifications/programmer.jpg',
            'ktp_verified' => true,
            'profile_completed' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);
        $programmer->assignRole('programmer');

        $admin = UserModel::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'phone_verified_at' => now(),
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
            'phone_number' => '081234567892',
            'address' => 'Jalan Admin No. 3, Jakarta',
            'ktp_number' => '1234567890123458',
            'ktp_image' => 'ktp_verifications/admin.jpg',
            'ktp_name' => 'Admin User',
            'ktp_address' => 'Jalan Admin No. 3, Jakarta',
            'ktp_verified' => false,
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $accounting = UserModel::create([
            'name' => 'Accounting User',
            'email' => 'accounting@example.com',
            'username' => 'accounting',
            'phone_verified_at' => now(),
            'password' => Hash::make('accounting'),
            'phone_number' => '081234567893',
            'address' => 'Jalan Accounting No. 4, Jakarta',
            'ktp_number' => '1234567890123459',
            'ktp_name' => 'Accounting User',
            'ktp_address' => 'Jalan Accounting No. 4, Jakarta',
            'ktp_image' => 'ktp_verifications/accounting.jpg',
            'ktp_verified' => false,
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);
        $accounting->assignRole('accounting');

        $operator = UserModel::create([
            'name' => 'Operator User',
            'email' => 'operator@example.com',
            'username' => 'operator',
            'phone_verified_at' => now(),
            'password' => Hash::make('operator'),
            'phone_number' => '081234567894',
            'address' => 'Jalan Operator No. 5, Jakarta',
            'ktp_number' => '1234567890123460',
            'ktp_name' => 'Operator User',
            'ktp_address' => 'Jalan Operator No. 5, Jakarta',
            'ktp_image' => 'ktp_verifications/operator.jpg',
            'ktp_verified' => false,
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);
        $operator->assignRole('operator');

        $guest = UserModel::create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'username' => 'guest',
            'phone_number' => '081234567814',
            'phone_verified_at' => now(),
            'password' => Hash::make('guest'),
            'profile_completed' => false,
        ]);
        $guest->assignRole('guest');

        // Create toko owners
        $tokoOwner1 = UserModel::create([
            'name' => 'Toko Owner 1',
            'email' => 'owner1@example.com',
            'username' => 'owner1',
            'phone_verified_at' => now(),
            'password' => Hash::make('owner'),
            'phone_number' => '081234567895',
            'address' => 'Jalan Toko 1 No. 1, Jakarta',
            'ktp_number' => '1234567890123461',
            'ktp_name' => 'Toko Owner 1',
            'ktp_address' => 'Jalan Toko 1 No. 1, Jakarta',
            'ktp_image' => 'ktp_verifications/owner1.jpg',
            'ktp_verified' => true,
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);

        $tokoOwner2 = UserModel::create([
            'name' => 'Toko Owner 2',
            'email' => 'owner2@example.com',
            'username' => 'owner2',
            'phone_verified_at' => now(),
            'password' => Hash::make('owner'),
            'phone_number' => '081234567896',
            'address' => 'Jalan Toko 2 No. 1, Bandung',
            'ktp_number' => '1234567890123462',
            'ktp_name' => 'Toko Owner 2',
            'ktp_address' => 'Jalan Toko 2 No. 1, Bandung',
            'ktp_image' => 'ktp_verifications/owner2.jpg',
            'ktp_verified' => true,
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);

        $tokoOwner3 = UserModel::create([
            'name' => 'Toko Owner 3',
            'email' => 'owner3@example.com',
            'username' => 'owner3',
            'phone_verified_at' => now(),
            'password' => Hash::make('owner'),
            'phone_number' => '081234567897',
            'address' => 'Jalan Toko 3 No. 1, Surabaya',
            'ktp_number' => '1234567890123463',
            'ktp_name' => 'Toko Owner 3',
            'ktp_address' => 'Jalan Toko 3 No. 1, Surabaya',
            'ktp_image' => 'ktp_verifications/owner3.jpg',
            'ktp_verified' => true,
            'profile_completed' => true,
            'email_verified_at' => now(),
        ]);

        // Create team members for tokos
        $teamMembers = [];
        $positions = ['manager', 'supervisor', 'kasir'];
        $totalTokos = 4; // Jumlah toko, misal 4 toko

        for ($tokoNumber = 1; $tokoNumber <= $totalTokos; $tokoNumber++) {
            foreach ($positions as $index => $position) {
                $i = (($tokoNumber - 1) * count($positions)) + $index + 1;

                $teamMember = UserModel::create([
                    'name' => "$position $i",
                    'email' => "team$i@example.com",
                    'username' => "$position$tokoNumber",
                    'phone_verified_at' => now(),
                    'password' => Hash::make('member'),
                    'phone_number' => '08' . rand(1000000000, 9999999999),
                    'address' => "Jalan Team Member $i, Kota " . match ($tokoNumber) {
                        1 => 'Jakarta',
                        2 => 'Bandung',
                        3 => 'Surabaya',
                        4 => 'Yogyakarta',
                        default => 'Makassar'
                    },
                    'ktp_number' => '12345678901234' . (63 + $i),
                    'ktp_name' => "$position $i",
                    'ktp_address' => "Jalan Team Member $i, Kota " . match ($tokoNumber) {
                        1 => 'Jakarta',
                        2 => 'Bandung',
                        3 => 'Surabaya',
                        4 => 'Yogyakarta',
                        default => 'Makassar'
                    },
                    'ktp_image' => "ktp_verifications/team$i.jpg",
                    'ktp_verified' => false,
                    'profile_completed' => true,
                    'email_verified_at' => now(),
                ]);

                $teamMembers[$tokoNumber][] = [
                    'user' => $teamMember,
                    'position' => $position
                ];

                $teamMember->assignRole('shop');
            }
        }

        // Create 3 different tokos with owners and teams
        $toko1 = TokoModel::create([
            'name' => 'Toko Elektronik Jakarta',
            'slug' => 'toko-elektronik-jakarta',
            'description' => 'Toko elektronik terlengkap di Jakarta',
            'address' => 'Jalan Elektronik No. 123, Jakarta',
            'latitude' => -6.175110,
            'longitude' => 106.865036,
            'owner_id' => $tokoOwner1->id,
            'edited_by' => $tokoOwner1->id,
            'verified_by' => $tokoOwner1->id,
        ]);

        $toko2 = TokoModel::create([
            'name' => 'Toko Fashion Bandung',
            'slug' => 'toko-fashion-bandung',
            'description' => 'Toko fashion terkini di Bandung',
            'address' => 'Jalan Fashion No. 456, Bandung',
            'latitude' => -6.914744,
            'longitude' => 107.609810,
            'owner_id' => $tokoOwner2->id,
            'edited_by' => $tokoOwner2->id,
            'verified_by' => $tokoOwner2->id,
        ]);

        $toko3 = TokoModel::create([
            'name' => 'Toko Makanan Surabaya',
            'slug' => 'toko-makanan-surabaya',
            'description' => 'Toko makanan khas Surabaya',
            'address' => 'Jalan Makanan No. 789, Surabaya',
            'latitude' => -7.250445,
            'longitude' => 112.768845,
            'owner_id' => $tokoOwner3->id,
            'edited_by' => $tokoOwner3->id,
            'verified_by' => $tokoOwner3->id,
            'status' => 'active',
        ]);

        // Get all jabatan
        $pemilikJabatan = JabatanModel::where('slug', 'pemilik-toko')->first();
        $managerJabatan = JabatanModel::where('slug', 'manager')->first();
        $supervisorJabatan = JabatanModel::where('slug', 'supervisor')->first();
        $kasirJabatan = JabatanModel::where('slug', 'kasir')->first();

        // Assign owners to tokos with pemilik jabatan
        $toko1->users()->attach($tokoOwner1->id, ['jabatan_id' => $pemilikJabatan->id]);
        $toko2->users()->attach($tokoOwner2->id, ['jabatan_id' => $pemilikJabatan->id]);
        $toko3->users()->attach($tokoOwner3->id, ['jabatan_id' => $pemilikJabatan->id]);

        // Give toko permissions to owners
        $tokoPermissions = Permission::where('name', 'like', 'toko.%')->pluck('name');
        $tokoOwner1->givePermissionTo($tokoPermissions);
        $tokoOwner2->givePermissionTo($tokoPermissions);
        $tokoOwner3->givePermissionTo($tokoPermissions);
        $tokoOwner1->assignrole('shop');
        $tokoOwner2->assignrole('shop');
        $tokoOwner3->assignrole('shop');

        // Create some accepted team members for each toko
        foreach ([1, 2, 3] as $tokoNum) {
            $currentToko = ${'toko' . $tokoNum};

            foreach ($teamMembers[$tokoNum] as $index => $member) {
                if ($index < 3) { // First 3 members already accepted
                    $jabatan = null;
                    switch ($member['position']) {
                        case 'manager':
                            $jabatan = $managerJabatan;
                            break;
                        case 'supervisor':
                            $jabatan = $supervisorJabatan;
                            break;
                        case 'kasir':
                            $jabatan = $kasirJabatan;
                            break;
                    }

                    $currentToko->users()->attach($member['user']->id, ['jabatan_id' => $jabatan->id]);

                    // Assign permissions based on jabatan
                    $this->assignPermissionsByJabatan($member['user'], $jabatan);
                }
            }
        }

        // Create pending invitations for the remaining team members
        foreach ([1, 2, 3] as $tokoNum) {
            $currentToko = ${'toko' . $tokoNum};
            $currentOwner = ${'tokoOwner' . $tokoNum};

            foreach ($teamMembers[$tokoNum] as $index => $member) {
                if ($index >= 3) { // Last 2 members have pending invitations
                    $jabatan = null;
                    switch ($member['position']) {
                        case 'manager':
                            $jabatan = $managerJabatan;
                            break;
                        case 'supervisor':
                            $jabatan = $supervisorJabatan;
                            break;
                        case 'kasir':
                            $jabatan = $kasirJabatan;
                            break;
                    }

                    // Create invitation
                    DB::table('toko_invitations')->insert([
                        'toko_id' => $currentToko->id,
                        'inviter_id' => $currentOwner->id,
                        'invited_id' => $member['user']->id,
                        'jabatan_id' => $jabatan->id,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Assign permissions to user based on jabatan
     */
    private function assignPermissionsByJabatan($user, $jabatan)
    {
        // Reset permission toko terlebih dahulu
        $tokoPermissions = Permission::where('name', 'like', 'toko.%')->get();
        $user->revokePermissionTo($tokoPermissions);

        // Assign permission berdasarkan level jabatan
        $permissions = [];

        // Permissions dasar
        $permissions[] = Permission::findByName('toko.view');
        $permissions[] = Permission::findByName('toko.view.inventory');

        if ($jabatan->can_manage_orders) {
            $permissions[] = Permission::findByName('toko.manage.orders');
            $permissions[] = Permission::findByName('toko.view.orders');
        }

        if ($jabatan->can_manage_inventory) {
            $permissions[] = Permission::findByName('toko.manage.inventory');
        }

        if ($jabatan->can_view_reports) {
            $permissions[] = Permission::findByName('toko.view.finances');
        }

        if ($jabatan->can_invite_users) {
            $permissions[] = Permission::findByName('toko.invite');
            $permissions[] = Permission::findByName('toko.manage.staff');
            $permissions[] = Permission::findByName('toko.view.staff');
        }

        // Jika level jabatan adalah pemilik (level 5)
        if ($jabatan->level >= 5) {
            $permissions[] = Permission::findByName('toko.create');
            $permissions[] = Permission::findByName('toko.edit');
            $permissions[] = Permission::findByName('toko.manage.finances');
            $permissions[] = Permission::findByName('toko.purchase.wholesale');
            $permissions[] = Permission::findByName('toko.sell.retail');
        }

        $user->givePermissionTo($permissions);
    }
}
