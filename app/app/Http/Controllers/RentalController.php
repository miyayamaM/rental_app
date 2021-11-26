<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RentalController extends Controller
{
    public function index(Request $request, $id) {
        $rental_items = User::find($id)->items;
        
        return view('item.rentals', compact('rental_items'));
    }
}
