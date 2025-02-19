<?php

namespace Database\Factories;

use App\Models\Succursale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuccursaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Succursale::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
        ];
    }
}
