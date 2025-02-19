<?php

namespace Database\Seeders\Users;

use App\Models\Address;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        /**
         * @var Company $company
         */
        $company = Company::create([
            'nom' => 'Stratège Média',
            'telephone_sans_frais' => '780-401-7648',
            'telephone' => '705-882-7905',
            'courriel_principal' => 'mathieu.roy@strategemedia.com',
        ]);

        $address = Address::create([
            'street' => '2466 Avenue Laurier Est',
            'city' => 'Montréal',
            'province' => 'Quebec',
            'zipcode' => 'H2H 1L6',
            'country' => 'Canada',
            'postal_address' => '2466 Avenue Laurier Est, Montréal, QC, Canada',
        ]);
        $company->address()->save($address);

        $user = \App\Models\User::factory()->create([
            'name'           => 'Admin',
            'firstname'      => 'admin',
            'lastname'       => 'Admin',
            'email'          => 'admin@admin.com',
            'password'       => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'company_id' => $company->id,
        ]);
        $user->assignRole('Administrateur');

        $user2 = \App\Models\User::factory()->create([
            'name'           => 'Mathieu Roy',
            'firstname'      => 'Mathieu',
            'lastname'       => 'Roy',
            'email'          => 'mathieu.roy@strategemedia.com',
            'password'       => bcrypt('Test1234$'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'company_id' => $company->id,
        ]);
        $user2->assignRole('Administrateur');


         /**
         * @var Company $company2
         */
        $company2 = Company::create([
            'nom' => 'Robinson Furniture',
            'telephone_sans_frais' => '416-889-4181',
            'telephone' => '416-699-3690',
            'courriel_principal' => 'james.barstow@robinson.com',
        ]);
        $address2 = Address::create([
            'street' => '3115 Tycos Dr',
            'city' => 'Toronto',
            'province' => 'Ontario',
            'zipcode' => 'M5T 1T4',
            'country' => 'Canada',
            'postal_address' => '3115 Tycos Drive, Toronto, North York, ON, Canada',
        ]);
        $company2->address()->save($address2);

        $user3 = \App\Models\User::factory()->create([
            'name'           => 'James L Barstow',
            'firstname'      => 'James',
            'lastname'       => 'L Barstow',
            'email'          => 'james.barstow@robinson.com',
            'password'       => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'company_id' => $company2->id,
        ]);
        $user3->assignRole('Administrateur');
    }
}
