<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\HasAdvancedFilter;

class Address extends Model
{
    use HasFactory, SoftDeletes, HasAdvancedFilter;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'street',
        'city',
        'province',
        'zipcode',
        'country',
        'note_interne',
        'postal_address',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'addressable_id',
        'addressable_type',
        'service_addressable_id',
        'service_addressable_type',
    ];

    /**
     * Get the parent addressable model (company or client or demande ...).
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Get the parent service addressable model (company or client ...).
     */
    public function service_addressable()
    {
        return $this->morphTo();
    }

    public function scopeWhereLike($query, $column, $value)
    {
        return $query->where($column, 'like', '%'.$value.'%');
    }

    public function scopeOrWhereLike($query, $column, $value)
    {
        return $query->orWhere($column, 'like', '%'.$value.'%');
    }
}
