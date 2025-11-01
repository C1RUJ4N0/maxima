<div x-show="pestañaActiva === 'Ventas'" class="bg-sky-50 p-6 rounded-lg shadow-xl max-w-7xl mx-auto">
    <h2 class="text-xl font-bold mb-4">Registro de Ventas Recientes</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase bg-sky-100"> 
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Cliente</th>
                    <th class="px-6 py-3">Monto Total</th>
                    <th class="px-6 py-3">Método Pago</th>
                    <th class="px-6 py-3">Fecha</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <template x-for="v in ventas" :key="v.id">
                    <tr>
                        <td class="px-6 py-4" x-text="v.id"></td>
                        <td class="px-6 py-4 font-medium" x-text="v.cliente ? v.cliente.nombre : 'Cliente Desconocido'"></td>
                        <td class="px-6 py-4" x-text="`$${parseFloat(v.monto_total).toFixed(2)}`"></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs" 
                                :class="{
                                    'bg-green-200 text-green-800': v.metodo_pago === 'efectivo', 
                                    'bg-sky-200 text-sky-800': v.metodo_pago === 'tarjeta',
                                    'bg-purple-200 text-purple-800': v.metodo_pago === 'transferencia'
                                }" 
                                x-text="v.metodo_pago">
                            </span>
                        </td>
                        <td class="px-6 py-4" x-text="new Date(v.created_at).toLocaleString()"></td>
                        <td class="px-6 py-4 flex gap-3">
                            <button @click="iniciarEdicionVenta(v)" class="text-sky-600 hover:text-sky-800" title="Editar Método de Pago">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button @click="confirmarEliminarVenta(v.id)" class="text-red-600 hover:text-red-900" title="Eliminar Venta (Devuelve Stock)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </template>
                <template x-if="ventas.length === 0">
                    <tr><td colspan="6" class="text-center p-8 text-gray-500">No hay ventas registradas recientemente.</td></tr>
                </template>
            </tbody>
            
            {{-- --- INICIO REVERSIÓN (Volvemos al footer original) --- --}}
            <tfoot class="bg-sky-100 border-t-2 border-sky-200">
                <tr class="font-semibold text-sm">
                    <td class="px-6 py-4 text-right" colspan="2">Total de Ventas (Recientes):</td>
                    {{-- Columna para el Monto Total --}}
                    <td class="px-6 py-4 font-bold" x-text="`$${parseFloat(ventasTotalAmount).toFixed(2)}`"></td>
                    <td class="px-6 py-4 text-right">Total Items:</td>
                    {{-- Columna para el Count Total --}}
                    <td class="px-6 py-4 font-bold" x-text="ventasTotalCount"></td>
                    <td class="px-6 py-4"></td> {{-- Columna de acciones vacía --}}
                </tr>
            </tfoot>
            {{-- --- FIN REVERSIÓN --- --}}

        </table>
    </div>

    {{-- --- INICIO REVERSIÓN (Quitamos Paginación) --- --}}
    {{-- --- FIN REVERSIÓN --- --}}

</div>