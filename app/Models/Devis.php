<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    /** @use HasFactory<\Database\Factories\DevisFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'service_id',
        'agency_id',
        'delai',
        'delai_agency',
        'description',
        'description_agency',
        'cdc_file',
        'price',
        'budget',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
