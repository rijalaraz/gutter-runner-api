<?php

namespace App\Transformers\Users;

use App\Models\Permission;
use League\Fractal\TransformerAbstract;

/**
 * Class PermissionTransformer.
 */
class PermissionTransformer extends TransformerAbstract
{
    /**
     * @param \App\Models\Permission $model
     * @return array
     */
    public function transform(Permission $model)
    {
        return [
            'id' => $model->uuid,
            'name' => $model->name,
        ];
    }
}
