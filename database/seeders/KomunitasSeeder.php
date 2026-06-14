<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\UserModel;
use App\Models\Komunitas\KomunitasPost;
use App\Models\Komunitas\KomunitasPostMedia;
use App\Models\Komunitas\KomunitasComment;
use App\Models\Komunitas\KomunitasLike;
use App\Models\Komunitas\KomunitasCommentLike;

class KomunitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = UserModel::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Run UserRoleSeeder first.');
            return;
        }

        $postContents = [
            'Baru aja launch project baru! Excited untuk share progress ke kalian semua 🚀',
            'Tips dan trick Laravel yang bisa bikin development lebih cepat dan efisien',
            'Workflow terbaru yang saya gunakan untuk manage project dengan tim',
            'Tutorial lengkap implementasi authentication dengan Spatie Permission',
            'Best practices untuk database design di aplikasi e-commerce',
            'Sharing experience ngerjain project besar dengan deadline ketat',
            'Database optimization tips yang significantly improve query performance',
            'Frontend dan backend integration patterns yang proven effective',
        ];

        $postImages = [
            ['image1.jpg', 'image'],
            ['image2.jpg', 'image'],
            ['image3.jpg', 'image'],
            ['video1.mp4', 'video'],
            ['image4.jpg', 'image'],
        ];

        foreach ($postContents as $key => $content) {
            $post = KomunitasPost::create([
                'user_id' => $users[$key % count($users)]->id,
                'content' => $content,
            ]);

            // Add media (1-3 media per post)
            $mediaCount = rand(1, 3);
            for ($i = 0; $i < $mediaCount; $i++) {
                $randomImage = $postImages[array_rand($postImages)];
                KomunitasPostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => 'posts/' . $randomImage[0],
                    'type' => $randomImage[1],
                    'order' => $i,
                ]);
            }

            // Add likes (5-20 likes per post)
            $likeCount = rand(5, 20);
            $likedByUsers = $users->random(min($likeCount, $users->count()));
            foreach ($likedByUsers as $user) {
                KomunitasLike::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
            }
            $post->update(['likes_count' => $likedByUsers->count()]);

            // Add comments (2-5 comments per post)
            $commentCount = rand(2, 5);
            $commentIds = [];
            for ($i = 0; $i < $commentCount; $i++) {
                $commentUser = $users->random();
                $comment = KomunitasComment::create([
                    'user_id' => $commentUser->id,
                    'post_id' => $post->id,
                    'content' => $this->getRandomComment(),
                ]);

                $commentIds[] = $comment->id;

                // Add replies to some comments
                if (rand(0, 1)) {
                    $replyCount = rand(1, 2);
                    $replyLikeCount = 0;

                    for ($j = 0; $j < $replyCount; $j++) {
                        $replyUser = $users->random();
                        $reply = KomunitasComment::create([
                            'user_id' => $replyUser->id,
                            'post_id' => $post->id,
                            'parent_id' => $comment->id,
                            'content' => $this->getRandomReply(),
                        ]);

                        // Add likes to reply
                        if (rand(0, 1)) {
                            $replyLikes = rand(1, 5);
                            $likeUsersForReply = $users->random(min($replyLikes, $users->count()));
                            foreach ($likeUsersForReply as $likeUser) {
                                KomunitasCommentLike::create([
                                    'user_id' => $likeUser->id,
                                    'comment_id' => $reply->id,
                                ]);
                            }
                            $reply->update(['likes_count' => $likeUsersForReply->count()]);
                            $replyLikeCount += $likeUsersForReply->count();
                        }
                    }
                }

                // Add likes to comment
                $commentLikeCount = 0;
                if (rand(0, 1)) {
                    $commentLikes = rand(1, 10);
                    $likeUsersForComment = $users->random(min($commentLikes, $users->count()));
                    foreach ($likeUsersForComment as $likeUser) {
                        KomunitasCommentLike::create([
                            'user_id' => $likeUser->id,
                            'comment_id' => $comment->id,
                        ]);
                    }
                    $comment->update(['likes_count' => $likeUsersForComment->count()]);
                    $commentLikeCount = $likeUsersForComment->count();
                }
            }

            $post->update(['comments_count' => $post->comments()->count()]);
        }

        $this->command->info('Komunitas posts, comments, and likes seeded successfully!');
    }

    /**
     * Get random comment text
     */
    private function getRandomComment(): string
    {
        $comments = [
            'Keren banget! Bisa sharing tutorial lengkapnya?',
            'Setuju banget dengan pendapatmu!',
            'Ini bagus, tapi ada yang bisa ditingkatkan lagi.',
            'Wow amazing work! 🔥',
            'Terima kasih sudah sharing ilmunya.',
            'Bisa minta source code-nya?',
            'Sangat membantu, terus berkarya!',
            'Ini exactly yang saya cari!',
            'Bagus banget implementasinya!',
            'Bisa ditunggu tutorial selengkapnya?',
        ];

        return $comments[array_rand($comments)];
    }

    /**
     * Get random reply text
     */
    private function getRandomReply(): string
    {
        $replies = [
            'Setuju! Aku juga pikir gitu.',
            'Ada di dokumentasi resmi kok.',
            'Makasih sudah share insights-nya!',
            'Bagus! Nanti aku coba nih.',
            'Thanks for clarifying this!',
            'Ini solusi yang paling elegant.',
            'Sama, pengalaman aku juga begitu.',
            'Noted! Akan dicoba di project berikutnya.',
            'Sangat membantu, makasih!',
            'Aku juga pernah ngalamin itu.',
        ];

        return $replies[array_rand($replies)];
    }
}
