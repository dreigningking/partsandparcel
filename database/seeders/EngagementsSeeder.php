<?php

namespace Database\Seeders;

use App\Models\Discussion;
use App\Models\Listing;
use App\Models\Post;
use App\Models\User;
use App\Models\ViewedEntity;
use App\Models\Watchlist;
use Illuminate\Database\Seeder;

class EngagementsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->count() < 3) {
            $this->call(DemoUsersSeeder::class);
            $users = User::all();
        }

        $listings = Listing::with('item')->get();
        if ($listings->isEmpty()) {
            $this->call(DemoItemsAndListingsSeeder::class);
            $listings = Listing::with('item')->get();
        }

        $posts = Post::all();
        if ($posts->isEmpty()) {
            $this->call(PostsSeeder::class);
            $posts = Post::all();
        }

        $discussions = Discussion::all();
        if ($discussions->isEmpty()) {
            $this->call(DemoRequestAndResponseSeeder::class);
            $discussions = Discussion::all();
        }

        $deviceTypes = ['mobile', 'desktop', 'tablet'];
        $userAgents = [
            'mobile' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
            'desktop' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
            'tablet' => 'Mozilla/5.0 (iPad; CPU OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
        ];

        // 1. Seed Viewed Entities
        // STRICT RULE: A user is NOT viewing his own viewable (listing, post, discussion, user profile)
        foreach ($users as $viewer) {
            $deviceType = $deviceTypes[array_rand($deviceTypes)];
            $ip = '102.89.' . rand(10, 250) . '.' . rand(2, 250);
            $userAgent = $userAgents[$deviceType];

            // (a) View other users' listings
            $otherListings = $listings->filter(fn($l) => $l->user_id !== $viewer->id)->shuffle()->take(3);
            foreach ($otherListings as $listing) {
                ViewedEntity::updateOrCreate(
                    [
                        'user_id' => $viewer->id,
                        'viewable_id' => $listing->id,
                        'viewable_type' => 'listing',
                    ],
                    [
                        'ip_address' => $ip,
                        'user_agent' => $userAgent,
                        'device_type' => $deviceType,
                    ]
                );
            }

            // (b) View other users' posts
            $otherPosts = $posts->filter(fn($p) => $p->user_id !== $viewer->id)->shuffle()->take(2);
            foreach ($otherPosts as $post) {
                ViewedEntity::updateOrCreate(
                    [
                        'user_id' => $viewer->id,
                        'viewable_id' => $post->id,
                        'viewable_type' => 'post',
                    ],
                    [
                        'ip_address' => $ip,
                        'user_agent' => $userAgent,
                        'device_type' => $deviceType,
                    ]
                );
            }

            // (c) View other users' discussions
            $otherDiscussions = $discussions->filter(fn($d) => $d->user_id !== $viewer->id)->shuffle()->take(2);
            foreach ($otherDiscussions as $discussion) {
                ViewedEntity::updateOrCreate(
                    [
                        'user_id' => $viewer->id,
                        'viewable_id' => $discussion->id,
                        'viewable_type' => 'discussion',
                    ],
                    [
                        'ip_address' => $ip,
                        'user_agent' => $userAgent,
                        'device_type' => $deviceType,
                    ]
                );
            }

            // (d) View other users' profiles (user profile view)
            $otherProfiles = $users->filter(fn($u) => $u->id !== $viewer->id)->shuffle()->take(2);
            foreach ($otherProfiles as $targetProfile) {
                ViewedEntity::updateOrCreate(
                    [
                        'user_id' => $viewer->id,
                        'viewable_id' => $targetProfile->id,
                        'viewable_type' => 'user',
                    ],
                    [
                        'ip_address' => $ip,
                        'user_agent' => $userAgent,
                        'device_type' => $deviceType,
                    ]
                );
            }
        }

        // 2. Seed Watchlists
        // STRICT RULE: A user is NOT watching his own watchable (listing, post, discussion)
        foreach ($users as $watcher) {
            // (a) Watch other users' listings
            $otherListings = $listings->filter(fn($l) => $l->user_id !== $watcher->id)->shuffle()->take(2);
            foreach ($otherListings as $listing) {
                Watchlist::updateOrCreate(
                    [
                        'user_id' => $watcher->id,
                        'watchable_id' => $listing->id,
                        'watchable_type' => 'listing',
                    ]
                );
            }

            // (b) Watch other users' posts
            $otherPosts = $posts->filter(fn($p) => $p->user_id !== $watcher->id)->shuffle()->take(1);
            foreach ($otherPosts as $post) {
                Watchlist::updateOrCreate(
                    [
                        'user_id' => $watcher->id,
                        'watchable_id' => $post->id,
                        'watchable_type' => 'post',
                    ]
                );
            }

            // (c) Watch other users' community discussions
            $otherDiscussions = $discussions->filter(fn($d) => $d->user_id !== $watcher->id)->shuffle()->take(1);
            foreach ($otherDiscussions as $discussion) {
                Watchlist::updateOrCreate(
                    [
                        'user_id' => $watcher->id,
                        'watchable_id' => $discussion->id,
                        'watchable_type' => 'discussion',
                    ]
                );
            }
        }
    }
}
