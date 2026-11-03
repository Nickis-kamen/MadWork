<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service_detail extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceDetailFactory> */
    use HasFactory;
    protected $fillable = [
        'service_id',
        'image',
        'title',
        'description'
    ];

    public function service()
    {
        $this->belongsTo(Service::class);
    }
}
