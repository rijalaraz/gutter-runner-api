<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $address1 = [
            'street' => '2466 Avenue Laurier Est',
            'city' => 'Montréal',
            'province' => 'Quebec',
            'zipcode' => 'H2H 1L6',
            'country' => 'Canada',
            'postal_address' => '2466 Avenue Laurier Est, Montréal, QC, Canada',
        ];

        $address2 = [
            'street' => '2466 Rue Bélanger',
            'city' => 'Montréal',
            'province' => 'Quebec',
            'zipcode' => 'H2G 1E5',
            'country' => 'Canada',
            'postal_address' => '2466 Rue Bélanger, Montréal, QC, Canada',
        ];

        $address3 = [
            'street' => '2460 Winston Churchill Blvd',
            'city' => 'Oakville',
            'province' => 'Ontario',
            'zipcode' => 'L6H 6J5',
            'country' => 'Canada',
            'postal_address' => '2460 Winston Churchill Boulevard, Oakville, ON, Canada',
        ];

        $address4 = [
            'street' => '2468 Rue Monseigneur Laflèche',
            'city' => 'Québec',
            'province' => 'Québec',
            'zipcode' => 'G1V 1J6',
            'country' => 'Canada',
            'postal_address' => '2468 Rue Monseigneur Laflèche, Québec, QC, Canada',
        ];

        $address5 = [
            'street' => '2455 St Clair Ave W',
            'city' => 'Toronto',
            'province' => 'Ontario',
            'zipcode' => 'M6N 1K9',
            'country' => 'Canada',
            'postal_address' => '2455 Saint Clair Avenue West, Toronto, ON, Canada',
        ];

        $address6 = [
            'street' => '914 Verdier Ave',
            'city' => 'Brentwood Bay',
            'province' => 'British Columbia',
            'zipcode' => 'V8M 1C1',
            'country' => 'Canada',
            'postal_address' => '914 Verdier Ave, Brentwood Bay, BC, Canada',
        ];

        $address7 = [
            'street' => '11020 Jasper Ave',
            'city' => 'Edmonton',
            'province' => 'Alberta',
            'zipcode' => 'T5K 2N8',
            'country' => 'Canada',
            'postal_address' => '11020 Jasper Avenue, Edmonton, AB, Canada',
        ];

        $address8 = [
            'street' => '795 Rue Saint-Alexandre',
            'city' => 'Longueuil',
            'province' => 'Québec',
            'zipcode' => 'J4H 3G7',
            'country' => 'Canada',
            'postal_address' => '795 Rue Saint-Alexandre, Longueuil, QC, Canada',
        ];

        $address9 = [
            'street' => '2794 Pacific Pl',
            'city' => 'Abbotsford',
            'province' => 'British Columbia',
            'zipcode' => 'V2T 4X8',
            'country' => 'Canada',
            'postal_address' => '2794 Pacific Pl, Abbotsford, BC, Canada',
        ];

        return Arr::random([$address1, $address2, $address3, $address4, $address5, $address6, $address7, $address8, $address9]);
    }
}
