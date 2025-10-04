<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'numero_factura',
        'monto',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'ruta_archivo',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
}