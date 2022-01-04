<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReservationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_物品の予約を登録する()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post('/reservations/items',
                             [
                                 'item_id' => $item->id,
                                 'start_date' => Carbon::tomorrow(),
                                 'end_date' => Carbon::today()->addDay(3)
                             ]
                         );

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::tomorrow(),
            'end_date' => Carbon::today()->addDay(3)
        ]);
        $response->assertRedirect(route('reservations.new', ['id' => $item->id]));
    }

    public function test_物品IDが空白では予約できない()
    {
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => null,
                    'start_date' => Carbon::tomorrow(),
                    'end_date' => Carbon::today()->addDay(3)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => null,
            'start_date' => Carbon::tomorrow(),
            'end_date' => Carbon::today()->addDay(3)
        ]);
    }

    public function test_物品IDが整数以外では予約できない()
    {
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => 'wrong_item_id',
                    'start_date' => Carbon::tomorrow(),
                    'end_date' => Carbon::today()->addDay(3)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => 'wrong_item_id',
            'start_date' => Carbon::tomorrow(),
            'end_date' => Carbon::today()->addDay(3)
        ]);
    }

    public function test_存在しない物品IDでは予約できない()
    {
        $non_existent_item_id = Item::max('id') + 1;
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => $non_existent_item_id,
                    'start_date' => Carbon::tomorrow(),
                    'end_date' => Carbon::today()->addDay(3)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $non_existent_item_id,
            'start_date' => Carbon::tomorrow(),
            'end_date' => Carbon::today()->addDay(3)
        ]);
    }

    public function test_貸出開始日が空白では予約できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => $item->id,
                    'start_date' => null,
                    'end_date' => Carbon::today()->addDay(3)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => null,
            'end_date' => Carbon::today()->addDay(3)
        ]);
    }

    public function test_貸出開始日がdate型以外では予約できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => $item->id,
                    'start_date' => 'wrong_type',
                    'end_date' => Carbon::today()->addDay(3)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => 'wrong_type',
            'end_date' => Carbon::today()->addDay(3)
        ]);
    }

    public function test_貸出開始日が今日では予約できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => $item->id,
                    'start_date' => Carbon::today(),
                    'end_date' => Carbon::today()->addDay(3)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDay(3)
        ]);
    }

    public function test_返却予定日が空白では予約できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => $item->id,
                    'start_date' =>  Carbon::tomorrow(),
                    'end_date' => null
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::tomorrow(),
            'end_date' => null
        ]);
    }

    public function test_返却予定日がdate型以外では予約できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => $item->id,
                    'start_date' =>  Carbon::tomorrow(),
                    'end_date' => 'wrong_date'
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::tomorrow(),
            'end_date' => 'wrong_date'
        ]);
    }

    public function test_返却予定日が貸出開始日より前では予約できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
            ->post('/reservations/items',
                [
                    'item_id' => $item->id,
                    'start_date' =>  Carbon::today()->addDay(20),
                    'end_date' => Carbon::today()->addDay(10)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::today()->addDay(20),
            'end_date' => Carbon::today()->addDay(10)
        ]);
    }
}
