<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Item
 *
 * @property integer $id
 * @property string $name
 * @property \Illuminate\Database\Eloquent\Collection $users
 * @property \Illuminate\Database\Eloquent\Collection $reservations
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

    /**
     * 現在の予約状況を取得
     *
     * @return  BelongsToMany
     */
    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', 'reservations')->whereNull('rentals.deleted_at');
    }
}
