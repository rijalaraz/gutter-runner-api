<?php

namespace Database\Seeders\Compagnie;

use App\Models\Domaine;
use Illuminate\Database\Seeder;

class DomaineSeeder extends Seeder
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $domaines = [
        ['name' => 'Gouttières'],
        ['name' => 'Soffite'],
        ['name' => 'Fascia'],
        ['name' => 'Revêtement extérieur'],
        ['name' => 'Toiture'],
        ['name' => 'Porte et fenêtre'],
        ['name' => 'Lavage extérieur'],
        ['name' => 'Entrepreneur général'],
        ['name' => 'Construction générale'],
        ['name' => 'Clôture'],
        ['name' => 'Terrassement'],
        ['name' => 'Lavage de fenêtre'],
        ['name' => 'Construction générale'],
        ['name' => 'Aménagement paysager'],
        ['name' => 'Architecture'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDomaines();
    }

     /**
     * @return $this
     */
    public function createDomaines()
    {
        $this->domaines = collect($this->domaines)->map(function ($domaine) {
            return Domaine::create($domaine);
        });

        return $this;
    }
}
