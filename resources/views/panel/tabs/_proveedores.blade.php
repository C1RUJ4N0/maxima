<div x-show="pestañaActiva === 'Proveedores'" class="grid grid-cols-12 gap-6">
    <div class="col-span-12 md:col-span-4 bg-sky-50 p-4 rounded-lg shadow-xl">
        <div class="flex justify-between items-center border-b pb-2 mb-2">
            <h2 class="text-xl font-bold">Proveedores</h2>
            <button @click="abrirModal('proveedor')" class="bg-sky-600 text-white px-3 py-1.5 rounded-lg text-sm"><i class="fas fa-plus"></i></button>
        </div>
        <ul class="divide-y -mx-4">
            <template x-for="p in proveedores" :key="p.id">
                <li class="p-4 hover:bg-sky-100" :class="{'bg-sky-200': proveedorSeleccionado?.id === p.id}"> 
                    <div @click="seleccionarProveedor(p.id)" class="cursor-pointer">
                        <p class="font-bold" x-text="p.nombre"></p>
                        <p class="text-sm text-gray-500" x-text="p.telefono"></p>
                    </div>
                    <div class="flex gap-3 mt-2">
                        <button @click.stop="iniciarEdicionProveedor(p)" class="action-btn bg-sky-100 text-sky-700 hover:bg-sky-200"><i class="fas fa-pencil-alt"></i></button>
                        <button @click.stop="confirmarEliminarProveedor(p.id)" class="action-btn bg-red-100 text-red-700 hover:bg-red-200"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </li>
            </template>
        </ul>
    </div>
    <div class="col-span-12 md:col-span-8 bg-sky-50 p-4 rounded-lg shadow-xl">
        <div x-show="!proveedorSeleccionado" class="text-center mt-16 text-gray-500">Selecciona un proveedor para ver sus detalles</div>
        <div x-show="proveedorSeleccionado">
            <div class="flex justify-between items-center border-b pb-2 mb-2">
                <h2 class="text-xl font-bold" x-text="proveedorSeleccionado?.nombre"></h2>
                <button @click="abrirModal('factura')" class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-sm"><i class="fas fa-plus mr-1"></i>Añadir Factura</button>
            </div>
            <p class="text-gray-600"><i class="fas fa-phone mr-2"></i><span x-text="proveedorSeleccionado?.telefono"></span></p><p class="text-gray-600"><i class="fas fa-envelope mr-2"></i><span x-text="proveedorSeleccionado?.email || 'No especificado'"></span></p><p class="mt-2" x-text="proveedorSeleccionado?.descripcion"></p>
            <h3 class="text-lg font-bold mt-4 border-t pt-2">Facturas</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm mt-2">
                    <thead class="text-left"><tr><th class="py-1"># Factura</th><th>Monto</th><th>Emisión</th><th>Estado</th><th class="text-right">Acciones</th></tr></thead>
                    <tbody class="divide-y">
                        <template x-for="f in proveedorSeleccionado?.facturas" :key="f.id">
                            <tr>
                                <td class="py-2" x-text="f.numero_factura"></td>
                                <td x-text="`$${parseFloat(f.monto).toFixed(2)}`"></td>
                                <td x-text="f.fecha_emision"></td>
                                <td><span class="px-2 py-1 rounded-full text-xs" :class="{'bg-yellow-200 text-yellow-800': f.estado === 'pendiente', 'bg-green-200 text-green-800': f.estado === 'pagada'}" x-text="f.estado"></span></td>
                                <td class="py-2 text-right">
                                    <div class="flex justify-end gap-3">
                                        <template x-if="f.imagen_url">
                                            <a :href="`/storage/${f.imagen_url}`" target="_blank" class="text-sky-600 hover:text-sky-900" title="Ver Imagen">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </template>
                                        <button @click="iniciarEdicionFactura(f)" class="text-sky-600 hover:text-sky-800" title="Editar Factura">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button @click="confirmarEliminarFactura(f.id)" class="text-red-600 hover:text-red-900" title="Eliminar Factura" :disabled="f.estado === 'pagada'">
                                            <i class="fas fa-trash-alt" :class="f.estado === 'pagada' ? 'opacity-30 cursor-not-allowed' : ''"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>