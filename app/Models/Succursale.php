<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\HasAdvancedFilter;

class Succursale extends Model
{
    use HasFactory, SoftDeletes, HasAdvancedFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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
    ];

    /**
     * Get the companies that owns the succursale.
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_succursales')->withPivot(['company_id', 'succursale_id']);
    }
}
