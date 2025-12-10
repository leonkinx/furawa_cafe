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
        'ppn_percentage',
        'category',
        'stock',
        'image',
        'is_best_seller',
        'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'ppn_percentage' => 'decimal:2',
        'is_best_seller' => 'boolean',
        'is_available' => 'boolean',
    ];
    
    // Calculate PPN amount
    public function getPpnAmountAttribute()
    {
        return ($this->price * $this->ppn_percentage) / 100;
    }
    
    // Calculate final price including PPN
    public function getFinalPriceAttribute()
    {
        return $this->price + $this->ppn_amount;
    }
}