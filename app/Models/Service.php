<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'image',
        'title',
        'slug',
        'description',
        'price',
        'agency_id',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }

    public function serviceDetails()
    {
        return $this->hasMany(Service_detail::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
