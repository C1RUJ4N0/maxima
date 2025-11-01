<div x-show="pestañaActiva === 'Apartados'" class="bg-sky-50 p-6 rounded-lg shadow-xl max-w-7xl mx-auto">
    <h2 class="text-xl font-bold mb-4">Gestión de Apartados</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase bg-sky-100">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Cliente</th>
                    <th class="px-6 py-3">Monto Total</th>
                    <th class="px-6 py-3">Monto Pagado</th>
                    <th class="px-6 py-3">Monto Restante</th>
                    <th class="px-6 py-3">Vencimiento</th>
                    <th class="px-6 py-3">Estado</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <template x-for="a in apartados" :key="a.id">
                    <tr>
                        <td class="px-6 py-4" x-text="a.id"></td>
                        <td class="px-6 py-4 font-medium" x-text="a.nombre_cliente"></td>
                        <td class="px-6 py-4" x-text="`$${parseFloat(a.monto_total).toFixed(2)}`"></td>
                        <td class="px-6 py-4" x-text="`$${parseFloat(a.monto_pagado).toFixed(2)}`"></td>
                        <td class="px-6 py-4 font-bold" x-text="`$${parseFloat(a.monto).toFixed(2)}`"></td>
                        <td class="px-6 py-4" x-text="a.fecha_vencimiento"></td>
                        <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs" :class="{'bg-sky-200 text-sky-800': a.estado === 'vigente', 'bg-green-200 text-green-800': a.estado === 'pagado', 'bg-red-200 text-red-800': a.estado === 'cancelado'}" x-text="a.estado"></span></td>
                        <td class="px-6 py-4">
                            <div class="flex gap-3">
                                <button @click="iniciarEdicionApartado(a)" class="text-sky-600 hover:text-sky-800" title="Editar Apartado">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button @click="confirmarEliminarApartado(a.id)" class="text-red-600 hover:text-red-900" title="Eliminar Apartado">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>