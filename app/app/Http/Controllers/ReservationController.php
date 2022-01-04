<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    //
    public function index(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user_name = Auth::id() == $id ? 'あなた' : $user->name . 'さん';
        $reservation_items = $user->reservations;

        return view('reservation.index', compact('reservation_items', 'user_name'));
    }

    public function new(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $reservation_users = $item->reservations;

        return view('reservation.new', compact('reservation_users', 'item'));
    }
}
