<?php

namespace Database\Seeders\Client;

use App\Models\Client\ClientNumeroType;
use Illuminate\Database\Seeder;

class ClientNumeroTypeSeeder extends Seeder
{
     /**
     * @var array|\Illuminate\Support\Collection
     */
    public $numberTypes = [
        ['name' => 'Maison'],
        ['name' => 'Bureau'],
        ['name' => 'Télécopier'],
        ['name' => 'Autre'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createNumberTypes();
    }

      /**
     * @return $this
     */
    public function createNumberTypes()
    {
        $this->numberTypes = collect($this->numberTypes)->map(function ($numberType) {
            return ClientNumeroType::create($numberType);
        });

        return $this;
    }
}
