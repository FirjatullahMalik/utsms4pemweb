<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Pastikan ini ditambahkan

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [ 
        'name', 
        'description',
        'price', 
        'image', 
        'category_id', 
        'expired_at', 
        'modified_by'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (Auth::check()) {
                $model->modified_by = Auth::user()->email;
            }
        });
    }
}
