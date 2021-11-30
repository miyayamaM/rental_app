<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RentalController extends Controller
{
    public function index(Request $request, $id) {
        $user = User::find($id);
        $user_name = Auth::id() == $id ? 'あなた': $user->name. 'さん';
        $rental_items = $user->items;
        
        return view('item.rentals', compact('rental_items', 'user_name'));
    }
}
