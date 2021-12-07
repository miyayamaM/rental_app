<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserRegistrationTest extends DuskTestCase
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

    public function test_名前を最大文字数を超えて登録するとエラーメッセージを表示()
    {   
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', str_repeat('a', 256))
                    ->type('email', "test@example.com")
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('登録')
                    ->assertSee('ユーザー名には255文字以下の文字列を指定してください。');
        });
    }

    public function test_メールアドレスを最大文字数を超えて登録するとエラーメッセージを表示()
    {   
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'test_user')
                    ->type('email', str_repeat('a', 256). '@example.com')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('登録')
                    ->assertSee('メールアドレスには255文字以下の文字列を指定してください。');
        });
    }

    public function test_既にあるメールアドレスを登録するとエラーメッセージを表示()
    {   
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/register')
                    ->type('name', 'another_user')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('登録')
                    ->assertSee('そのメールアドレスはすでに使われています。');
        });
    }

    public function test_パスワードが確認用と異なるとエラーメッセージを表示()
    {   
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'another_user')
                    ->type('email', "test@example.com")
                    ->type('password', 'password')
                    ->type('password_confirmation', 'different_password')
                    ->press('登録')
                    ->assertSee('パスワードが確認用の値と一致しません。');
        });
    }

    public function test_パスワードが短すぎるとエラーメッセージを表示()
    {   
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'another_user')
                    ->type('email', "test@example.com")
                    ->type('password', 'p')
                    ->type('password_confirmation', 'p')
                    ->press('登録')
                    ->assertSee('パスワードには8文字以上の文字列を指定してください。');
        });
    }
}
