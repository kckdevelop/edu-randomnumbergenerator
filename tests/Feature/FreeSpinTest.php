<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GameSetting;
use App\Models\SpinLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FreeSpinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_player_can_claim_free_spins(): void
    {
        $player = User::where('role', 'player')->first() ?? User::factory()->create([
            'role' => 'player',
            'balance' => 50000,
            'free_spins' => 0,
        ]);

        $player->free_spins = 0;
        $player->save();

        $response = $this->actingAs($player)->postJson('/claim-free-spins');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'free_spins' => 5,
        ]);

        $this->assertEquals(5, $player->fresh()->free_spins);
    }

    public function test_player_can_buy_free_spins(): void
    {
        $player = User::where('role', 'player')->first();
        $player->balance = 500000;
        $player->free_spins = 0;
        $player->save();

        $response = $this->actingAs($player)->postJson('/buy-free-spins', [
            'bet_amount' => 2000, // 100x = 200,000
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'free_spins' => 10,
        ]);

        $player->refresh();
        $this->assertEquals(300000, (float) $player->balance);
        $this->assertEquals(10, $player->free_spins);
    }

    public function test_player_can_perform_free_spin_without_losing_balance(): void
    {
        $player = User::where('role', 'player')->first();
        $player->balance = 10000;
        $player->free_spins = 3;
        $player->save();

        GameSetting::updateOrCreate(
            ['user_id' => $player->id],
            ['next_spin_result' => 'lose']
        );

        $response = $this->actingAs($player)->postJson('/spin', [
            'bet_amount' => 5000,
            'is_free_spin' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_free_spin' => true,
            'effective_bet_cost' => 0,
            'free_spins_left' => 2,
        ]);

        $player->refresh();
        // Balance remains unchanged on lose free spin (did not deduct 5000)
        $this->assertEquals(10000, (float) $player->balance);
        $this->assertEquals(2, $player->free_spins);

        $latestLog = SpinLog::where('user_id', $player->id)->latest()->first();
        $this->assertTrue((bool) $latestLog->is_free_spin);
        $this->assertEquals(0, (float) $latestLog->bet_amount);
    }

    public function test_free_spin_fails_when_quota_is_zero(): void
    {
        $player = User::where('role', 'player')->first();
        $player->free_spins = 0;
        $player->save();

        $response = $this->actingAs($player)->postJson('/spin', [
            'bet_amount' => 5000,
            'is_free_spin' => true,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_admin_can_add_free_spins_to_player(): void
    {
        $admin = User::where('role', 'admin')->first();
        $player = User::where('role', 'player')->first();
        $player->free_spins = 2;
        $player->save();

        $response = $this->actingAs($admin)->postJson("/admin/add-free-spins/{$player->id}", [
            'amount' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'free_spins' => 7,
        ]);

        $this->assertEquals(7, $player->fresh()->free_spins);
    }

    public function test_admin_can_bulk_add_free_spins(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->postJson('/admin/bulk-add-free-spins', [
            'amount' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}
