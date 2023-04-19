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
}
