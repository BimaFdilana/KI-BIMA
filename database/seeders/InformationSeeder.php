<?php

namespace Database\Seeders;

use App\Models\Information\Information;
use App\Models\Information\InformationCategory;
use App\Models\Information\InformationComment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InformationSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->pluck('id')->toArray();

        if (empty($users)) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $categories = [
            [
                'name' => 'Berita Terkini',
                'slug' => 'berita-terkini',
                'description' => 'Informasi berita terbaru dan terkini dari berbagai sumber',
                'icon' => '📰',
            ],
            [
                'name' => 'Tips & Trik',
                'slug' => 'tips-trik',
                'description' => 'Kumpulan tips dan trik bermanfaat untuk kehidupan sehari-hari',
                'icon' => '💡',
            ],
            [
                'name' => 'Teknologi',
                'slug' => 'teknologi',
                'description' => 'Update teknologi terbaru, gadget, dan inovasi digital',
                'icon' => '💻',
            ],
            [
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'description' => 'Informasi kesehatan, tips hidup sehat, dan medical update',
                'icon' => '🏥',
            ],
            [
                'name' => 'Kuliner',
                'slug' => 'kuliner',
                'description' => 'Resep masakan, review makanan, dan tips kuliner',
                'icon' => '🍳',
            ],
            [
                'name' => 'Bisnis & Keuangan',
                'slug' => 'bisnis-keuangan',
                'description' => 'Informasi bisnis, investasi, dan tips keuangan',
                'icon' => '💰',
            ],
            [
                'name' => 'Pendidikan',
                'slug' => 'pendidikan',
                'description' => 'Artikel pendidikan, tips belajar, dan informasi akademik',
                'icon' => '📚',
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Gaya hidup, fashion, dan tren terkini',
                'icon' => '✨',
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'description' => 'Berita olahraga, tips fitness, dan event olahraga',
                'icon' => '⚽',
            ],
            [
                'name' => 'Hiburan',
                'slug' => 'hiburan',
                'description' => 'Film, musik, selebriti, dan entertainment',
                'icon' => '🎬',
            ],
            [
                'name' => 'Wisata',
                'slug' => 'wisata',
                'description' => 'Destinasi wisata, tips traveling, dan review tempat',
                'icon' => '✈️',
            ],
            [
                'name' => 'Otomotif',
                'slug' => 'otomotif',
                'description' => 'Informasi mobil, motor, dan dunia otomotif',
                'icon' => '🚗',
            ],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[] = DB::table('information_categories')->insertGetId(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Created ' . count($categoryIds) . ' information categories');

        $informations = [
            [
                'title' => 'Pemerintah Luncurkan Program Bantuan UMKM 2025',
                'description' => 'Program bantuan modal usaha untuk UMKM dengan bunga rendah dan tenor panjang',
                'content' => 'Pemerintah Indonesia meluncurkan program bantuan UMKM tahun 2025 dengan total dana Rp 50 triliun.',
                'category_id' => $categoryIds[0],
                'visibility' => 'public',
            ],
            [
                'title' => '10 Cara Menghemat Listrik di Rumah',
                'description' => 'Tips praktis menurunkan tagihan listrik hingga 30% per bulan',
                'content' => 'Gunakan lampu LED, cabut charger saat tidak digunakan, atur suhu AC pada 24-25 derajat.',
                'category_id' => $categoryIds[1],
                'visibility' => 'public',
            ],
            [
                'title' => 'Review Smartphone Flagship 2025',
                'description' => 'Uji coba mendalam smartphone terbaru dengan chipset terkini',
                'content' => 'Smartphone flagship tahun 2025 hadir dengan chipset 3nm yang super cepat.',
                'category_id' => $categoryIds[2],
                'visibility' => 'public',
            ],
        ];

        $informationIds = [];
        foreach ($informations as $info) {
            $userId = $users[array_rand($users)];
            $createdAt = Carbon::now()->subDays(rand(1, 30));

            $informationId = DB::table('informations')->insertGetId([
                'user_id' => $userId,
                'title' => $info['title'],
                'description' => $info['description'],
                'content' => $info['content'],
                'category_id' => $info['category_id'],
                'visibility' => $info['visibility'],
                'shares_count' => rand(0, 20),
                'is_published' => true,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,

            ]);

            $informationIds[] = $informationId;

            // Add media for each information
            $mediaCount = rand(1, 3);
            for ($j = 0; $j < $mediaCount; $j++) {
                $type = rand(0, 1) ? 'image' : 'video';
                DB::table('information_media')->insert([
                    'information_id' => $informationId,
                    'type' => $type,
                    'media_path' => $type === 'image' ? 'informations/images/sample-' . rand(1, 5) . '.jpg' : 'informations/videos/sample-' . rand(1, 3) . '.mp4',
                    'thumbnail_path' => $type === 'video' ? 'informations/thumbnails/sample-' . rand(1, 3) . '.jpg' : null,
                    'alt_text' => 'Sample media for ' . $info['title'],
                    'order' => $j + 1,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            $commentsCount = rand(2, 5);
            for ($i = 0; $i < $commentsCount; $i++) {
                $commenterId = $users[array_rand($users)];
                $comments = [
                    'Informasi yang sangat bermanfaat! Terima kasih sudah berbagi.',
                    'Wah menarik sekali, saya akan coba tips ini.',
                    'Artikel yang bagus dan informatif.',
                    'Sangat membantu, ditunggu artikel selanjutnya!',
                    'Penjelasannya detail dan mudah dipahami.',
                ];

                DB::table('information_comments')->insert([
                    'information_id' => $informationId,
                    'user_id' => $commenterId,
                    'parent_id' => null,
                    'content' => $comments[array_rand($comments)],
                    'replies_count' => 0,
                    'created_at' => $createdAt->copy()->addMinutes(rand(1, 1500)),
                    'updated_at' => $createdAt->copy()->addMinutes(rand(1, 1500)),
                ]);
            }
        }

        $this->command->info('Created ' . count($informationIds) . ' information posts with comments');
        $this->command->info('Information seeder completed successfully!');
    }
}
