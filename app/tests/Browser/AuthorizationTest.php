<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthorizationTest extends DuskTestCase
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

    public function test_ログイン画面からログイン()
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('#login')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Dashboard');
        });
    }

    public function test_存在しないユーザーでログインするとエラーメッセージを表示()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('#login')
                    ->assertSee('該当するユーザーが存在しません');
        });
    }

    public function test_連続してログインを失敗するとエラーメッセージを表示()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('#login')
                    ->type('password', 'password')
                    ->press('#login')
                    ->type('password', 'password')
                    ->press('#login')
                    ->type('password', 'password')
                    ->press('#login')
                    ->type('password', 'password')
                    ->press('#login')
                    ->assertSee('ログイン試行回数が多すぎます。');
        });
    }

    public function test_ログアウトする()
    {
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@myname_on_navigation')
                    ->click('@logout')
                    ->assertPathIs('/login')
                    ->assertSee('パスワードを忘れた場合');
        });
    }
}
