<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\GameSetting;

class SlotMachineLogicTest extends TestCase
{
    use RefreshDatabase;

    private function makePlayerWithSetting(string $setting): User
    {
        $player = User::factory()->create([
            'role'    => 'player',
            'balance' => 50000,
        ]);

        GameSetting::create([
            'user_id'          => $player->id,
            'next_spin_result' => $setting,
        ]);

        // Seed deposit/spin log sebelumnya agar ada revenue admin untuk guard 60% payout
        \App\Models\SpinLog::create([
            'user_id'           => $player->id,
            'bet_amount'        => 500000,
            'result_amount'     => 0,
            'status_manipulasi' => 'Pasti Rungkad / Zonk',
            'multiplier'        => 0,
            'is_free_spin'      => false,
            'grid_pattern'      => ['💎', '💎', '💎'],
        ]);

        return $player;
    }

    /** Mode lose menghasilkan zonk dengan near miss & tanpa winning lines */
    public function test_lose_mode_returns_near_miss_and_zero_win(): void
    {
        $player = $this->makePlayerWithSetting('lose');

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'        => true,
            'win_amount'     => 0,
            'multiplier'     => 0,
            'paylines_count' => 10,
            'winning_lines'  => [],
        ]);

        $this->assertCount(15, $response->json('grid'));
        $this->assertIsBool($response->json('near_miss'));
    }

    /** Mode win menghasilkan winning lines dan perkalian jackpot */
    public function test_win_mode_returns_winning_lines_and_jackpot(): void
    {
        $player = $this->makePlayerWithSetting('win');

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertGreaterThan(0, $response->json('win_amount'));
        $this->assertNotEmpty($response->json('winning_lines'));
        $this->assertCount(15, $response->json('grid'));
    }

    /** Mode random_low_win menghasilkan winning lines pancingan */
    public function test_low_win_mode_returns_small_win_paylines(): void
    {
        $player = $this->makePlayerWithSetting('random_low_win');

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertGreaterThan(0, $response->json('win_amount'));
        $this->assertNotEmpty($response->json('winning_lines'));
        $this->assertEquals(1, $response->json('winning_lines.0.line_number'));
    }
}
