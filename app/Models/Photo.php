<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\HasAdvancedFilter;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory, SoftDeletes, HasAdvancedFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'url',
        'created_at',
        'updated_at',
        'deleted_at',
        'title',
        'photoable_id',
        'photoable_type',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url',
    ];

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        return asset( Storage::url( $this->getAttribute('url') ) );
    }

    /**
     * Get the parent photoable model (company or note).
     */
    public function photoable()
    {
        return $this->morphTo();
    }
}
