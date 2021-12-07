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


}
