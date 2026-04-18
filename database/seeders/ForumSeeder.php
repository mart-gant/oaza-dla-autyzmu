<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Hash;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create deterministic forum users (no factory needed in production)
        $user1 = User::firstOrCreate(
            ['email' => 'forum.user1@oaza.pl'],
            [
                'name' => 'Forum User 1',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'email_verified_at' => now(),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'forum.user2@oaza.pl'],
            [
                'name' => 'Forum User 2',
                'password' => Hash::make('password'),
                'role' => 'autistic_person',
                'email_verified_at' => now(),
            ]
        );

        // Create categories
        $category1 = ForumCategory::create(['name' => 'Ogólne']);
        $category2 = ForumCategory::create(['name' => 'Zdrowie psychiczne']);

        // Create topics
        $topic1 = ForumTopic::create([
            'title' => 'Witaj na forum!',
            'user_id' => $user1->id,
            'forum_category_id' => $category1->id,
        ]);

        $topic2 = ForumTopic::create([
            'title' => 'Jak radzić sobie ze stresem?',
            'user_id' => $user2->id,
            'forum_category_id' => $category2->id,
        ]);

        // Create posts
        ForumPost::create([
            'body' => 'To jest pierwszy post na tym forum. Zapraszamy do dyskusji!',
            'user_id' => $user1->id,
            'forum_topic_id' => $topic1->id,
        ]);

        ForumPost::create([
            'body' => 'Stres jest częścią życia, ale istnieją sposoby, aby sobie z nim radzić. Podziel się swoimi doświadczeniami!',
            'user_id' => $user2->id,
            'forum_topic_id' => $topic2->id,
        ]);
    }
}
