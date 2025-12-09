<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'details',
        'price',
        'category',
        'stock',
        'image',
        'is_best_seller',
        'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_best_seller' => 'boolean',
        'is_available' => 'boolean',
    ];
}