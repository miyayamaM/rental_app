<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\RepositoryInterfaces\InterfaceItemRepository;
use App\Repositories\ItemRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InterfaceItemRepository::class, ItemRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
