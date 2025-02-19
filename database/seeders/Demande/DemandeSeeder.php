<?php

namespace Database\Seeders\Demande;

use App\Models\Address;
use App\Models\Client\Client;
use App\Models\Demande\Demande;
use App\Models\Demande\DemandeClientAppointment;
use App\Models\Demande\DemandeProductPresentation;
use App\Models\Demande\DemandeService;
use App\Models\Demande\DemandeSource;
use App\Models\Demande\DemandeTag;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Arr;

class DemandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0 ; $i < 50 ; $i++) {
            Demande::factory()
                ->state(new Sequence(
                    fn () => [
                        'created_by' => User::all()->random(),
                        'assigned_to' => $this->getRandomAssignedTo([
                            'an_user' => 90,
                            'no_user' => 10,
                        ]),
                    ]
                ))
                ->for(Client::all()->random())
                ->for($this->getRandomSource([
                    'existing_source' => 90,
                    'factory_source' => 10,
                ]), 'source')
                ->has(DemandeClientAppointment::factory(), 'client_appointment')
                ->has(Address::factory(), 'service_address')
                ->has(Note::factory()->count(2), 'notes_internes')
                ->hasAttached($this->getRandomService([
                    'existing_service' => 90,
                    'factory_service' => 10,
                ]), [], 'services')
                ->hasAttached($this->getRandomTag([
                    'existing_tag' => 90,
                    'factory_tag' => 10,
                ]) , [], 'tags')
                ->hasAttached(DemandeProductPresentation::all()->random(2), [], 'product_presentations')
                ->create();
        }
    }

    /**
     * getRandomWeightedElement()
     * Utility function for getting random values with weighting.
     * Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
     * An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
     * The return value is the array key, A, B, or C in this case.  Note that the values assigned
     * do not have to be percentages.  The values are simply relative to each other.  If one value
     * weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
     * chance of being selected.  Also note that weights should be integers.
     *
     * @param array $weightedValues
     */
    private function getRandomAssignedTo(array $weightedValues)
    {
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key == 'an_user' ? User::all()->random() : null;
            }
        }
    }

    private function getRandomSource(array $weightedValues)
    {
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key == 'existing_source' ? DemandeSource::all()->random() : DemandeSource::factory();
            }
        }
    }

    private function getRandomService(array $weightedValues)
    {
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key == 'existing_service' ? DemandeService::all()->random(2) : DemandeService::factory();
            }
        }
    }

    private function getRandomTag(array $weightedValues)
    {
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key == 'existing_tag' ? (DemandeTag::all()->count() > 1 ? DemandeTag::all()->random(2) : DemandeTag::factory()) : DemandeTag::factory();
            }
        }
    }
}
