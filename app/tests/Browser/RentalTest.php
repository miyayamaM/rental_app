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
                    ->assertSee('itemA')
                    ->assertSee('2021-11-01');
        });
    }
}
