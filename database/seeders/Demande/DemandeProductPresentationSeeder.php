<?php

namespace Database\Seeders\Demande;

use App\Models\Demande\DemandeProductPresentation;
use Illuminate\Database\Seeder;

class DemandeProductPresentationSeeder extends Seeder
{
    /**
     * @var array|\Illuminate\Support\Collection
     */
    public $productPresentations = [
        ['name' => 'GutterClean System', 'url' => 'https://www.youtube.com/watch?v=vjbeCzMpflY'],
        ['name' => 'T-Rex', 'url' => 'https://www.youtube.com/watch?v=Zvp7E5vMrWM'],
        ['name' => 'DoublePro', 'url' => 'https://www.youtube.com/watch?v=NFTKbST9C-Y'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createProductPresentations();
    }

    /**
     * @return $this
     */
    public function createProductPresentations()
    {
        $this->productPresentations = collect($this->productPresentations)->map(function ($productPresentation) {
            return DemandeProductPresentation::create($productPresentation);
        });

        return $this;
    }
}
