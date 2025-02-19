<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class DemandeResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->client_appointment) {
            $this->client_appointment->client_availabilities;
        }
        return [
            'client' => $this->client->company && $this->client->prenom ? $this->client->company->name : sprintf('%s. %s', Str::substr($this->client->prenom, 0, 1), $this->client->nom),
            'reception_date' => $this->reception_date,
            'received_since' => $this->received_since,
            'uuid' => $this->uuid,
            'urgent' => $this->urgent,
            'statut' => $this->statut,
            'delai_de_reponse' => $this->delai_de_reponse,
            'creator' => $this->creator,
            'assigne' => $this->assigne,
            'source' => $this->source,
            'services' => $this->services,
            'plus_de_details' => $this->plus_de_details,
            'client_appointment' => $this->client_appointment,
            'tags' => $this->tags,
            // 'videos' => $this->product_presentations,
            'service_address' => $this->service_address,
            'notes_internes' => $this->notes_internes->map(function ($notes_interne) {
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
            // 'activities' => $this->activities,
        ];
    }
}
