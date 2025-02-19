<?php

namespace App\Http\Resources;

use App\Models\Demande\Demande;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DemandeCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => DemandeResource::collection($this->collection),
        ];
    }
}