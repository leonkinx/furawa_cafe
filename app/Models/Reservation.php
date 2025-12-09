<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'customer_name',
        'customer_phone',
        'date',
        'time', 
        'people_count',
        'status',
        'special_requests'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}