<?php

namespace Database\Seeders\Client;

use App\Models\Client\ClientTitre;
use Illuminate\Database\Seeder;

class ClientTitreSeeder extends Seeder
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $titres = [
        ['name' => 'M.'],
        ['name' => 'Mme.'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createTitres();
    }

    /**
     * @return $this
     */
    public function createTitres()
    {
        $this->titres = collect($this->titres)->map(function ($titre) {
            return ClientTitre::create($titre);
        });

        return $this;
    }
}
