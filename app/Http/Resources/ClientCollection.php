<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => ClientCollection::collection($this->collection)
        ];
    }
}
