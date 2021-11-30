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

    public function test_新規ユーザーを登録()
    {   
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', "test_user")
                    ->type('email', "test@example.com")
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('登録')
                    ->assertPathIs('/dashboard')
                    ->assertSee('test_user');
        });
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

    public function test_ログアウトする()
    {   
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs(User::find(1))
                    ->visit('/dashboard')
                    ->click('@myname_on_navigation')
                    ->click('@logout')
                    ->assertPathIs('/login')
                    ->assertSee('パスワードを忘れた場合');
        });
    }
}
