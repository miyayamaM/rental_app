<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalRequest;
use App\Models\User;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    public function index($id)
    {
        $user = User::findOrFail($id);
        $user_name = Auth::id() === intval($id) ? 'あなた' : $user->name . 'さん';
        $rental_items = $user->items()->get();

        return view('item.rentals', compact('rental_items', 'user_name'));
    }

    public function create(RentalRequest $request)
    {
        Rental::create([
            "user_id" => Auth::id(),
            "item_id" => $request->item_id,
            "end_date" => $request->end_date,
        ]);
        return redirect('/items');
    }

    public function destroy($id)
    {
        $rental = Rental::findOrFail($id);
        $this->authorize('destroy', $rental);
        $rental->delete();
        return redirect()->route('user.rentals', ['id' => Auth::id()]);
    }
}
