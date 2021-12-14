<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Rental
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $item_id
 * @property \Illuminate\Support\Carbon $end_date
 */
class Rental extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'item_id',
        'end_date',
    ];
}
