<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index() {
        $items = Item::all();
        return view('item.index', compact('items'));
    }

    public function new() {
        return view('item.new');
    }

    public function create(Request $request) {
        $request->validate(
            ['name' => ['required', 'string', 'max:255']],
            [],
            ['name' => '物品名']
        );
        Item::create(['name' => $request->name]);
        return redirect('/items');
    }

    public function show($id) {
        $item = Item::find($id);
        return view('item.show', compact('item'));
    }

    public function edit($id) {
        $item = Item::find($id);
        return view('item.edit', compact('item'));
    }

    public function update(Request $request, $id) {
        $item = Item::find($id);
        if(!$item->isRentable()) {
            abort(403);
        };
        $request->validate(
            ['name' => ['required', 'string', 'max:255']],
            [],
            ['name' => '物品名']
        );

        $item->update([
                        'name' => $request->name
                    ]);
        return redirect()->route('item.show',['id' => $id]);
    }

    public function destroy($id) {
        $item = Item::find($id);
        if(!$item->isRentable()) {
            abort(403);
        };
        $item->delete();
        return redirect('/items');
    }
}
