<?php

namespace Tests\Browser;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Item;
use Carbon\Carbon;
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

    public function test_物品の予約状況が表示される()
    {
        $another_user = User::factory()->create();
        $item = $this->user->reservations->first();
        Reservation::create([
            'user_id' => $another_user->id,
            'item_id' => $item->id,
            'start_date' => Carbon::today()->addDay(2),
            'end_date' => Carbon::today()->addDay(5),
        ]);

        $this->browse(function (Browser $browser) use ($another_user, $item) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->click('@itemlist_on_navigation')
                ->click('@reservation_link_' . $item->id)
                ->assertSee('2021-10-01')
                ->assertSee('2021-11-01')
                ->assertSee($another_user->name)
                ->assertSee(Carbon::today()->addDay(2))
                ->assertSee(Carbon::today()->addDay(5));
        });
    }

    public function test_予約をキャンセルする()
    {
        $item = $this->user->reservations->first();
        $this->browse(function (Browser $browser) use ($item) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->click('@reservation_on_navigation')
                ->assertSee($item->name)
                ->click('@reservation_cancel_link_' . $item->pivot->id)
                ->assertDontSee($item->name);
        });
    }
}
