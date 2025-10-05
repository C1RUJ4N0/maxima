<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'monto_total',
        'monto_recibido',
        'cambio',
        'users_id', // <-- CORREGIDO
        'metodo_pago',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(ItemVenta::class, 'venta_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}