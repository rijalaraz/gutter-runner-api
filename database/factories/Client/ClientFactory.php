<?php

namespace Database\Factories\Client;

use App\Models\Address;
use App\Models\Client\Client;
use App\Models\Client\ClientSequence;
use App\Models\Photo;
use App\Support\UploadBase64Trait;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ClientFactory extends Factory
{
    use UploadBase64Trait;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    private function getNextClientNumero()
    {
        $lastSequence = ClientSequence::latest()->first();
        if($lastSequence) {
            $lastNumero = $lastSequence->numero;
        } else {
            $lastNumero = '010000';
        }
        $res = Client::where('numero_saisi', $lastNumero)->first();
        if($res) {
            $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
            ClientSequence::create([
                'numero' => $newLastNumero,
            ]);
            return $this->getNextClientNumero();
        } else {
            return $lastNumero;
        }
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Client $client) {
            //
        })->afterCreating(function (Client $client) {
            if ($client->same_as_billing_address) {
                $client->service_addresses()->delete();
                $billing_address = $client->billing_address()->first();
                $service_address = Address::create([
                    'street' => $billing_address->street,
                    'city' => $billing_address->city,
                    'province' => $billing_address->province,
                    'zipcode' => $billing_address->zipcode,
                    'country' => $billing_address->country,
                    'postal_address' => $billing_address->postal_address,
                    'note_interne' => 'Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum',
                ]);
                $client->service_addresses()->save($service_address);
            }

            $aAttachements = [];
            $attachements = Storage::disk('dev')->files('base64');
            foreach ($attachements as $attachement) {
                $pieces_jointe = [
                    'file_name' => basename($attachement),
                    'url' => Storage::disk('dev')->get($attachement),
                ];
                $filename =  $this->uploadFile($pieces_jointe, 'clients');
                $pj = Photo::create([
                    'url' => $filename,
                ]);
                $aAttachements[] = $pj;
            }
            $client->pieces_jointes()->saveMany($aAttachements);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $lastNumero = $this->getNextClientNumero();
        return [
            'numero_saisi' => $lastNumero,
            'prenom' => $this->faker->firstName(),
            'nom' => $this->faker->lastName(),
            'client_recurrent' => $this->faker->boolean(20),
            "note_interne" => "On sait depuis longtemps que travailler avec du texte lisible et contenant du sens est source de distractions, et empêche de se concentrer sur la mise en page elle-même.",
            'same_as_billing_address' => $this->faker->boolean(),
        ];
    }
}
