<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterRentalBatchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $another_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->hasAttached(
                Item::factory()->count(1),
                [
                    'start_date' => Carbon::today(),
                    'end_date' => Carbon::today()->addDay(10),
                ],
                'reservations',
            )
            ->create();

        $this->another_user = User::factory()
            ->hasAttached(
                Item::factory()->count(1),
                [
                    'start_date' =>  Carbon::today()->addDay(3),
                    'end_date' => Carbon::today()->addDay(10),
                ],
                'reservations',
            )
            ->create();
    }

    public function test_予約レコードに基づいて貸出が登録される()
    {
        $item_id = $this->user->reservations->first()->id;
        $this->artisan('rentals:register');

        $this->assertDatabaseHas('rentals', [
            'user_id' => $this->user->id,
            'item_id' => $item_id,
            'end_date' => Carbon::today()->addDay(10),
        ]);

        $this->assertSoftDeleted('reservations', [
            'user_id' => $this->user->id,
            'item_id' => $item_id,
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDay(10),
        ]);
    }

    public function test_対象日以外の予約は登録されない()
    {
        $item_id = $this->another_user->reservations->first()->id;
        $this->artisan('rentals:register');

        $this->assertDatabaseMissing('rentals', [
            'user_id' => $this->another_user->id,
            'item_id' => $item_id,
            'end_date' => Carbon::today()->addDay(10),
        ]);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->another_user->id,
            'item_id' => $item_id,
            'start_date' => Carbon::today()->addDay(3),
            'end_date' => Carbon::today()->addDay(10),
        ]);
    }
}
