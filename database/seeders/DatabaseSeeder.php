<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use App\Models\ReleaseStore;
use App\Models\Royalty;
use App\Models\Withdrawal;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admins
        $admin = User::updateOrCreate(
            ['email' => 'delfinusideusdedith@gmail.com'],
            [
                'name' => 'Delfinusi Deusdedith',
                'phone' => '+255700000000',
                'password' => Hash::make('deli@123!'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );

        $systemAdmin = User::updateOrCreate(
            ['email' => 'admin@collegemusic.com'],
            [
                'name' => 'System Administrator',
                'phone' => '+1112223333',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'system_setup',
            'description' => 'System administrator account generated during initialization.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console Seeder'
        ]);

        // 2. Create Artists
        $artistUser = User::create([
            'name' => 'John Legend Artist',
            'email' => 'artist@collegemusic.com',
            'phone' => '+4445556666',
            'password' => Hash::make('password'),
            'role' => 'artist',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        $artistProfile = Artist::create([
            'user_id' => $artistUser->id,
            'name' => 'John Legend',
            'bio' => 'An award-winning singer-songwriter known for beautiful piano melodies, deep vocals, and soul-stirring ballads.',
            'contact_info' => 'booking@johnlegend.com',
            'verification_status' => 'verified', // Verified artist
            'social_links' => [
                'facebook' => 'https://facebook.com/johnlegend',
                'twitter' => 'https://x.com/johnlegend',
                'instagram' => 'https://instagram.com/johnlegend',
                'spotify' => 'https://open.spotify.com/artist/53A0mJ14UNjhSccm8Vpt2g'
            ],
            'verification_documents' => [
                [
                    'type' => 'Passport',
                    'path' => 'verifications/passport_sample.jpg',
                    'submitted_at' => now()->subDays(10)->toDateTimeString()
                ]
            ]
        ]);

        AuditLog::create([
            'user_id' => $artistUser->id,
            'action' => 'artist_verified',
            'description' => 'Artist profile has been verified by the administrator.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console Seeder'
        ]);

        // 3. Create Record Labels
        $labelUser = User::create([
            'name' => 'Death Row Records',
            'email' => 'label@collegemusic.com',
            'phone' => '+7778889999',
            'password' => Hash::make('password'),
            'role' => 'record_label',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Create some managed artists under this label
        $managedArtist1 = Artist::create([
            'user_id' => $labelUser->id,
            'name' => 'Snoop Dogg',
            'bio' => 'West Coast rap legend and pop culture icon, delivering laid-back flows since the 90s.',
            'contact_info' => 'mgmt@deathrow.com',
            'verification_status' => 'verified',
        ]);

        $managedArtist2 = Artist::create([
            'user_id' => $labelUser->id,
            'name' => 'Tupac Shakur',
            'bio' => 'One of the most influential rappers of all time, poet, and actor.',
            'contact_info' => 'legal@deathrow.com',
            'verification_status' => 'pending', // Pending verification
        ]);

        // 4. Create Distributors
        $distributorUser = User::create([
            'name' => 'Global Distribution Inc.',
            'email' => 'distributor@collegemusic.com',
            'phone' => '+8889990000',
            'password' => Hash::make('password'),
            'role' => 'distributor',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // 5. Create Releases under Artist (John Legend)
        // Release 1: Distributed (Paid)
        $release1 = Release::create([
            'artist_id' => $artistProfile->id,
            'title' => 'All of Me',
            'type' => 'single',
            'cover_image' => 'covers/sample_cover_1.jpg',
            'genre' => 'R&B/Soul',
            'language' => 'English',
            'release_date' => now()->subMonths(6),
            'copyright_info' => '℗ 2026 John Legend Music',
            'scheduling_type' => 'immediate',
            'distribution_status' => 'distributed',
            'billing_status' => 'paid',
            'price_paid' => 9.99,
        ]);

        // Create Payment Invoice for Release 1
        $invNum1 = 'INV-' . date('Ymd', strtotime('-180 days')) . '-' . sprintf('%04d', rand(1, 9999));
        Payment::create([
            'user_id' => $artistUser->id,
            'release_id' => $release1->id,
            'amount' => 9.99,
            'status' => 'completed',
            'payment_method' => 'card',
            'transaction_reference' => 'TX-' . Str::upper(Str::random(12)),
            'invoice_number' => $invNum1,
            'created_at' => now()->subMonths(6),
        ]);

        // Create Stores for Release 1
        $stores = ['Spotify', 'Apple Music', 'YouTube Music', 'Deezer', 'Tidal'];
        foreach ($stores as $store) {
            ReleaseStore::create([
                'release_id' => $release1->id,
                'store_name' => $store,
                'status' => 'distributed',
            ]);
        }

        // Create Track for Release 1
        $track1 = Track::create([
            'release_id' => $release1->id,
            'title' => 'All of Me (Main Mix)',
            'artist_name' => 'John Legend',
            'composer' => 'John Legend',
            'songwriter' => 'John Legend',
            'isrc' => 'US-CM1-26-00001',
            'audio_file' => 'tracks/sample_audio.mp3',
            'duration' => 248,
            'streams_count' => 125000,
            'downloads_count' => 1400,
        ]);

        // Create Royalties for Track 1
        // Grouping streams & revenues across countries and platforms
        $platforms = [
            'Spotify' => ['rate' => 0.004, 'countries' => ['US' => 60000, 'GB' => 20000, 'CA' => 10000]],
            'Apple Music' => ['rate' => 0.007, 'countries' => ['US' => 15000, 'GB' => 5000, 'CA' => 5000]],
            'YouTube Music' => ['rate' => 0.002, 'countries' => ['NG' => 5000, 'JP' => 5000]],
        ];

        foreach ($platforms as $plat => $data) {
            foreach ($data['countries'] as $country => $streams) {
                Royalty::create([
                    'track_id' => $track1->id,
                    'platform' => $plat,
                    'amount' => $streams * $data['rate'],
                    'country' => $country,
                    'date' => date('Y-m', strtotime('-3 months')),
                    'streams_count' => $streams,
                ]);
            }
        }

        // Release 2: Pending (Awaiting review, Paid)
        $release2 = Release::create([
            'artist_id' => $artistProfile->id,
            'title' => 'Ordinary People',
            'type' => 'single',
            'cover_image' => 'covers/sample_cover_2.jpg',
            'genre' => 'R&B/Soul',
            'language' => 'English',
            'release_date' => now()->addDays(5),
            'copyright_info' => '℗ 2026 John Legend Music',
            'scheduling_type' => 'scheduled',
            'distribution_status' => 'pending',
            'billing_status' => 'paid',
            'price_paid' => 9.99,
        ]);

        $invNum2 = 'INV-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
        Payment::create([
            'user_id' => $artistUser->id,
            'release_id' => $release2->id,
            'amount' => 9.99,
            'status' => 'completed',
            'payment_method' => 'card',
            'transaction_reference' => 'TX-' . Str::upper(Str::random(12)),
            'invoice_number' => $invNum2,
        ]);

        foreach ($stores as $store) {
            ReleaseStore::create([
                'release_id' => $release2->id,
                'store_name' => $store,
                'status' => 'pending',
            ]);
        }

        Track::create([
            'release_id' => $release2->id,
            'title' => 'Ordinary People',
            'artist_name' => 'John Legend',
            'composer' => 'John Legend',
            'songwriter' => 'Will.i.am',
            'isrc' => 'US-CM1-26-00002',
            'audio_file' => 'tracks/sample_audio.mp3',
            'duration' => 281,
        ]);

        // Release 3: Rejected (Needs correction, unpaid)
        $release3 = Release::create([
            'artist_id' => $artistProfile->id,
            'title' => 'Green Light (Radio Edit)',
            'type' => 'single',
            'cover_image' => 'covers/sample_cover_3.jpg',
            'genre' => 'Pop',
            'language' => 'English',
            'release_date' => null,
            'copyright_info' => '℗ 2026 John Legend Records',
            'scheduling_type' => 'immediate',
            'distribution_status' => 'rejected',
            'rejection_reason' => 'Cover artwork resolution is low (blurry). Please upload a high-resolution 3000x3000px square cover art.',
            'billing_status' => 'unpaid',
            'price_paid' => 0.00,
        ]);

        foreach ($stores as $store) {
            ReleaseStore::create([
                'release_id' => $release3->id,
                'store_name' => $store,
                'status' => 'pending',
            ]);
        }

        Track::create([
            'release_id' => $release3->id,
            'title' => 'Green Light',
            'artist_name' => 'John Legend ft. Andre 3000',
            'composer' => 'John Legend',
            'songwriter' => 'Andre Benjamin',
            'isrc' => 'US-CM1-26-00003',
            'audio_file' => 'tracks/sample_audio.mp3',
            'duration' => 224,
        ]);

        // 6. Create Withdrawal History for John Legend (Artist)
        // Request 1: Completed Payout
        Withdrawal::create([
            'user_id' => $artistUser->id,
            'amount' => 120.00,
            'payment_method' => 'bank_transfer',
            'payment_details' => 'Chase Bank, Account: ****1234, Routing: 021000021, Holder: John Legend',
            'status' => 'completed',
            'invoice_number' => 'WD-' . date('Ymd', strtotime('-30 days')) . '-0001',
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(28),
        ]);

        AuditLog::create([
            'user_id' => $artistUser->id,
            'action' => 'withdrawal_completed',
            'description' => 'Withdrawal request for $120.00 approved and processed by administrator.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console Seeder',
            'created_at' => now()->subDays(28)
        ]);

        // Request 2: Pending Approval
        Withdrawal::create([
            'user_id' => $artistUser->id,
            'amount' => 50.00,
            'payment_method' => 'paypal',
            'payment_details' => 'paypal-receipts@johnlegend.com',
            'status' => 'pending',
            'invoice_number' => 'WD-' . date('Ymd') . '-0002',
        ]);

        AuditLog::create([
            'user_id' => $artistUser->id,
            'action' => 'request_withdrawal',
            'description' => 'Requested a royalty withdrawal of $50.00 via paypal.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console Seeder'
        ]);

        // 7. Premium Subscription for Distributor User to show free billing status
        $subStarts = now()->subMonths(2);
        $subEnds = now()->addMonths(10);
        $sub = Subscription::create([
            'user_id' => $distributorUser->id,
            'plan_name' => 'Premium',
            'price' => 49.99,
            'status' => 'active',
            'starts_at' => $subStarts,
            'ends_at' => $subEnds,
        ]);

        Payment::create([
            'user_id' => $distributorUser->id,
            'subscription_id' => $sub->id,
            'amount' => 49.99,
            'status' => 'completed',
            'payment_method' => 'card',
            'transaction_reference' => 'TX-' . Str::upper(Str::random(12)),
            'invoice_number' => 'INV-' . date('Ymd', strtotime('-60 days')) . '-9999',
            'created_at' => $subStarts,
        ]);

        AuditLog::create([
            'user_id' => $distributorUser->id,
            'action' => 'subscribe_premium',
            'description' => 'Subscribed to Premium plan ($49.99/year).',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console Seeder',
            'created_at' => $subStarts
        ]);
    }
}
