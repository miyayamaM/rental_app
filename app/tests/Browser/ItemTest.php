<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        // テストケースごとにログアウトするためクッキーを消す
        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }

    public function test_see_item_lists()
    {   
        $user = User::factory()->create();
        foreach(['itemA', 'itemB', 'itemC'] as $item_name) {
            Item::factory()->create(['name' => $item_name]);
        }
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find(1))
                    ->visit('/dashboard')
                    ->click('@itemlist_on_navigation')
                    ->assertPathIs('/items')
                    ->assertSee('itemA')
                    ->assertSee('itemB')
                    ->assertSee('itemC');
        });
    }

    public function test_register_new_item()
    {   
        $user = User::factory()->create();
        foreach(['itemA', 'itemB', 'itemC'] as $item_name) {
            Item::factory()->create(['name' => $item_name]);
        }
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find(1))
                    ->visit('/dashboard')
                    ->click('@item_register_on_navigation')
                    ->type('name', 'itemD')
                    ->press('登録する')
                    ->assertPathIs('/items')
                    ->assertSee('itemD');
        });
    }

    public function test_edit_item_name()
    {   
        $user = User::factory()->create();
        foreach(['itemA', 'itemB', 'itemC'] as $item_name) {
            Item::factory()->create(['name' => $item_name]);
        }
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find(1))
                    ->visit('/items')
                    ->click('@edit_link_1')
                    ->assertPathIs('/items/1/edit')
                    ->assertSee('itemA')
                    ->type('name', 'itemA_changed')
                    ->press('編集する')
                    ->assertSee('itemA_changed')
                    ->visit('/items')
                    ->assertSee('itemA_changed');
        });
    }
}
