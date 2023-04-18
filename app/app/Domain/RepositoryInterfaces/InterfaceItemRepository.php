<?php

namespace App\Domain\RepositoryInterfaces;

use Illuminate\Database\Eloquent\Collection;

interface InterfaceItemRepository
{
    public function list(): Collection;
}
