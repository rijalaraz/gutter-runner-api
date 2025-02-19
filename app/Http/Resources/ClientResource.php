<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'numero_saisi' => $this->numero_saisi,
            'uuid' => $this->uuid,
            'titre' => $this->titre,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'nom_complet' => $this->nom_complet,
            'company' => $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'types' => $this->company->company_types,
            ] : null,
            'cellulaire' => $this->cellulaire,
            'telephones' => $this->telephones->map(function ($telephone) {
                return [
                    'id' => $telephone->id,
                    'numero' => $telephone->numero,
                    'type' => $telephone->numero_type,
                ];
            }),
            'courriels' => $this->courriels,
            'billing_address' => $this->billing_address,
            'service_addresses' => $this->service_addresses,
            'tags' => $this->tags,
            'pieces_jointes' => $this->pieces_jointes,
            'note_interne' => $this->note_interne,
            'same_as_billing_address' => $this->same_as_billing_address,
            'client_recurrent' => $this->client_recurrent,
            'payment_term' => $this->payment_term,
        ];
    }
}
