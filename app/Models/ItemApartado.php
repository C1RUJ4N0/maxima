<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemApartado extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartado_id',
        'producto_id',
        'cantidad',
        'precio'
    ];
}