<?php

namespace Database\Seeders\Demande;

use App\Models\Demande\DemandeService;
use Illuminate\Database\Seeder;

class DemandeServiceSeeder extends Seeder
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $demandeServices = [
        ['name' => 'Installation'],
        ['name' => 'Réparation'],
        ['name' => 'Nettoyage'],
        ['name' => "Appel de service"],
        ['name' => 'Garantie'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDemandeServices();
    }

    /**
     * @return $this
     */
    public function createDemandeServices()
    {
        $this->demandeServices = collect($this->demandeServices)->map(function ($demandeService) {
            return DemandeService::create($demandeService);
        });

        return $this;
    }
}
