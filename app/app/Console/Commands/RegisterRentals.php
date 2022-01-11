<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Models\Reservation;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegisterRentals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register new rentals according to the reservations.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     * @return void
     */
    public function handle()
    {
        $target_date = Carbon::today();
        $reservations = Reservation::where('start_date', $target_date)->get();

        foreach($reservations as $reservation) {
            try {
                DB::beginTransaction();

                Rental::create([
                        'user_id' => $reservation->user_id,
                        'item_id' => $reservation->item_id,
                        'end_date' => $reservation->end_date,
                    ]
                );

                Reservation::find($reservation->id)->delete();

                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
            }
        }
    }
}
