<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    /**
     * CORRECCIÓN: Se especifica el nombre correcto de la tabla.
     */
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'descripcion',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }
}