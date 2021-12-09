<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use App\Rules\isRentable;

class RentalController extends Controller
{
    public function index(Request $request, $id) {
        $user = User::findOrFail($id);
        $user_name = Auth::id() == $id ? 'あなた': $user->name. 'さん';
        $rental_items = $user->items;
        
        return view('item.rentals', compact('rental_items', 'user_name'));
    }

    public function create(Request $request) {
        $request->validate(
            [   
                'item_id' => ['required', 'int', 'exists:items,id', new isRentable],
                'end_date' => ['required', 'date', 'after_or_equal:today']
            ]
        );

        Rental::create([
            "user_id" => Auth::id(),
            "item_id" => $request->item_id,
            "end_date" => $request->end_date,
        ]);
        return redirect('/items');
    }

    public function destroy($id) {
        $rental = Rental::findOrFail($id);
        $this->authorize('destroy', $rental);
        $rental->delete();
        return redirect()->route('user.rentals', ['id' => Auth::id()]);;
    }
}
