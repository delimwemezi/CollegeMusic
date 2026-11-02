<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Release;
use App\Models\ReleaseStore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTakedownTest extends TestCase
{
    use RefreshDatabase;

    public function test_distributed_platforms_can_be_removed_individually(): void
    {
        $artistUser = User::factory()->create();
        $artist = Artist::create([
            'user_id' => $artistUser->id,
            'name' => 'Test Artist',
        ]);
        $release = Release::create([
            'artist_id' => $artist->id,
            'title' => 'Distributed Single',
            'type' => 'single',
            'genre' => 'Pop',
            'language' => 'English',
            'copyright_info' => 'Test Copyright',
            'distribution_status' => 'distributed',
            'billing_status' => 'paid',
        ]);
        $spotify = ReleaseStore::create([
            'release_id' => $release->id,
            'store_name' => 'Spotify',
            'status' => 'distributed',
        ]);
        $appleMusic = ReleaseStore::create([
            'release_id' => $release->id,
            'store_name' => 'Apple Music',
            'status' => 'distributed',
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($artistUser)
            ->post(route('releases.stores.takedown', [$release, $spotify]))
            ->assertRedirect();

        $this->assertDatabaseHas('release_store', [
            'id' => $spotify->id,
            'status' => 'pending_takedown',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.releases.review', $release), [
                'action' => 'process_store_takedown',
                'store_id' => $spotify->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('release_store', [
            'id' => $spotify->id,
            'status' => 'removed',
        ]);
        $this->assertDatabaseHas('releases', [
            'id' => $release->id,
            'distribution_status' => 'distributed',
        ]);

        $this->actingAs($artistUser)
            ->post(route('releases.stores.takedown', [$release, $appleMusic]))
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.releases.review', $release), [
                'action' => 'process_store_takedown',
                'store_id' => $appleMusic->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('release_store', [
            'id' => $appleMusic->id,
            'status' => 'removed',
        ]);
        $this->assertDatabaseHas('releases', [
            'id' => $release->id,
            'distribution_status' => 'removed',
        ]);
    }
}