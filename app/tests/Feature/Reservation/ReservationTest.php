<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $another_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->hasAttached(
                Item::factory()->count(3),
                [
                    'start_date' => '2021-10-01',
                    'end_date' => '2021-11-01',
                ],
                'reservations',
            )
            ->create();

        $this->another_user = User::factory()
            ->hasAttached(
                Item::factory()->count(3),
                [
                    'start_date' => '2022-10-01',
                    'end_date' => '2022-11-01',
                ],
                'reservations',
            )
            ->create();
    }

    public function test_自分が予約している物品一覧を見れる()
    {
        $response = $this->actingAs($this->user)
                        ->get(route('user.reservations', ['id' => $this->user->id]));

        $response->assertStatus(200);
    }

    public function test_別のユーザーが予約している物品一覧を見れる()
    {
        $response = $this->actingAs($this->user)
                        ->get(route('user.reservations', ['id' => $this->another_user->id]));

        $response->assertStatus(200);
    }

    public function test_存在しないユーザーの予約照会は404を返す()
    {
        $non_existent_user_id = User::all()->max('id') + 1;
        $response = $this->actingAs($this->user)
            ->get(route('user.reservations', ['id' => $non_existent_user_id]));

        $response->assertStatus(404);
    }

    public function test_予約登録画面が表示される()
    {
        $item = $this->user->reservations->first();
        $response = $this->actingAs($this->user)
            ->get(route('reservations.new', ['id' => $item->id]));

        $response->assertStatus(200);
    }

    public function test_存在しない物品の予約登録画面は404を返す()
    {
        $non_existent_item_id = Item::all()->max('id') + 1;
        $response = $this->actingAs($this->user)
            ->get(route('reservations.new', ['id' => $non_existent_item_id]));

        $response->assertStatus(404);
    }

    public function test_予約をキャンセルする()
    {
        $reservation = Reservation::where('user_id', $this->user->id)->first();
        $response = $this->actingAs($this->user)
            ->delete(route('reservation.destroy', ['id' => $reservation->id]));

        $this->assertSoftDeleted('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('user.reservations', ['id' => $this->user->id]));
    }

    public function test_存在しない予約IDに対するリクエストは404を返す()
    {
        $non_existent_reservation_id = Reservation::max('id') + 1;
        $response = $this->actingAs($this->user)
            ->delete(route('reservation.destroy', ['id' => $non_existent_reservation_id]));

        $response->assertStatus(404);
    }

    public function test_他のユーザーの予約はキャンセルできない()
    {
        $reservation = Reservation::where('user_id', $this->another_user->id)->first();
        $response = $this->actingAs($this->user)
            ->delete(route('reservation.destroy', ['id' => $reservation->id]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'deleted_at' => null,
        ]);
    }
}
