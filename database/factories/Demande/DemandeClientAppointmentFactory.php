<?php

namespace Database\Factories\Demande;

use App\Models\Demande\DemandeClientAppointment;
use App\Models\Demande\DemandeClientAvailability;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DemandeClientAppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DemandeClientAppointment::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (DemandeClientAppointment $client_appointment) {
            //
        })->afterCreating(function (DemandeClientAppointment $client_appointment) {
            $client_appointment->client_availabilities()->sync(DemandeClientAvailability::pluck('id')->random(2));
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fewDaysLater = '+5 days';
        return [
            'appointment_date' => date("U",strtotime($fewDaysLater)),
            'year' => date("Y",strtotime($fewDaysLater)),
            'month' => date("m",strtotime($fewDaysLater)),
            'day' => date("d",strtotime($fewDaysLater)),
            'time' => date("H:i:s",strtotime($fewDaysLater)),
        ];
    }
}
