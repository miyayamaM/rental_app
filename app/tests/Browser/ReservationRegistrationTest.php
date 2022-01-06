<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReservationRegistrationTest extends DuskTestCase
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
                [
                    'start_date' => '2021-10-01',
                    'end_date' => '2021-11-01',
                ],
                'reservations',
            )
            ->create();
    }

    public function test_貸出の予約ができる()
    {
        $start_date = Carbon::today()->addDay(2);
        $end_date = Carbon::today()->addDay(5);
        $item = $this->user->reservations->first();

        $this->browse(function (Browser $browser) use ($item, $start_date, $end_date) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->click('@itemlist_on_navigation')
                ->click('@reservation_link_' . $item->id)
                ->keys('#start_date', sprintf('%02d', $start_date->month), sprintf('%02d', $start_date->day), $start_date->year)
                ->keys('#end_date', sprintf('%02d', $end_date->month), sprintf('%02d', $end_date->day), $end_date->year)
                ->press('予約する')
                ->assertSee($start_date)
                ->assertSee($end_date);
            ;
        });
    }

    public function test_日付が空欄だとエラーメッセージを表示()
    {
        $item = $this->user->reservations->first();

        $this->browse(function (Browser $browser) use ($item) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->click('@itemlist_on_navigation')
                ->click('@reservation_link_' . $item->id)
                ->press('予約する')
                ->assertSee('貸出開始日は必須です。')
                ->assertSee('返却予定日は必須です。');
        });
    }

    public function test_貸出予定日が今日より前だとエラーメッセージを表示()
    {
        $start_date = Carbon::today()->subDay(2);
        $end_date = Carbon::today()->addDay(5);
        $item = $this->user->reservations->first();

        $this->browse(function (Browser $browser) use ($item, $start_date, $end_date) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->click('@itemlist_on_navigation')
                ->click('@reservation_link_' . $item->id)
                ->keys('#start_date', sprintf('%02d', $start_date->month), sprintf('%02d', $start_date->day), $start_date->year)
                ->keys('#end_date', sprintf('%02d', $end_date->month), sprintf('%02d', $end_date->day), $end_date->year)
                ->press('予約する')
                ->assertSee('貸出開始日には今日以降の日付を指定してください。');
        });
    }

    //FIXME: ブラウザテスト側のdateフォームの形式がmm/dd/yyyyになっている。
    //ローカルではyyyy/mm/ddなので統一する必要がある
    public function test_返却予定日が貸出予定日より前だとエラーメッセージを表示()
    {
        $start_date = Carbon::today()->addDay(5);
        $end_date = Carbon::today()->addDay(2);
        $item = $this->user->reservations->first();

        $this->browse(function (Browser $browser) use ($item, $start_date, $end_date) {
            $browser->loginAs($this->user)
                ->visit('/dashboard')
                ->click('@itemlist_on_navigation')
                ->click('@reservation_link_' . $item->id)
                ->keys('#start_date', sprintf('%02d', $start_date->month), sprintf('%02d', $start_date->day), $start_date->year)
                ->keys('#end_date', sprintf('%02d', $end_date->month), sprintf('%02d', $end_date->day), $end_date->year)
                ->press('予約する')
                ->assertSee('返却予定日には貸出開始日以降の日付を指定してください。');
        });
    }
}
