<?php

namespace App\Domain\RepositoryInterfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Item;

interface InterfaceItemRepository
{
    public function list(): Collection;
    public function save(Item $item);
}
