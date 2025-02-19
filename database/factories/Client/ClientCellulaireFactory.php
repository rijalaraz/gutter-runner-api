<?php

namespace Database\Factories\Client;

use App\Models\Client\ClientCellulaire;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ClientCellulaireFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientCellulaire::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $cellulaires = ['819-233-0681', '604-585-4903', '250-676-2962', '604-606-0260', '418-314-3692', '450-917-4567', '780-514-3201', '819-949-8119', '416-449-9529', '519-630-3182', '514-568-3119', '613-374-0178', '807-773-5291', '403-253-3322', '519-855-2717', '778-837-1167', '604-764-7185', '416-979-5462', '416-789-9849', '902-669-6819'];
        return [
            'numero' => Arr::random($cellulaires),
            'send_sms' => true,
        ];
    }
}
