<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class RentalTest extends TestCase
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
                ['end_date' => '2021-11-01']
            )
            ->create();

        $this->another_user = User::factory()
            ->hasAttached(
                Item::factory()->count(3),
                ['end_date' => '2020-01-01']
            )
            ->create();
    }

    public function test_物品の貸出を登録する()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => $item->id, 'end_date' => Carbon::today()]);

        $this->assertDatabaseHas('rentals', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'end_date' => Carbon::today()
        ]);
        $response->assertRedirect(route('item.index'));
    }

    public function test_物品IDが空白では登録できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => null, 'end_date' => Carbon::tomorrow()]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->user->id,
            'item_id' => null,
            'end_date' => Carbon::tomorrow()
        ]);
    }

    public function test_物品IDは整数以外登録できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => 'string', 'end_date' => Carbon::tomorrow()]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->user->id,
            'item_id' => 'string',
            'end_date' => Carbon::tomorrow()
        ]);
    }

    public function test_存在しない物品IDは登録できない()
    {
        $non_exsitent_item_id = Item::all()->max('id') + 1;
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => $non_exsitent_item_id, 'end_date' => Carbon::tomorrow()]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->user->id,
            'item_id' => $non_exsitent_item_id,
            'end_date' => Carbon::tomorrow()
        ]);
    }

    public function test_現在貸出中の物品IDは登録できない()
    {
        $rented_item_id = $this->another_user->items->first()->id;
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => $rented_item_id, 'end_date' => Carbon::tomorrow()]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->user->id,
            'item_id' => $rented_item_id,
            'end_date' => Carbon::tomorrow()
        ]);
    }

    public function test_返却予定日が空欄では登録できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => $item->id, 'end_date' => null]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_返却予定日が日付以外では登録できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => $item->id, 'end_date' => 'today']);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'end_date' => 'today'
        ]);
    }

    public function test_返却予定日が今日より前では登録できない()
    {
        $item = Item::factory()->create();
        $response = $this->actingAs($this->user)
                         ->post('/rentals', ['item_id' => $item->id, 'end_date' => Carbon::yesterday()]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'end_date' => Carbon::yesterday()
        ]);
    }
}
