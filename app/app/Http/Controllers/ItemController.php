<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Rules\isEditable;
use Illuminate\Support\Facades\Validator;

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
        $merged_params = array_merge($request->all(), [
            'id' => $id,
        ]);

        $rules =  [
            'id' => [new isEditable],
            'name' => ['required', 'string', 'max:255']
        ];

        Validator::make($merged_params, $rules, [], ['name' => '物品名'])->validate();

        Item::find($id)->update([
                        'name' => $request->name
                    ]);
        return redirect()->route('item.show',['id' => $id]);
    }

    public function destroy($id) {
        $rules = [
            'id' => [ new isEditable ]
        ];
         
        Validator::make(['id' => $id], $rules)->validate();

        Item::find($id)->delete();
        return redirect('/items');
    }
}
