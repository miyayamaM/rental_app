<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Reservation
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $item_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 */
class Reservation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'item_id',
        'start_date',
        'end_date',
    ];

    public static function checkNoOverlapWithReservations($item_id, $start_date, $end_date): bool
    {
        $overlap_counts = Reservation::where('item_id', $item_id)
                    ->where('start_date', '<', $end_date)
                    ->where('end_date', '>', $start_date)
                    ->count();

        return $overlap_counts > 0;
    }

    public static function checkNoOverlapWithRentals($item_id, $start_date): bool
    {
        $overlap_counts = Rental::where('item_id', $item_id)
            ->where('end_date', '>', $start_date)
            ->count();

        return $overlap_counts > 0;
    }
}
