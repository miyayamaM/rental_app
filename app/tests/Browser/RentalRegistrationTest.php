<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;

class RentalRegistrationTest extends DuskTestCase
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

        $this->user = User::factory()->create();
    }

    public function test_物品の貸出を登録()
    {
        //FIXME: ブラウザテスト側のend_dateフォームの形式がmm/dd/yyyyになっている。
        //ローカルではyyyy/mm/ddなので統一する必要がある
        $return_date = Carbon::today();
        $item = Item::factory()->create(["name" => "rentable_item"]);
        $this->browse(function (Browser $browser) use ($item, $return_date) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->click('@show_link_' . $item->id)
                    ->assertSee('貸出可')
                    ->keys('#end_date', $return_date->month, $return_date->day, $return_date->year)
                    ->press('貸出する')
                    ->assertPathIs('/items')
                    ->assertSee('貸出中');
        });
    }

    public function test_返却予定日を指定しないとエラーメッセージを表示()
    {
        $item = Item::factory()->create(["name" => "rentable_item"]);
        $this->browse(function (Browser $browser) use ($item) {
            $browser->loginAs($this->user)
                    ->visit('/items')
                    ->click('@show_link_' . $item->id)
                    ->assertSee('貸出可')
                    ->press('貸出する')
                    ->assertSee('返却予定日は必須です。')
                    ->assertRouteIs('item.show', ['id' => $item->id ]);
        });
    }

    public function test_返却予定日が過去の日付だとエラーメッセージを表示()
    {
        //FIXME: ブラウザテスト側のend_dateフォームの形式がmm/dd/yyyyになっている。
        //ローカルではyyyy/mm/ddなので統一する必要がある
        $return_date = Carbon::yesterday();
        $item = Item::factory()->create(["name" => "rentable_item"]);
        $this->browse(function (Browser $browser) use ($item, $return_date) {
            $browser->loginAs($this->user)
            ->visit('/items')
            ->click('@show_link_' . $item->id)
            ->assertSee('貸出可')
            ->keys('#end_date', $return_date->month, $return_date->day, $return_date->year)
            ->press('貸出する')
            ->assertRouteIs('item.show', ['id' => $item->id ])
            ->assertSee('返却予定日には今日かそれ以降の日付を指定してください。');
        });
    }
}
