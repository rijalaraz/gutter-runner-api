<?php

namespace Database\Factories\Client;

use App\Models\Client\ClientNumeroType;
use App\Models\Client\ClientTelephone;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ClientTelephoneFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClientTelephone::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $telephones = ['780-596-4781', '902-856-7471', '403-552-0276', '416-693-0389', '416-530-5962', '416-625-4478', '403-714-6327', '780-504-2412', '780-710-1863', '902-947-0147', '905-638-9889', '250-717-9075', '250-893-1580', '613-393-8381', '403-755-3547', '250-217-1228', '250-566-7695', '519-347-9929', '519-862-3938', '905-669-7914'];
        return [
            'numero' => Arr::random($telephones),
            'client_numero_type_id' => Arr::random(ClientNumeroType::pluck('id')->toArray()),
        ];
    }
}
