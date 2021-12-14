<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class RentalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $another_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->hasAttached(
                Item::factory()->count(3),
                ['end_date' => '2021-11-01']
            )
            ->create();

        $this->another_user = User::factory()
            ->hasAttached(
                Item::factory()->count(3),
                ['end_date' => '2020-01-01']
            )
            ->create();
    }

    public function test_自分が借りている物品一覧を見れる()
    {
        $response = $this->actingAs($this->user)
                        ->get(route('user.rentals', ['id' => $this->user->id]));

        $response->assertStatus(200);
    }

    public function test_別のユーザーが借りている物品一覧を見れる()
    {
        $response = $this->actingAs($this->user)
                        ->get(route('user.rentals', ['id' => $this->another_user->id]));

        $response->assertStatus(200);
    }

    public function test_存在しないユーザーの貸出照会は404を返す()
    {
        $non_exsitent_user_id = User::all()->max('id') + 1;
        $response = $this->actingAs($this->user)
                        ->get(route('user.rentals', ['id' => $non_exsitent_user_id]));

        $response->assertStatus(404);
    }

    public function test_物品の返却ができる()
    {
        $rental = Rental::where('user_id', $this->user->id)->first();
        $response = $this->actingAs($this->user)
                        ->delete(route('rental.destroy', ['id' => $rental->id]));

        $this->assertSoftDeleted('rentals', ['id' => $rental->id]);
        $response->assertRedirect(route('user.rentals', ['id' => $this->user->id]));
    }

    public function test_存在しない貸出IDに対するリクエストは404を返す()
    {
        $non_exsitent_rental_id = Rental::all()->max('id') + 1;
        $response = $this->actingAs($this->user)
                        ->delete(route('rental.destroy', ['id' => $non_exsitent_rental_id]));

        $response->assertStatus(404);
    }

    public function test_他人の物品の貸出は削除できない()
    {
        $rental = Rental::where('user_id', $this->another_user->id)->first();
        $response = $this->actingAs($this->user)
                        ->delete(route('rental.destroy', ['id' => $rental->id]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('rentals', [
            'id' => $rental->id,
            'deleted_at' => null,
        ]);
    }
}
