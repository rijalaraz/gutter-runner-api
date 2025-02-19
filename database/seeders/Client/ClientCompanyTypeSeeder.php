<?php

namespace Database\Seeders\Client;

use App\Models\Client\ClientCompanyType;
use Illuminate\Database\Seeder;

class ClientCompanyTypeSeeder extends Seeder
{
     /**
     * @var array|\Illuminate\Support\Collection
     */
    public $companyTypes = [
        ['name' => 'Entrepreneur général'],
        ['name' => 'Constructeur'],
        ['name' => 'Architecte'],
        ['name' => "Gestionnaire d'immeubles"],
        ['name' => 'Assureur'],
        ['name' => 'Toiture'],
        ['name' => 'Revêtement'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createCompanyTypes();
    }

     /**
     * @return $this
     */
    public function createCompanyTypes()
    {
        $this->companyTypes = collect($this->companyTypes)->map(function ($companyType) {
            return ClientCompanyType::create($companyType);
        });

        return $this;
    }
}
