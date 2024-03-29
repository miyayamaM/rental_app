<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/items');

        $response->assertStatus(200);
    }

    public function test_index_screen_cannot_be_rendered_to_guest_user()
    {
        $response = $this->get('/items');

        $response->assertRedirect('/login');
    }

    public function test_new_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/items/new');

        $response->assertStatus(200);
    }

    public function test_new_screen_cannot_be_rendered_to_guest_user()
    {
        $response = $this->get('/items/new');

        $response->assertRedirect('/login');
    }

    public function test_item_can_be_registered()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/items', [
            'name' => 'new item',
        ]);

        $this->assertDatabaseHas('items', [
            'name' => 'new item'
        ]);
        $response->assertRedirect(route('item.index'));
    }

    public function test_item_cannot_be_registered_by_guest_user()
    {
        $response = $this->post('/items', [
            'name' => 'new item',
        ]);

        $this->assertDatabaseMissing('items', [
            'name' => 'new item'
        ]);
        $response->assertRedirect('/login');
    }

    public function test_show_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->get(route('item.show', ['id' => $item->id]));

        $response->assertStatus(200);
    }

    public function test_show_screen_cannot_be_rendered_to_guest_user()
    {
        $item = Item::factory()->create();
        $response = $this->get(route('item.show', ['id' => $item->id]));

        $response->assertRedirect('/login');
    }

    public function test_存在しない物品詳細画面へのアクセスは404を返す()
    {
        $user = User::factory()->create();
        $non_existent_id = Item::max('id') + 1;
        $response = $this->actingAs($user)->get(route('item.show', ['id' => $non_existent_id]));

        $response->assertStatus(404);
    }

    public function test_edit_screen_can_be_rendered()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->get(route('item.edit', ['id' => $item->id]));

        $response->assertStatus(200);
    }

    public function test_edit_screen_cannot_be_rendered_to_guest_user()
    {
        $item = Item::factory()->create();
        $response = $this->get(route('item.edit', ['id' => $item->id]));

        $response->assertRedirect('/login');
    }

    public function test_存在しない物品編集画面へのアクセスは404を返す()
    {
        $user = User::factory()->create();
        $non_existent_id = Item::max('id') + 1;
        $response = $this->actingAs($user)->get(route('item.edit', ['id' => $non_existent_id]));

        $response->assertStatus(404);
    }

    public function test_name_can_be_changed()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->put(route('item.update', ['id' => $item->id, 'name' => 'updated name']));

        $this->assertSame(Item::find($item->id)->name, 'updated name');
        $response->assertRedirect(route('item.index'));
    }

    public function test_name_cannot_be_changed_by_guest_user()
    {
        $item = Item::factory()->create();
        $response = $this->put(route('item.update', ['id' => $item->id, 'name' => 'updated name']));

        $this->assertNotSame(Item::find($item->id)->name, 'updated name');
        $response->assertRedirect('/login');
    }

    public function test_存在しない物品の編集は404を返す()
    {
        $user = User::factory()->create();
        $non_existent_id = Item::max('id') + 1;
        $response = $this->actingAs($user)->put(route('item.update', ['id' => $non_existent_id, 'name' => 'updated name']));

        $response->assertStatus(404);
    }

    public function test_item_can_be_deleted()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->delete(route('item.destroy', ['id' => $item->id]));

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
        $response->assertRedirect(route('item.index'));
    }

    public function test_item_cannot_be_deleted_by_guest_user()
    {
        $item = Item::factory()->create();
        $response = $this->delete(route('item.destroy', ['id' => $item->id]));

        $this->assertDatabaseHas('items', ['id' => $item->id]);
        $response->assertRedirect('/login');
    }

    public function test_存在しない物品の削除は404を返す()
    {
        $user = User::factory()->create();
        $non_existent_id = Item::max('id') + 1;
        $response = $this->actingAs($user)->delete(route('item.destroy', ['id' => $non_existent_id]));

        $response->assertStatus(404);
    }
}
