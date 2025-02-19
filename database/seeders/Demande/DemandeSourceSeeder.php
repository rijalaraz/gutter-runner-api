<?php

namespace Database\Seeders\Demande;

use App\Models\Demande\DemandeSource;
use Illuminate\Database\Seeder;

class DemandeSourceSeeder extends Seeder
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $demandeSources = [
        ['name' => 'Internet'],
        ['name' => 'Référence'],
        ['name' => 'Camion'],
        ['name' => "Placement publicitaire"],
        ['name' => 'Réseaux sociaux'],
        ['name' => 'Ne sais pas'],
        ['name' => 'Déjà client'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDemandeSources();
    }

    /**
     * @return $this
     */
    public function createDemandeSources()
    {
        $this->demandeSources = collect($this->demandeSources)->map(function ($demandeSource) {
            return DemandeSource::create($demandeSource);
        });

        return $this;
    }
}
