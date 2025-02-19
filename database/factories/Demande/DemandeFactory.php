<?php

namespace Database\Factories\Demande;

use App\Models\Demande\Demande;
use App\Models\Demande\DemandeSequence;
use App\Models\DocumentActivity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DemandeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Demande::class;

    private function getNextNumero()
    {
        $lastSequence = DemandeSequence::latest()->first();
        if($lastSequence) {
            $lastNumero = $lastSequence->numero;
        } else {
            $lastNumero = '010000';
        }
        $res = Demande::where('numero', $lastNumero)->first();
        if($res) {
            $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
            DemandeSequence::create([
                'numero' => $newLastNumero,
            ]);
            return $this->getNextNumero();
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
        return $this->afterMaking(function (Demande $demande) {
            //
        })->afterCreating(function (Demande $demande) {
            if (is_null($demande->assigned_to)) {
                $demande->update([
                    'statut' => Demande::STATUT_BROUILLON,
                ]);
            }
            $activity = DocumentActivity::create([
                'activity_user' => $demande->created_by,
                'document_statut' => $demande->statut,
            ]);

            $demande->activities()->save($activity);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $nextNumero = $this->getNextNumero();

        $reception_date = $this->faker->dateTimeBetween('-2 months', 'now');
        $statut_change_date = clone $reception_date;
        $nbNextMinutes = rand(180, 7200);
        $statut_change_date->add(new \DateInterval('PT'.$nbNextMinutes.'M'));

        return [
            'numero' => $nextNumero,
            'reception_date' => $reception_date,
            'statut' => Arr::random(Demande::STATUTES),
            'statut_change_date' => $statut_change_date,
            'delai_de_reponse' => null,
            'urgent' => $this->faker->boolean(30),
            'plus_de_details' => "Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500",
            'confirmation_email' => $this->faker->boolean(),
        ];
    }
}
