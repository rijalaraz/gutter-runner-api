<?php

namespace Database\Seeders\Demande;

use App\Models\Demande\DemandeClientAvailability;
use Illuminate\Database\Seeder;

class DemandeClientAvailabilitySeeder extends Seeder
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $clientAvailabilities = [
        ['name' => 'Avant-midi'],
        ['name' => 'Après-midi'],
        ['name' => 'Soir'],
        ['name' => "En tout temps"],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createClientAvailabilities();
    }

    /**
     * @return $this
     */
    public function createClientAvailabilities()
    {
        $this->clientAvailabilities = collect($this->clientAvailabilities)->map(function ($clientAvailabilitie) {
            return DemandeClientAvailability::create($clientAvailabilitie);
        });

        return $this;
    }
}
