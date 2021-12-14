<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Item
 *
 * @property integer $id
 * @property string $name
 * @property \Illuminate\Database\Eloquent\Collection $users
 */
class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'rentals')->whereNull('rentals.deleted_at');
    }

    public function isRentable()
    {
        return $this->users->isEmpty();
    }

    public function rentalEndDate()
    {
        return $this->isRentable() ? null : Rental::where('item_id', $this->id)->select('end_date')->first()->end_date;
    }
}
