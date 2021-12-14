<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemsOnLoanTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->hasAttached(
                Item::factory(),
                ['end_date' => '2021-11-01']
            )
            ->create();
    }

    public function test_貸出中の物品編集画面にはアクセスできない()
    {
         $item = $this->user->items->first();

         $response = $this->actingAs($this->user)->get(route('item.edit', ['id' => $item->id]));

         $response->assertStatus(302);
         $response->assertRedirect('/items');
    }

    public function test_貸出中の物品名は変更できない()
    {
         $item = $this->user->items->first();

         $response = $this->actingAs($this->user)->put(route('item.update', ['id' => $item->id, 'name' => 'updated name']));

         $response->assertStatus(302);
         $this->assertNotSame(Item::find($item->id)->name, 'updated name');
    }

    public function test_貸出中の物品は削除できない()
    {
         $item = $this->user->items->first();

         $response = $this->actingAs($this->user)->delete(route('item.destroy', ['id' => $item->id]));

         $response->assertStatus(302);
         $this->assertDatabaseHas('items', ['id' => $item->id]);
    }
}
