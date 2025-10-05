<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemVenta extends Model
{
    use HasFactory;

    protected $table = 'item_ventas';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio', // <-- CORREGIDO a 'precio'
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}