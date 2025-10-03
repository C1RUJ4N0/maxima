<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'existencias',
        'precio',
        'descripcion',
    ];

    public function articulosVenta(): HasMany
    {
        return $this->hasMany(ItemVenta::class, 'producto_id');
    }

    public function articulosApartado(): HasMany
    {
        return $this->hasMany(ItemApartado::class, 'producto_id');
    }
}
