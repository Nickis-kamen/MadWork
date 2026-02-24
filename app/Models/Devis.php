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
        'service_type',
        'delai',
        'delai_admin',
        'description',
        'description_admin',
        'cdc_file',
        'price',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
