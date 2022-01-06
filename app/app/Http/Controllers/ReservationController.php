<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Reservation;
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

    public function create(Request $request)
    {
        $request->validate(
            [
                'item_id' => ['required', 'int', 'exists:items,id'],
                'start_date' => ['required', 'date', 'after:today'],
                'end_date' => ['required', 'date', 'after:start_date'],
            ]
        );

        if (Reservation::checkNoOverlapWithReservations($request->item_id, $request->start_date, $request->end_date)) {
            return redirect()->route('reservations.new', ['id' => $request->item_id]);
        };

        if (Reservation::checkNoOverlapWithRentals($request->item_id, $request->start_date)) {
            return redirect()->route('reservations.new', ['id' => $request->item_id]);
        };

        Reservation::create(
            [
                "user_id" => Auth::id(),
                "item_id" => $request->item_id,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
            ]
        );

        return redirect()->route('reservations.new', ['id' => $request->item_id]);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $this->authorize('destroy', $reservation);
        $reservation->delete();
        return redirect()->route('user.reservations', ['id' => Auth::id()]);
    }
}
