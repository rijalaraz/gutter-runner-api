<?php

namespace Database\Factories\Client;

use App\Models\Client\ClientCompany;
use App\Models\Client\ClientCompanyType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class ClientCompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientCompany::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (ClientCompany $client_company) {
            //
        })->afterCreating(function (ClientCompany $client_company) {
            $client_company->company_types()->sync(ClientCompanyType::pluck('id')->random(2));
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
        ];
    }
}
