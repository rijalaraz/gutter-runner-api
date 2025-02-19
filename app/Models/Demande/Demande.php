<?php

namespace App\Models\Demande;

use App\Models\Address;
use App\Models\Client\Client;
use App\Models\DocumentActivity;
use App\Models\Note;
use App\Models\User;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\UuidScopeTrait;

class Demande extends Model
{
    use HasFactory, SoftDeletes, HasAdvancedFilter, UuidScopeTrait;

    public const STATUT_BROUILLON = 'Brouillon';
    public const STATUT_ASSIGNEE = "Assignée";
    public const STATUT_PLANIFIEE = "R-V planifié";
    public const STATUT_SOUMISSIONNEE = 'Soumissionnée';
    public const STATUT_ANNULEE = 'Annulée';
    public const STATUT_PERDUE = 'Perdue';
    public const STATUTES = [
        self::STATUT_BROUILLON,
        self::STATUT_ASSIGNEE,
        self::STATUT_PLANIFIEE,
        self::STATUT_SOUMISSIONNEE,
        self::STATUT_ANNULEE,
        self::STATUT_PERDUE
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
        'numero',
        'created_by',
        'statut',
        'delai_de_reponse',
        'assigned_to',
        'urgent',
        'demande_source_id',
        'plus_de_details',
        'confirmation_email',
        'client_id',
        'reception_date',
        'statut_change_date',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'reception_date' => 'datetime',
        'statut_change_date' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'received_since',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function diffToHumanInterval($start, $end) {
        $human = '';
        $start_date = \Carbon\Carbon::parse($start);
        $end_date = \Carbon\Carbon::parse($end);
        $years = $end_date->diffInYears($start_date);
        if ($years > 0) {
            $human = $human.$years.trans('duration.years').' ';
            $start_date = $start_date->addYears($years);
        }
        $months = $end_date->diffInMonths($start_date);
        if ($months > 0) {
            $human = $human.$months.trans('duration.months').' ';
            $start_date = $start_date->addMonths($months);
        }
        $days = $end_date->diffInDays($start_date);
        if ($days > 0) {
            $human = $human.$days.trans('duration.days').' ';
            $start_date = $start_date->addDays($days);
        }
        $hours = $end_date->diffInHours($start_date);
        if ($hours > 0) {
            $human = $human.$hours.trans('duration.hours');
            $start_date = $start_date->addHours($hours);
        }
        $minutes = $end_date->diffInMinutes($start_date);
        if ($minutes > 0) {
            $human = $human.$minutes.trans('duration.minutes');
            $start_date = $start_date->addMinutes($minutes);
        }
        return $human;
    }

    public static function secondsToHumanReadable(int $seconds, int $requiredParts = null)
    {
        $from     = new \DateTime('@0');
        $to       = new \DateTime("@$seconds");
        $interval = $from->diff($to);
        $str      = '';

        $parts = [
            'y' => trans('duration.years'),
            'm' => trans('duration.months'),
            'd' => trans('duration.days'),
            'h' => trans('duration.hours'),
            'i' => trans('duration.minutes'),
            's' => 'second',
        ];

        $includedParts = 0;

        foreach ($parts as $key => $text) {
            if ($requiredParts && $includedParts >= $requiredParts) {
                break;
            }

            $currentPart = $interval->{$key};

            if (empty($currentPart)) {
                continue;
            }

            if (!empty($str)) {
                $str .= '';
            }

            $str .= sprintf('%d%s', $currentPart, $text);

            if ($currentPart > 1) {
                // handle plural
                // $str .= 's';
            }

            $includedParts++;
        }

        return $str;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the demande_source that owns the demande.
     */
    public function source()
    {
        return $this->belongsTo(DemandeSource::class, 'demande_source_id');
    }

    /**
     * Get the demande_services that owns the demande.
     */
    public function services()
    {
        return $this->belongsToMany(DemandeService::class, 'demande_demande_services')->withPivot(['demande_id', 'demande_service_id']);
    }

    /**
     * Get the demande_client_appointment associated with the demande.
     */
    public function client_appointment()
    {
        return $this->hasOne(DemandeClientAppointment::class);
    }

    /**
     * Get the demande_tags that owns the demande.
     */
    public function tags()
    {
        return $this->belongsToMany(DemandeTag::class, 'demande_demande_tags')->withPivot(['demande_id', 'demande_tag_id']);
    }

    /**
     * Get the demande_product_presentations that owns the demande.
     */
    public function product_presentations()
    {
        return $this->belongsToMany(DemandeProductPresentation::class, 'demande_demande_product_presentations')->withPivot(['demande_id', 'demande_product_presentation_id']);
    }

    /**
     * Get the user that creates the demande.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all of the demande's notes_internes.
     */
    public function notes_internes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    /**
     * Get the client that owns the demande.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /*
     * Get all of the demande's activities.
     */
    public function activities()
    {
        return $this->morphMany(DocumentActivity::class, 'activiteable');
    }

    /**
     * Get the demande's service_address.
     */
    public function service_address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get the user to whom the request is assigned.
     */
    public function assigne()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    public function getReceivedSinceAttribute() {
        return trans('duration.since').': '.Demande::diffToHumanInterval($this->reception_date->format('Y-m-d H:i:m'), \Carbon\Carbon::now()->format('Y-m-d H:i:m'));
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
