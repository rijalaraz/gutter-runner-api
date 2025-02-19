<?php

namespace App\Transformers\Client;

use App\Models\Client\Client;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
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
    public function transform(Client $model)
    {
        return [
            'numero_saisi' => $model->numero_saisi,
            'uuid' => $model->uuid,
            'titre' => $model->titre,
            'prenom' => $model->prenom,
            'nom' => $model->nom,
            'nom_complet' => $model->nom_complet,
            'company' => $model->company ? [
                'id' => $model->company->id,
                'name' => $model->company->name,
                'types' => $model->company->company_types,
            ] : null,
            'cellulaire' => $model->cellulaire,
            'telephones' => $model->telephones->map(function ($telephone) {
                return [
                    'id' => $telephone->id,
                    'numero' => $telephone->numero,
                    'type' => $telephone->numero_type,
                ];
            }),
            'courriels' => $model->courriels,
            'billing_address' => $model->billing_address,
            'service_addresses' => $model->service_addresses,
            'tags' => $model->tags,
            'pieces_jointes' => $model->pieces_jointes,
            'note_interne' => $model->note_interne,
            'same_as_billing_address' => $model->same_as_billing_address,
            'client_recurrent' => $model->client_recurrent,
            'payment_term' => $model->payment_term,
        ];
    }
}
