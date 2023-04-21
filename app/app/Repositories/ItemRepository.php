<?php

namespace App\Repositories;

use App\Models\Item;
use App\Domain\RepositoryInterfaces\InterfaceItemRepository;
use Illuminate\Database\Eloquent\Collection;

class ItemRepository implements InterfaceItemRepository
{
    public function list(): Collection
    {
        return Item::with('users')->get();
    }

    public function save(Item $item): void
    {
        $item->save();
    }

    public function find(int $id): Item
    {
        return Item::find($id);
    }

    public function delete(Item $item): void
    {
        $item->delete();
    }
}
