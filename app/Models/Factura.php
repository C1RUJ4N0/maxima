<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'numero_factura',
        'monto',
        'fecha_emision',
        'estado',
        'imagen_url', // <-- AÑADIDO
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}