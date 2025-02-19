<?php

namespace App\Transformers\Demande;

use App\Models\Demande\Demande;
use App\Models\User;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Str;

class DemandeTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Demande $model)
    {
        if ($model->client_appointment) {
            $model->client_appointment->client_availabilities;
        }
        return [
            'client' => $model->client->company && $model->client->prenom ? $model->client->company->name : sprintf('%s. %s', Str::substr($model->client->prenom, 0, 1), $model->client->nom),
            'reception_date' => $model->reception_date,
            'received_since' => $model->received_since,
            'uuid' => $model->uuid,
            'urgent' => $model->urgent,
            'statut' => $model->statut,
            'delai_de_reponse' => $model->delai_de_reponse,
            'creator' => $model->creator,
            'assigne' => $model->assigne,
            'source' => $model->source,
            'services' => $model->services,
            'plus_de_details' => $model->plus_de_details,
            'client_appointment' => $model->client_appointment,
            'tags' => $model->tags,
            // 'videos' => $model->product_presentations,
            'service_address' => $model->service_address,
            'notes_internes' => $model->notes_internes->map(function ($notes_interne) {
                return [
                    'id' => $notes_interne->id,
                    'note' => $notes_interne->note,
                    'report_to_soumission' => $notes_interne->report_to_soumission,
                    'report_to_contrat' => $notes_interne->report_to_contrat,
                    'report_to_bon_de_travail' => $notes_interne->report_to_bon_de_travail,
                    'report_to_facture' => $notes_interne->report_to_facture,
                    'attachements' => $notes_interne->attachements,
                ];
            }),
            // 'activities' => $model->activities,
        ];
    }
}
