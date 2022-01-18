<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Models\User;
use App\Models\Item;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index($id)
    {
        $user = User::findOrFail($id);
        $user_name = Auth::id() === intval($id) ? 'あなた' : $user->name . 'さん';
        $reservation_items = $user->reservations;

        return view('reservation.index', compact('reservation_items', 'user_name'));
    }

    public function new($id)
    {
        $item = Item::findOrFail($id);
        $reservation_users = $item->reservations;

        return view('reservation.new', compact('reservation_users', 'item'));
    }

    public function create(ReservationRequest $request)
    {
        if (Reservation::checkNoOverlapWithReservations($request->item_id, $request->start_date, $request->end_date)) {
            return redirect()->route('reservation.new', ['id' => $request->item_id]);
        };

        if (Reservation::checkNoOverlapWithRentals($request->item_id, $request->start_date)) {
            return redirect()->route('reservation.new', ['id' => $request->item_id]);
        };

        Reservation::create(
            [
                "user_id" => Auth::id(),
                "item_id" => $request->item_id,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
            ]
        );

        return redirect()->route('reservation.new', ['id' => $request->item_id]);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $this->authorize('destroy', $reservation);
        $reservation->delete();
        return redirect()->route('user.reservations', ['id' => Auth::id()]);
    }
}
