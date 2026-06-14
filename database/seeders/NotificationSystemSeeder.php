<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users from the database
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->info('No users found in the database. Please seed users first.');
            return;
        }

        // Get all roles from the system
        $roles = [
            'founder',
            'programmer',
            'admin',
            'accounting',
            'operator',
            'guest',
            'pemilik-toko',
            'manager',
            'supervisor',
            'kasir',
            'staff'
        ];

        // Clear existing tables to prevent duplicate constraints
        DB::table('notification_groups')->truncate();
        DB::table('notifications')->delete(); // Can't truncate if there are foreign keys, but delete works.
        DB::table('notification_subscriptions')->truncate();
        DB::table('notification_settings')->truncate();

        // Create notifications for each role
        $notificationIds = $this->seedNotificationsForRoles($userIds, $roles);

        // Create notification settings
        $this->seedNotificationSettings($userIds);

        // Create notification subscriptions
        $this->seedNotificationSubscriptions($userIds);

        // Create formal notification templates
        $this->seedNotificationTemplates();

        $this->command->info('Notification system seeded successfully.');
    }

    /**
     * Seed notification templates with formal Indonesian language and correct Flutter paths
     */
    private function seedNotificationTemplates()
    {
        $templates = [
            [
                'type' => 'order_status_changed',
                'title_template' => 'Pembaruan Pesanan #{transaction_id}',
                'message_template' => 'Yth. {user_name}, pesanan Anda dengan nomor referensi {transaction_id} saat ini berstatus {status_label}. Total tagihan: Rp. {total}. Terima kasih telah menggunakan layanan kami.',
                'path_template' => '/historyShopping',
                'is_active' => true,
            ],
            [
                'type' => 'order_status_changed_role',
                'title_template' => 'Pemberitahuan Pesanan: {toko_name}',
                'message_template' => 'Terdapat pembaruan pesanan dengan ID #{transaction_id} menjadi {status_label} senilai Rp. {total} pada toko {toko_name}. Mohon segera ditindaklanjuti.',
                'path_template' => '/salesMenu',
                'is_active' => true,
            ],
            [
                'type' => 'payment_received',
                'title_template' => 'Konfirmasi Pembayaran Berhasil',
                'message_template' => 'Yth. {user_name}, pembayaran Anda sebesar Rp. {total} untuk pesanan #{transaction_id} telah kami terima. Pesanan Anda sedang diproses.',
                'path_template' => '/historyShopping',
                'is_active' => true,
            ],
            [
                'type' => 'payment_rejected',
                'title_template' => 'Pembayaran Gagal/Ditolak',
                'message_template' => 'Yth. {user_name}, pembayaran untuk pesanan #{transaction_id} tidak dapat divalidasi dengan alasan: {reason}. Silakan lakukan pengecekan ulang.',
                'path_template' => '/historyShopping',
                'is_active' => true,
            ],
            [
                'type' => 'toko_approved',
                'title_template' => 'Persetujuan Pembuatan Toko',
                'message_template' => 'Selamat, pengajuan toko "{toko_name}" telah disetujui. Anda kini memiliki akses penuh untuk mengelola inventaris dan penjualan.',
                'path_template' => '/menu', // Mengarahkan ke menu utama / dashboard
                'is_active' => true,
            ],
            [
                'type' => 'toko_rejected',
                'title_template' => 'Penolakan Pembuatan Toko',
                'message_template' => 'Mohon maaf, pengajuan untuk toko "{toko_name}" belum dapat disetujui. Alasan penolakan: {reason}. Silakan perbaiki dan ajukan kembali.',
                'path_template' => '/openShop', // Kembali ke halaman buka toko
                'is_active' => true,
            ],
            [
                'type' => 'ktp_verified',
                'title_template' => 'Verifikasi Identitas Berhasil',
                'message_template' => 'Yth. {user_name}, verifikasi identitas (KTP) Anda telah divalidasi. Seluruh layanan kini dapat Anda gunakan secara maksimal.',
                'path_template' => '/profile',
                'is_active' => true,
            ],
            [
                'type' => 'ktp_rejected',
                'title_template' => 'Verifikasi Identitas Ditolak',
                'message_template' => 'Yth. {user_name}, verifikasi KTP Anda belum berhasil divalidasi. Alasan: {reason}. Silakan lakukan pembaruan dokumen melalui profil Anda.',
                'path_template' => '/editProfile',
                'is_active' => true,
            ],
            [
                'type' => 'cart_reminder',
                'title_template' => 'Selesaikan Pesanan Anda',
                'message_template' => 'Yth. {user_name}, terdapat produk menarik yang menunggu di keranjang belanja Anda. Segera lakukan pembayaran sebelum kehabisan stok!',
                'path_template' => '/shoppingMenu',
                'is_active' => true,
            ],
            [
                'type' => 'promo_broadcast',
                'title_template' => 'Penawaran Spesial Kedai Indonesia',
                'message_template' => 'Yth. {user_name}, nikmati promo eksklusif dan diskon menarik hari ini. Buka aplikasi sekarang untuk melihat penawaran terbaik kami.',
                'path_template' => '/menu',
                'is_active' => true,
            ],
            [
                'type' => 'pakai_dulu_approved',
                'title_template' => 'Limit Pakai Dulu Disetujui',
                'message_template' => 'Selamat {user_name}! Pengajuan fasilitas Pakai Dulu Anda telah disetujui. Kini Anda dapat menikmati kemudahan berbelanja dengan limit yang tersedia.',
                'path_template' => '/pakaiDulu',
                'is_active' => true,
            ],
            [
                'type' => 'pakai_dulu_rejected',
                'title_template' => 'Pengajuan Pakai Dulu',
                'message_template' => 'Mohon maaf {user_name}, pengajuan fasilitas Pakai Dulu Anda saat ini belum dapat disetujui. Silakan tingkatkan transaksi Anda dan coba kembali nanti.',
                'path_template' => '/pakaiDulu',
                'is_active' => true,
            ],
            [
                'type' => 'belanja_cepat_success',
                'title_template' => 'Pesanan Belanja Cepat Berhasil',
                'message_template' => 'Yth. {user_name}, transaksi Belanja Cepat Anda dengan ID #{transaction_id} telah berhasil divalidasi. Terima kasih telah menggunakan layanan kami.',
                'path_template' => '/historyShopping',
                'is_active' => true,
            ],
        ];

        // Delete existing templates to prevent unique constraint violations during seeding
        DB::table('notification_templates')->truncate();

        foreach ($templates as $template) {
            DB::table('notification_templates')->insert([
                'id' => Str::uuid(),
                'type' => $template['type'],
                'title_template' => $template['title_template'],
                'message_template' => $template['message_template'],
                'path_template' => $template['path_template'],
                'is_active' => $template['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Seed notifications for each role
     */
    private function seedNotificationsForRoles($userIds, $roles)
    {
        $notificationIds = [];
        $notificationTypes = [
            'system_update',
            'task_assigned',
            'inventory_low',
            'order_completed',
            'payment_received',
            'sale_completed',
            'refund_issued',
            'customer_inquiry'
        ];

        // Create targeted notifications for each role
        foreach ($roles as $role) {
            // Generate role-specific notification type
            $notificationType = $this->getNotificationTypeForRole($role);

            // For each role, create one notification targeted to all users with that role
            $id = Str::uuid();

            // Random sender from users
            $senderId = $userIds[array_rand($userIds)];

            // Create notification data based on role and type
            $data = $this->generateRoleBasedNotificationData($role, $notificationType);

            // Path relevant to the role
            $path = $this->getPathForRole($role);

            // Insert the notification aimed at this role
            DB::table('notifications')->insert([
                'id' => $id,
                'type' => $notificationType,
                'notifiable_type' => 'App\Models\Role',
                'notifiable_id' => $role,
                'data' => json_encode($data),
                'sender_type' => 'App\Models\Auth\UserModel',
                'sender_id' => $senderId,
                'path' => $path,
                'status' => 'unopen',
                'is_active' => true,
                'is_system' => true,
                'is_important' => in_array($role, ['founder', 'admin', 'programmer']),
                'created_at' => Carbon::now()->subHours(rand(1, 24)),
                'updated_at' => Carbon::now()->subHours(rand(1, 24)),
            ]);

            $notificationIds[] = $id;

            // Create notification group entry for this role
            DB::table('notification_groups')->insert([
                'id' => Str::uuid(),
                'notification_id' => $id,
                'group_type' => 'role',
                'group_id' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Now create individual notifications for users
            foreach ($userIds as $index => $userId) {
                if ($index % 2 == 0) { // Just to not create too many notifications, do for every other user
                    $individualId = Str::uuid();
                    $individualType = $notificationTypes[array_rand($notificationTypes)];
                    $individualData = $this->generateNotificationData($individualType);

                    DB::table('notifications')->insert([
                        'id' => $individualId,
                        'type' => $individualType,
                        'notifiable_type' => 'App\Models\Auth\UserModel',
                        'notifiable_id' => $userId,
                        'data' => json_encode($individualData),
                        'sender_type' => 'App\Models\Auth\UserModel',
                        'sender_id' => $userIds[array_rand(array_diff($userIds, [$userId]))], // Different user as sender
                        'path' => $this->getRandomPath(),
                        'status' => rand(0, 3) > 0 ? 'unopen' : ['read', 'clicked', 'downloaded'][rand(0, 2)],
                        'is_active' => true,
                        'is_system' => $individualType === 'system_update',
                        'is_important' => rand(0, 5) === 0,
                        'read_at' => rand(0, 3) > 0 ? null : Carbon::now()->subMinutes(rand(1, 1000)),
                        'created_at' => Carbon::now()->subHours(rand(1, 72)),
                        'updated_at' => Carbon::now()->subHours(rand(1, 24)),
                    ]);

                    $notificationIds[] = $individualId;
                }
            }
        }

        return $notificationIds;
    }

    /**
     * Get notification type based on role
     */
    private function getNotificationTypeForRole($role)
    {
        switch ($role) {
            case 'founder':
            case 'programmer':
            case 'admin':
                return 'system_update';

            case 'accounting':
                return 'payment_received';

            case 'operator':
                return 'task_assigned';

            case 'guest':
                return 'system_update';

            case 'toko_owner':
                return 'sale_completed';

            case 'toko_manager':
                return 'inventory_low';

            case 'toko_supervisor':
                return 'refund_issued';

            case 'toko_kasir':
                return 'order_completed';

            case 'toko_staff':
                return 'customer_inquiry';

            default:
                return 'system_update';
        }
    }

    /**
     * Get path based on role
     */
    private function getPathForRole($role)
    {
        switch ($role) {
            case 'founder':
            case 'programmer':
            case 'admin':
            case 'operator':
            case 'accounting':
                return '/dashboard';

            case 'guest':
                return '/';

            case 'toko_owner':
                return '/reports/sales';

            case 'toko_manager':
                return '/inventory';

            case 'toko_supervisor':
                return '/sales';

            case 'toko_kasir':
                return '/pos';

            case 'toko_staff':
                return '/customers';

            default:
                return '/dashboard';
        }
    }

    /**
     * Generate role-specific notification data
     */
    private function generateRoleBasedNotificationData($role, $type)
    {
        switch ($role) {
            case 'founder':
                return [
                    'message' => 'Critical system update scheduled',
                    'details' => 'A major system update is scheduled for maintenance tonight at 2 AM. Please prepare accordingly.',
                    'version' => '2.5.0',
                    'impact' => 'high'
                ];

            case 'programmer':
                return [
                    'message' => 'API changes deployed to production',
                    'details' => 'Recent API changes have been deployed. Please monitor for any issues.',
                    'version' => '2.4.7',
                    'components' => ['API', 'Authentication', 'Database']
                ];

            case 'admin':
                return [
                    'message' => 'User management updates',
                    'details' => 'New user management features have been added. Training materials available in the admin section.',
                    'features' => ['Bulk user import', 'Advanced filtering', 'Permission templates']
                ];

            case 'accounting':
                return [
                    'message' => 'Monthly financial reports ready',
                    'details' => 'The monthly financial reports are now available for review.',
                    'period' => 'April 2025',
                    'total_revenue' => rand(5000000, 50000000),
                    'report_id' => 'FIN-' . rand(1000, 9999)
                ];

            case 'operator':
                return [
                    'message' => 'New task assignment workflow',
                    'details' => 'Task assignment workflow has been updated. Please review the new process.',
                    'changes' => ['Priority-based assignment', 'Task templates', 'Automated notifications']
                ];

            case 'guest':
                return [
                    'message' => 'Welcome to our platform',
                    'details' => 'Thank you for joining. Here are some features you might want to explore.',
                    'features' => ['Product catalog', 'Special offers', 'Customer support']
                ];

            case 'toko_owner':
                return [
                    'message' => 'Sales analytics updated',
                    'details' => 'Your weekly sales analytics have been updated.',
                    'period' => 'Week ' . date('W'),
                    'total_sales' => rand(1000000, 10000000),
                    'growth' => rand(1, 15) . '%'
                ];

            case 'toko_manager':
                return [
                    'message' => 'Inventory alert: Multiple items below threshold',
                    'details' => 'Several items in your inventory are below the minimum threshold.',
                    'items_count' => rand(3, 10),
                    'priority' => 'high',
                    'report_link' => '/inventory/alerts'
                ];

            case 'toko_supervisor':
                return [
                    'message' => 'Staff schedule updated',
                    'details' => 'The staff schedule for next week has been updated. Please review.',
                    'period' => 'Week ' . (date('W') + 1),
                    'changes' => rand(2, 8)
                ];

            case 'toko_kasir':
                return [
                    'message' => 'New POS features available',
                    'details' => 'New features have been added to the POS system. Training will be provided.',
                    'features' => ['Quick item search', 'Customer loyalty integration', 'Split payment options']
                ];

            case 'toko_staff':
                return [
                    'message' => 'Customer service reminder',
                    'details' => 'Remember to follow the updated customer service guidelines.',
                    'key_points' => ['Greet customers promptly', 'Offer assistance proactively', 'Follow up on inquiries']
                ];

            default:
                return [
                    'message' => 'System notification',
                    'details' => 'This is a system notification for all users.'
                ];
        }
    }

    /**
     * Generate general notification data
     */
    private function generateNotificationData($type)
    {
        switch ($type) {
            case 'system_update':
                return [
                    'message' => 'System has been updated',
                    'version' => '1.' . rand(0, 9) . '.' . rand(0, 9),
                    'details' => 'New features and bug fixes have been implemented.'
                ];

            case 'task_assigned':
                return [
                    'message' => 'A new task has been assigned to you',
                    'task_id' => 'TSK-' . rand(10000, 99999),
                    'task_name' => 'Task #' . rand(1000, 9999),
                    'priority' => ['Low', 'Medium', 'High'][array_rand(['Low', 'Medium', 'High'])]
                ];

            case 'inventory_low':
                return [
                    'message' => 'Inventory alert: Item is below threshold',
                    'item_id' => 'ITM-' . rand(10000, 99999),
                    'item_name' => 'Product #' . rand(1000, 9999),
                    'current_stock' => rand(1, 5),
                    'threshold' => 10
                ];

            case 'order_completed':
                return [
                    'message' => 'Order has been completed',
                    'order_id' => 'ORD-' . rand(10000, 99999),
                    'customer_name' => 'Customer #' . rand(100, 999),
                    'total_amount' => rand(50000, 1000000)
                ];

            case 'payment_received':
                return [
                    'message' => 'Payment received',
                    'payment_id' => 'PAY-' . rand(10000, 99999),
                    'order_id' => 'ORD-' . rand(10000, 99999),
                    'amount' => rand(50000, 1000000),
                    'payment_method' => ['Cash', 'Credit Card', 'Transfer', 'E-wallet'][array_rand(['Cash', 'Credit Card', 'Transfer', 'E-wallet'])]
                ];

            case 'sale_completed':
                return [
                    'message' => 'Sale completed successfully',
                    'sale_id' => 'SL-' . rand(10000, 99999),
                    'items_count' => rand(1, 20),
                    'total_amount' => rand(50000, 1000000)
                ];

            case 'refund_issued':
                return [
                    'message' => 'Refund has been issued',
                    'refund_id' => 'RF-' . rand(10000, 99999),
                    'order_id' => 'ORD-' . rand(10000, 99999),
                    'amount' => rand(10000, 500000),
                    'reason' => ['Damaged product', 'Wrong item', 'Customer dissatisfaction', 'Other'][array_rand(['Damaged product', 'Wrong item', 'Customer dissatisfaction', 'Other'])]
                ];

            case 'customer_inquiry':
                return [
                    'message' => 'New customer inquiry received',
                    'inquiry_id' => 'INQ-' . rand(10000, 99999),
                    'customer_name' => 'Customer #' . rand(100, 999),
                    'subject' => 'Question about product #' . rand(1000, 9999)
                ];

            default:
                return [
                    'message' => 'System notification',
                    'details' => 'This is a system notification for all users.'
                ];
        }
    }

    /**
     * Get random path for notifications
     */
    private function getRandomPath()
    {
        $paths = [
            '/dashboard',
            '/profile',
            '/tasks',
            '/documents',
            '/settings',
            '/inventory',
            '/sales',
            '/customers',
            '/finances',
            '/reports',
            '/pos',
            '/products',
            null
        ];

        return $paths[array_rand($paths)];
    }

    /**
     * Seed notification settings table
     */
    private function seedNotificationSettings($userIds)
    {
        $notificationTypes = [
            'system_update',
            'task_assigned',
            'inventory_low',
            'order_completed',
            'payment_received',
            'sale_completed',
            'refund_issued',
            'customer_inquiry'
        ];

        foreach ($userIds as $userId) {
            foreach ($notificationTypes as $type) {
                DB::table('notification_settings')->insert([
                    'id' => Str::uuid(),
                    'user_type' => 'App\Models\Auth\UserModel',
                    'user_id' => $userId,
                    'notification_type' => $type,
                    'is_enabled' => rand(0, 10) > 1, // 90% enabled
                    'is_email_enabled' => rand(0, 2) > 0, // 66% email enabled
                    'is_push_enabled' => rand(0, 2) > 0, // 66% push enabled
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Seed notification subscriptions table
     */
    private function seedNotificationSubscriptions($userIds)
    {
        $subscribableTypes = [
            'App\Models\Toko',
            'App\Models\Product',
            'App\Models\Category',
            'App\Models\Customer'
        ];

        // Create 5 sample entities for each subscribable type
        $subscribableIds = [];
        foreach ($subscribableTypes as $type) {
            for ($i = 0; $i < 5; $i++) {
                $subscribableIds[$type][] = Str::uuid();
            }
        }

        // Create subscriptions - for each user create at least one subscription
        foreach ($userIds as $userId) {
            $subscribableType = $subscribableTypes[array_rand($subscribableTypes)];
            $subscribableId = $subscribableIds[$subscribableType][array_rand($subscribableIds[$subscribableType])];

            DB::table('notification_subscriptions')->insert([
                'id' => Str::uuid(),
                'user_type' => 'App\Models\Auth\UserModel',
                'user_id' => $userId,
                'subscribable_type' => $subscribableType,
                'subscribable_id' => $subscribableId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add additional random subscriptions (0-3 more per user)
            $additionalCount = rand(0, 3);
            for ($i = 0; $i < $additionalCount; $i++) {
                $addType = $subscribableTypes[array_rand($subscribableTypes)];
                $addId = $subscribableIds[$addType][array_rand($subscribableIds[$addType])];

                // Check if subscription already exists
                $exists = DB::table('notification_subscriptions')
                    ->where('user_type', 'App\Models\Auth\UserModel')
                    ->where('user_id', $userId)
                    ->where('subscribable_type', $addType)
                    ->where('subscribable_id', $addId)
                    ->exists();

                if (!$exists) {
                    DB::table('notification_subscriptions')->insert([
                        'id' => Str::uuid(),
                        'user_type' => 'App\Models\Auth\UserModel',
                        'user_id' => $userId,
                        'subscribable_type' => $addType,
                        'subscribable_id' => $addId,
                        'is_active' => rand(0, 10) > 1, // 90% active
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
