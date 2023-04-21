<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\ItemRequest;
use App\Http\Requests\ItemEditRequest;
use App\Rules\IsEditable;
use Illuminate\Support\Facades\Validator;
use App\Domain\RepositoryInterfaces\InterfaceItemRepository;
use Exception;

class ItemController extends Controller
{
    private InterfaceItemRepository $itemRepository;

    public function __construct(InterfaceItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function index()
    {
        $items = $this->itemRepository->list();
        return view('item.index', compact('items'));
    }

    public function new()
    {
        return view('item.new');
    }

    public function create(ItemRequest $request)
    {
        $item = new Item();
        $item->name = data_get($request->validated(), 'name');

        $this->itemRepository->save($item);
        return redirect('/items');
    }

    public function show($id)
    {
        $item = $this->itemRepository->find($id);
        if (null === $item) {
            abort(404);
        }
        return view('item.show', compact('item'));
    }

    public function edit($id)
    {
        $item = $this->itemRepository->find($id);
        if (null === $item) {
            abort(404);
        }
        if (!$item->isRentable()) {
            return redirect('/items');
        };
        return view('item.edit', compact('item'));
    }

    public function update(ItemEditRequest $request, $id)
    {
        $item = $this->itemRepository->find($id);

        if (null === $item) {
            abort(404);
        }

        $item->name = data_get($request->validated(), 'name');
        $this->itemRepository->save($item);

        return redirect()->route('item.index');
    }

    public function destroy($id)
    {
        $rules = [
            'id' => [new IsEditable()]
        ];

        Validator::make(['id' => $id], $rules)->validate();

        $item = $this->itemRepository->find($id);

        if (null === $item) {
            abort(404);
        }

        $this->itemRepository->delete($item);
        return redirect('/items');
    }
}
