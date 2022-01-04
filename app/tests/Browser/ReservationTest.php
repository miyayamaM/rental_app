<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReservationTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $another_user;

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
                [
                    'start_date' => '2021-10-01',
                    'end_date' => '2021-11-01',
                ],
                'reservations',
            )
            ->create();

        $this->another_user = User::factory()
            ->hasAttached(
                Item::factory()->count(3),
                [
                    'start_date' => '2022-10-01',
                    'end_date' => '2022-11-01',
                ],
                'reservations',
            )
            ->create();
    }

    public function test_自分の予約照会が表示される()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->click('@reservation_on_navigation')
                    ->assertRouteIs('user.reservations', ['id' => $this->user->id])
                    ->assertSee('あなたが予約している物品')
                    ->assertSee($this->user->reservations->first()->name)
                    ->assertSee('2021-10-01')
                    ->assertSee('2021-11-01');
        });
    }

    public function test_他ユーザーの予約照会が表示される()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->click('@reservation_on_navigation')
                    ->assertRouteIs('user.reservations', ['id' => $this->another_user->id])
                    ->assertSee($this->another_user->name . 'さんが予約している物品')
                    ->assertSee($this->another_user->reservations->first()->name)
                    ->assertSee('2022-10-01')
                    ->assertSee('2022-11-01');
        });
    }
}
