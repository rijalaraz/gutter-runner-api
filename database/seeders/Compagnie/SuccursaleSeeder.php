<?php

namespace Database\Seeders\Compagnie;

use App\Models\Succursale;
use Illuminate\Database\Seeder;

class SuccursaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Succursale::factory(8)->create();
    }
}
