<?php

namespace App\Support;

use App\Models\Client\Client;
use App\Models\Demande\Demande;
use Illuminate\Support\Str;

/**
 * Class UuidScopeTrait.
 */
trait UuidScopeTrait
{
    /**
     * @param $query
     * @param $uuid
     * @return mixed
     */
    public function scopeByUuid($query, $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Boot the uuid scope trait for a model.
     *
     * @return void
     */
    protected static function bootUuidScopeTrait()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                if($model instanceof Client) {
                    $model->uuid = sprintf('CU-%s', $model->numero_saisi);
                } else if($model instanceof Demande) {
                    $model->uuid = sprintf('RQ-%s', $model->numero);
                } else {
                    $model->uuid = $model->generateRandomInt(6);
                }
            }
        });
    }

    private function generateRandomInt($length)
    {
        $a = '';
        for ($i = 0 ; $i < $length ; $i++)
        {
            $a .= mt_rand(0,9);
        }
        return $a;
    }
}
