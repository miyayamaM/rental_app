<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
}
