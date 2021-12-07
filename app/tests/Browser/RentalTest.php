<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RentalTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        // テストケースごとにログアウトするためクッキーを消す
        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }

        $this->user = User::factory()
        ->hasAttached(
            Item::factory()->count(3),
            ['end_date' => '2021-11-01']
        )
        ->create();
    }

    public function test_貸出照会が表示される()
    {   
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->click('@rentallist_on_navigation')
                    ->assertRouteIs('user.rentals', ['id' => $this->user->id])
                    ->assertSee('あなたが借りている物品')
                    ->assertSee($this->user->items->first()->name)
                    ->assertSee('2021-11-01');
        });
    }

    public function test_物品の貸出状況が表示される()
    {   
        Item::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->click('@itemlist_on_navigation')
                    ->assertPathIs('/items')
                    ->assertSee('貸出可')
                    ->assertSee('貸出中');
        });
    }

    public function test_貸出中の物品は編集・削除ボタンが表示されない()
    {   
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->click('@itemlist_on_navigation')
                    ->assertPathIs('/items')
                    ->assertSee('貸出中')
                    ->assertDontSee('編集する')
                    ->assertDontSee('削除する');
        });
    }

    public function test_貸出可能な物品詳細ページが表示()
    {
        $item = Item::factory()->create(["name" => "rentable_item"]);
        $this->browse(function (Browser $browser) use ($item) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->click('@show_link_'. $item->id)
                    ->assertSee('貸出可');
        });
    }

    public function test_貸出中の物品詳細ページが表示()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->click('@show_link_1')
                    ->assertSee('状況： 貸出中')
                    ->assertSee('貸出者： '. $this->user->name)
                    ->assertDontSee('返却予定日： 2020-11-01');
        });
    }

    public function test_物品の返却をする()
    {   
        $item = $this->user->items->first();
        $this->browse(function (Browser $browser) use ($item) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->click('@rentallist_on_navigation')
                    ->assertSee($item->name)
                    ->click('@return_item_'. $item->pivot->id)
                    ->acceptDialog()
                    ->assertDontSee($item->name);
        });
    }

}
