<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\ItemRequest;
use App\Http\Requests\ItemEditRequest;
use App\Rules\IsEditable;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('users')->get();
        return view('item.index', compact('items'));
    }

    public function new()
    {
        return view('item.new');
    }

    public function create(ItemRequest $request)
    {
        Item::create(['name' => $request->name]);
        return redirect('/items');
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return view('item.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        if (!$item->isRentable()) {
            return redirect('/items');
        };
        return view('item.edit', compact('item'));
    }

    public function update(ItemEditRequest $request, $id)
    {
        Item::findOrFail($id)->update([
                        'name' => $request->name
                    ]);
        return redirect()->route('item.show', ['id' => $id]);
    }

    public function destroy($id)
    {
        $rules = [
            'id' => [ new IsEditable() ]
        ];

        Validator::make(['id' => $id], $rules)->validate();

        Item::findOrFail($id)->delete();
        return redirect('/items');
    }
}
