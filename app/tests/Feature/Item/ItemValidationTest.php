<?php

namespace Tests\Feature\Item;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemValidationTest extends TestCase
{   
    use RefreshDatabase;
    
    //物品新規登録(POST)のテスト
    public function test_blank_name_cannot_be_registered()
    {   
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/items',[
            'name' => '',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('items',[
            'name' => ''
        ]);
    }

    public function test_name_must_be_string()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/items',[
            'name' => 40,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('items',[
            'name' => 40
        ]);
    }

    public function test_name_with_255_chars_can_be_registered()
    {     
        $user = User::factory()->create();
        $item_name = str_repeat('a', 255);

        $response = $this->actingAs($user)->post('/items',[
            'name' => $item_name,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('items',[
            'name' => $item_name
        ]);
    }

    public function test_name_over_255_chars_cannot_be_registered()
    {     
        $user = User::factory()->create();
        $item_name = str_repeat('a', 256);

        $response = $this->actingAs($user)->post('/items',[
            'name' => $item_name,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('items',[
            'name' => $item_name
        ]);
    }

    //物品編集(PUT)のテスト
    public function test_name_cannot_be_editted_to_blank()
    {   
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->put(route('item.update', ['id' => $item->id, 'name' => '']));

        $response->assertStatus(302);
        $this->assertsame(Item::find($item->id)->name, $item->name);
    }

    //route()ヘルパを使うとintがstringに変換されてしまうので、ここだけパスを直接指定
    public function test_name_cannot_be_editted_to_number()
    {   
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $id = (string) $item->id;
        $response = $this->actingAs($user)->put("items/".$id, ['name' => 40]);

        $response->assertStatus(302);
        $this->assertsame(Item::find($item->id)->name, $item->name);
    }

    public function test_name_can_be_editted_to__255_chars()
    {   
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $item_name = str_repeat('a', 255);
        $response = $this->actingAs($user)->put(route('item.update', ['id' => $item->id, 'name' => $item_name]));

        $response->assertStatus(302);
        $this->assertsame(Item::find($item->id)->name, $item_name);
    }

    public function test_name_cannot_be_editted_to_more_than_255_chars()
    {   
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $item_name = str_repeat('a', 256);
        $response = $this->actingAs($user)->put(route('item.update', ['id' => $item->id, 'name' => $item_name]));

        $response->assertStatus(302);
        $this->assertsame(Item::find($item->id)->name, $item->name);
    }
}
