<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartado extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'monto_total',
        'monto_pagado',
        'monto_restante', // <-- AÑADIDO
        'fecha_vencimiento',
        'estado',
        'users_id'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemApartado::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}