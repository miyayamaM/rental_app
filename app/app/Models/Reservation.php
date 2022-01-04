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
}
