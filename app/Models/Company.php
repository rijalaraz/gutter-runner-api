<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\HasAdvancedFilter;

class Company extends Model
{
    use HasFactory, SoftDeletes, HasAdvancedFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'couleur_principale',
        'couleur_secondaire',
        'telephone_sans_frais',
        'telephone',
        'siteweb',
        'courriel_principal',
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
     * Get the company's logo.
     */
    public function logo()
    {
        return $this->morphOne(Photo::class, 'photoable');
    }

     /**
     * Get the company's address.
     */
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get the region associated with the company.
     */
    public function region()
    {
        return $this->hasOne(Region::class);
    }

     /**
     * Get the domaines that owns the company.
     */
    public function domaines()
    {
        return $this->belongsToMany(Domaine::class, 'company_domaines')->withPivot(['company_id', 'domaine_id']);
    }

    /**
     * Get the succursales that owns the company.
     */
    public function succursales()
    {
        return $this->belongsToMany(Succursale::class, 'company_succursales')->withPivot(['company_id', 'succursale_id']);
    }

    /**
     * Get the social_networks for the company.
     */
    public function social_networks()
    {
        return $this->hasMany(SocialNetwork::class);
    }

    /**
     * Get the certifications for the company.
     */
    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }

    /**
     * Get the taxes for the company.
     */
    public function taxes()
    {
        return $this->hasMany(Taxe::class);
    }

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
