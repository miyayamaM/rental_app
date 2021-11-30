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

    public function test_物品一覧が表示される()
    {   
        $user = User::factory()->create();
        foreach(['itemA', 'itemB', 'itemC'] as $item_name) {
            Item::factory()->create(['name' => $item_name]);
        }
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@itemlist_on_navigation')
                    ->assertPathIs('/items')
                    ->assertSee('itemA')
                    ->assertSee('itemB')
                    ->assertSee('itemC');
        });
    }

    public function test_新しい物品を登録する()
    {   
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@item_register_on_navigation')
                    ->type('name', 'itemD')
                    ->press('登録する')
                    ->assertPathIs('/items')
                    ->assertSee('itemD');
        });
    }

    public function test_空白を登録するとエラーメッセージを表示()
    {   
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/items/new')
                    ->type('name', '')
                    ->press('登録する')
                    ->assertPathIs('/items/new')
                    ->assertSee('名前は必須です');
        });
    }

    public function test_最大文字数を超えて登録するとエラーメッセージを表示()
    {   
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/items/new')
                    ->type('name', str_repeat('a', 256))
                    ->press('登録する')
                    ->assertPathIs('/items/new')
                    ->assertSee('名前には255文字以下の文字列を指定してください。');
        });
    }

    public function test_物品の名前を編集する()
    {   
        $user = User::factory()->create();
        Item::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
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

    public function test_空白で編集するとエラーメッセージを表示()
    {   
        $user = User::factory()->create();
        Item::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/items/1/edit')
                    ->type('name', '')
                    ->press('編集する')
                    ->assertPathIs('/items/1/edit')
                    ->assertSee('名前は必須です');
        });
    }

    public function test_最大文字数を超えて編集するとエラーメッセージを表示()
    {   
        $user = User::factory()->create();
        Item::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/items/1/edit')
                    ->type('name', str_repeat('a', 256))
                    ->press('編集する')
                    ->assertPathIs('/items/1/edit')
                    ->assertSee('名前には255文字以下の文字列を指定してください。');
        });
    }

    public function test_物品を削除する()
    {   
        $user = User::factory()->create();
        foreach(['itemA', 'itemB', 'itemC'] as $item_name) {
            Item::factory()->create(['name' => $item_name]);
        }
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/items')
                    ->click('@delete_link_1')
                    ->acceptDialog()
                    ->assertDontSee('itemA')
                    ->assertSee('itemB')
                    ->assertSee('itemC');
        });
    }
}
