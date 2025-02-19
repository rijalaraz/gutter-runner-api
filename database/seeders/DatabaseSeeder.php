<?php

namespace Database\Seeders;

use Database\Seeders\Compagnie\DomaineSeeder;
use Database\Seeders\Compagnie\SuccursaleSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Client\ClientCompanyTypeSeeder;
use Database\Seeders\Client\ClientNumeroTypeSeeder;
use Database\Seeders\Client\ClientPaymentTermSeeder;
use Database\Seeders\Client\ClientSeeder;
use Database\Seeders\Client\ClientTitreSeeder;
use Database\Seeders\Demande\DemandeClientAvailabilitySeeder;
use Database\Seeders\Demande\DemandeProductPresentationSeeder;
use Database\Seeders\Demande\DemandeSeeder;
use Database\Seeders\Demande\DemandeServiceSeeder;
use Database\Seeders\Demande\DemandeSourceSeeder;
use Database\Seeders\Users\RoleTableSeeder;
use Database\Seeders\Users\UsersTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        $this->call(DomaineSeeder::class);
        $this->call(SuccursaleSeeder::class);
        $this->call(ClientTitreSeeder::class);
        $this->call(ClientCompanyTypeSeeder::class);
        $this->call(ClientPaymentTermSeeder::class);
        $this->call(ClientNumeroTypeSeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(DemandeSourceSeeder::class);
        $this->call(DemandeServiceSeeder::class);
        $this->call(DemandeClientAvailabilitySeeder::class);
        $this->call(DemandeProductPresentationSeeder::class);

        $this->call(ClientSeeder::class);
        $this->call(DemandeSeeder::class);
    }
}
