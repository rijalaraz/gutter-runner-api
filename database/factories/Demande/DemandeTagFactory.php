<?php

namespace Database\Factories\Demande;

use App\Models\Demande\DemandeTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class DemandeTagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DemandeTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->catchPhrase(),
        ];
    }
}
