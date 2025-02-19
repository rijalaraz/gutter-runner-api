<?php

namespace App\Models\Client;

use App\Models\Address;
use App\Models\Demande\Demande;
use App\Models\Photo;
use App\Models\Tag;
use App\Notifications\DemandeCreated;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\UuidScopeTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, Notifiable, SoftDeletes, HasAdvancedFilter, UuidScopeTrait;

    public const STATUT_INACTIF = 'Inactif';
    public const STATUT_ACTIF = 'Actif';
    public const STATUT_PAIEMENT_EN_RETARD = 'Paiement en retard';
    public const STATUT_ARCHIVE = 'Archivé';
    public const STATUTES = [
        self::STATUT_INACTIF,
        self::STATUT_ACTIF,
        self::STATUT_PAIEMENT_EN_RETARD,
        self::STATUT_ARCHIVE
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_titre_id',
        'numero_saisi',
        'prenom',
        'nom',
        'client_recurrent',
        'client_payment_term_id',
        'note_interne',
        'same_as_billing_address',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'nom_complet',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Send the email demande creation notification.
     *
     * @return void
     */
    public function sendEmailDemandeCreationNotification($demande)
    {
        $this->notify(new DemandeCreated($demande));
    }

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @param  \Illuminate\Notifications\Notification|null  $notification
     * @return mixed
     */
    public function routeNotificationFor($driver, $notification = null)
    {
        if (method_exists($this, $method = 'routeNotificationFor'.Str::studly($driver))) {
            return $this->{$method}($notification);
        }

        switch ($driver) {
            case 'database':
                return $this->notifications();
            case 'mail':
                $courriels = $this->courriels()->pluck('email')->toArray();
                return $courriels;
                // return $this->email;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the client_titre that owns the client.
     */
    public function titre()
    {
        return $this->belongsTo(ClientTitre::class, 'client_titre_id');
    }

    /**
     * Get the client's company.
     */
    public function company()
    {
        return $this->hasOne(ClientCompany::class);
    }

    /**
     * Get the client_payment_term that owns the client.
     */
    public function payment_term()
    {
        return $this->belongsTo(ClientPaymentTerm::class, 'client_payment_term_id');
    }

    /**
     * Get the tags that owns the client.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'client_tags')->withPivot(['client_id', 'tag_id']);
    }

    /**
     * Get the client's cellulaire.
     */
    public function cellulaire()
    {
        return $this->hasOne(ClientCellulaire::class);
    }

    /**
     * Get the client_telephones for the client.
     */
    public function telephones()
    {
        return $this->hasMany(ClientTelephone::class);
    }

    /**
     * Get the client_courriels for the client.
     */
    public function courriels()
    {
        return $this->hasMany(ClientCourriel::class);
    }

    /**
     * Get all of the client's pieces_jointes.
     */
    public function pieces_jointes()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    /**
     * Get the client's billing_address.
     */
    public function billing_address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get all of the client's service_addresses.
     */
    public function service_addresses()
    {
        return $this->morphMany(Address::class, 'service_addressable');
    }

    /**
     * Get the demandes for the client.
     */
    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeWhereLike($query, $column, $value)
    {
        return $query->where($column, 'like', '%'.$value.'%');
    }

    public function scopeOrWhereLike($query, $column, $value)
    {
        return $query->orWhere($column, 'like', '%'.$value.'%');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the nom_complet attribute.
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        return sprintf("%s %s", $this->prenom, $this->nom);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
