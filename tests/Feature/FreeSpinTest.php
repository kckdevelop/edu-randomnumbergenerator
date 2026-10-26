<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\GameSetting;

/**
 * FreeSpinTest — menguji fitur "Spin Gratis" versi sederhana:
 * - Spin Gratis = toggle is_free_spin=true saat spin → saldo TIDAK dipotong
 * - Tidak ada kuota, tidak ada pembelian, tidak ada klaim
 */
class FreeSpinTest extends TestCase
{
    use RefreshDatabase;

    private function makePlayer(array $attrs = []): User
    {
        $player = User::factory()->create(array_merge([
            'role'    => 'player',
            'balance' => 10000,
        ], $attrs));

        GameSetting::create([
            'user_id'          => $player->id,
            'next_spin_result' => 'lose',
        ]);

        return $player;
    }

    /** Spin biasa (bukan gratis) memotong saldo */
    public function test_regular_spin_deducts_balance(): void
    {
        $player = $this->makePlayer(['balance' => 10000]);

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_free_spin' => false]);

        // Saldo harus berkurang (mode lose, win_amount = 0)
        $this->assertLessThan(10000, $player->fresh()->balance);
    }

    /** Spin Gratis (is_free_spin=true) TIDAK memotong saldo */
    public function test_free_spin_does_not_deduct_balance(): void
    {
        $player = $this->makePlayer(['balance' => 10000]);

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'            => true,
            'is_free_spin'       => true,
            'effective_bet_cost' => 0,
        ]);

        // Saldo tidak boleh berkurang
        $this->assertEquals(10000, (float) $player->fresh()->balance);
    }

    /** Spin Gratis mengabaikan kemenangan: saldo tetap utuh dan win_amount = 0 */
    public function test_free_spin_ignores_win_and_keeps_balance(): void
    {
        $player = $this->makePlayer(['balance' => 10000]);

        // Atur ke mode menang di manipulasi bandar
        $player->gameSetting()->update(['next_spin_result' => 'win']);

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'            => true,
            'is_free_spin'       => true,
            'effective_bet_cost' => 0,
            'win_amount'         => 0,
            'multiplier'         => 0,
        ]);

        // Saldo tetap utuh Rp 10.000 karena kemenangan diabaikan dan tidak ada taruhan
        $this->assertEquals(10000, (float) $player->fresh()->balance);
    }

    /** Spin Gratis dapat dilakukan berkali-kali (tidak ada batas kuota) */
    public function test_free_spin_can_be_done_multiple_times(): void
    {
        $player = $this->makePlayer(['balance' => 10000]);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($player)->postJson('/api/spin', [
                'bet_amount'   => 1000,
                'is_free_spin' => true,
            ]);
            $response->assertStatus(200);
            $response->assertJson(['success' => true, 'is_free_spin' => true]);
        }

        // Setelah 5x spin gratis (lose), saldo masih tetap Rp 10.000
        $this->assertEquals(10000, (float) $player->fresh()->balance);
    }

    /** Spin biasa memerlukan saldo cukup */
    public function test_regular_spin_fails_when_balance_insufficient(): void
    {
        $player = $this->makePlayer(['balance' => 500]);

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => false,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** Spin Gratis bisa dilakukan meski saldo nol */
    public function test_free_spin_works_with_zero_balance(): void
    {
        $player = $this->makePlayer(['balance' => 0]);

        $response = $this->actingAs($player)->postJson('/api/spin', [
            'bet_amount'   => 5000,
            'is_free_spin' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'            => true,
            'is_free_spin'       => true,
            'effective_bet_cost' => 0,
        ]);
    }
}
