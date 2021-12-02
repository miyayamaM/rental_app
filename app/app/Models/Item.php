<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users() {
        return $this->belongsToMany('App\Models\User', 'rentals')->whereNull('rentals.deleted_at');
    }

    public function isRentable() {
        return $this->users->isEmpty();
    }

    public function rental_end_date() {
        return $this->isRentable() ? null: Rental::where('item_id', $this->id)->get()->first()->end_date;
    }
}
