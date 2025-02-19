<?php

namespace Database\Seeders\Client;

use App\Models\Address;
use App\Models\Client\Client;
use App\Models\Client\ClientCellulaire;
use App\Models\Client\ClientCompany;
use App\Models\Client\ClientCourriel;
use App\Models\Client\ClientPaymentTerm;
use App\Models\Client\ClientTelephone;
use App\Models\Client\ClientTitre;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0 ; $i < 4 ; $i++) {
            Client::factory()
                ->has(ClientCompany::factory(), 'company')
                ->has(ClientCellulaire::factory(), 'cellulaire')
                ->has(ClientTelephone::factory()->count(2), 'telephones')
                ->hasAttached(Arr::random([Tag::all()->count() > 1 ? Tag::all()->random(2) : Tag::factory(), Tag::factory()->count(2)]), [], 'tags')
                ->has(ClientCourriel::factory()->count(2), 'courriels')
                ->has(Address::factory()->state([
                    'note_interne' => 'Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression',
                ]), 'billing_address')
                ->has(Address::factory()->count(2)->state([
                    'note_interne' => 'De nombreuses suites logicielles de mise en page ou éditeurs de sites Web ont fait du Lorem Ipsum leur faux texte par défaut',
                ]), 'service_addresses')
                ->state(new Sequence(
                    fn () => [
                        'client_titre_id' => ClientTitre::all()->random(),
                        'client_payment_term_id' => ClientPaymentTerm::all()->random(),
                    ]
                ))
                ->create();
        }
    }
}
