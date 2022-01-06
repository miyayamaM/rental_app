<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Item;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReservationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->item = Item::factory()->create();
        Reservation::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' => Carbon::today()->addDay(11),
            'end_date' => Carbon::today()->addDay(15)
        ]);

        Reservation::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' => Carbon::today()->addDay(21),
            'end_date' => Carbon::today()->addDay(25)
        ]);
    }

    public function test_物品の予約を登録する()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post(
                             '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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
            ->post(
                '/reservations/items',
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

    public function test_返却予定日のみが重複する場合は予約できない()
    {
        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $this->item->id,
                    'start_date' =>  Carbon::today()->addDay(1),
                    'end_date' => Carbon::today()->addDay(13)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' =>  Carbon::today()->addDay(1),
            'end_date' => Carbon::today()->addDay(13)
        ]);
    }

    public function test_貸出予定日のみが重複する場合は予約できない()
    {
        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $this->item->id,
                    'start_date' =>  Carbon::today()->addDay(13),
                    'end_date' => Carbon::today()->addDay(18)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' =>  Carbon::today()->addDay(13),
            'end_date' => Carbon::today()->addDay(18)
        ]);
    }

    public function test_他の予約期間を含む期間では予約できない()
    {
        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $this->item->id,
                    'start_date' =>  Carbon::today()->addDay(1),
                    'end_date' => Carbon::today()->addDay(18)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' =>  Carbon::today()->addDay(1),
            'end_date' => Carbon::today()->addDay(18)
        ]);
    }

    public function test_他の予約期間に含まれる期間では予約できない()
    {
        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $this->item->id,
                    'start_date' =>  Carbon::today()->addDay(11),
                    'end_date' => Carbon::today()->addDay(12)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' =>  Carbon::today()->addDay(11),
            'end_date' => Carbon::today()->addDay(12)
        ]);
    }

    public function test_予約期間に挟まれた期間で予約できる()
    {
        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $this->item->id,
                    'start_date' =>  Carbon::today()->addDay(16),
                    'end_date' => Carbon::today()->addDay(20)
                ]
            );

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' =>  Carbon::today()->addDay(16),
            'end_date' => Carbon::today()->addDay(20)
        ]);
    }

    public function test_他の物品と予約期間が重複しても予約できる()
    {
        $item = Item::factory()->create();
        Reservation::create([
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::today()->addDay(10),
            'end_date' => Carbon::today()->addDay(20)
        ]);

        $another_item = Item::factory()->create();
        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $another_item->id,
                    'start_date' =>  Carbon::today()->addDay(7),
                    'end_date' => Carbon::today()->addDay(30)
                ]
            );

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $another_item->id,
            'start_date' =>  Carbon::today()->addDay(7),
            'end_date' => Carbon::today()->addDay(30)
        ]);
        $response->assertRedirect(route('reservations.new', ['id' => $another_item->id]));
    }

    public function test_返却予定日から予約できる()
    {
        $item = Item::factory()->create();
        Reservation::create([
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::today()->addDay(10),
            'end_date' => Carbon::today()->addDay(20)
        ]);

        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $item->id,
                    'start_date' =>  Carbon::today()->addDay(20),
                    'end_date' => Carbon::today()->addDay(30)
                ]
            );

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'start_date' =>  Carbon::today()->addDay(20),
            'end_date' => Carbon::today()->addDay(30)
        ]);
        $response->assertRedirect(route('reservations.new', ['id' => $item->id]));
    }

    public function test_現在の貸出期間と重複して予約はできない()
    {
        Rental::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'end_date' => Carbon::today()->addDay(5)
        ]);

        $response = $this->actingAs($this->user)
            ->post(
                '/reservations/items',
                [
                    'item_id' => $this->item->id,
                    'start_date' =>  Carbon::today()->addDay(2),
                    'end_date' => Carbon::today()->addDay(7)
                ]
            );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'start_date' =>  Carbon::today()->addDay(2),
            'end_date' => Carbon::today()->addDay(7)
        ]);
    }
}
