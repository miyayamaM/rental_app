<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_index_screen_can_be_rendered()
    {
        $response = $this->get('/items');

        $response->assertStatus(200);
    }

    public function test_items_new_screen_can_be_rendered()
    {
        $response = $this->get('/items/new');

        $response->assertStatus(200);
    }

    public function test_new_item_can_be_registered()
    {
        $response = $this->post('/items',[
            'name' => 'new item',
        ]);

        $this->assertDatabaseHas('items',[
            'name' => 'new item'
        ]);
        $response->assertredirect(route('item.index'));
    }

    public function test_item_show_screen_can_be_rendered()
    {   
        $item = Item::factory()->create();
        $response = $this->get(route('item.show', ['id' => $item->id]));

        $response->assertStatus(200);
    }

    public function test_item_edit_screen_can_be_rendered()
    {   
        $item = Item::factory()->create();
        $response = $this->get(route('item.edit', ['id' => $item->id]));

        $response->assertStatus(200);
    }

    public function test_item_name_can_be_changed()
    {   
        $item = Item::factory()->create();
        $response = $this->put(route('item.update', ['id' => $item->id, 'name' => 'updated name']));

        $this->assertsame(Item::find($item->id)->name, 'updated name');
        $response->assertredirect(route('item.show', ['id' => $item->id]));
    }   
    
    public function test_item_can_be_deleted()
    {   
        $item = Item::factory()->create();
        $response = $this->delete(route('item.destroy', ['id' => $item->id]));
        
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
        $response->assertredirect(route('item.index'));
    }   
}
