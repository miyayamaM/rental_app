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
            ['name' => ['required', 'string', 'max:255']]
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
        $request->validate(
            ['name' => ['required', 'string', 'max:255']]
        );

        Item::find($id)
                ->update([
                        'name' => $request->name
                        ]);
        return redirect()->route('item.show',['id' => $id]);
    }
}
