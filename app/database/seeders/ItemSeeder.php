<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            $item = [
                'name' => '物品' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('items')->insert($item);
        };
    }
}
