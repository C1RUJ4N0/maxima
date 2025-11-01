{{-- // --- MODALES PARA TODOS (CLIENTE Y APARTADO) --- // --}}
<div x-show="modalActivo === 'cliente' || modalActivo === 'apartado'" class="modal-container" x-transition>
    <div class="bg-sky-50 rounded-xl shadow-xl p-8 w-full max-w-lg" @click.away="modalActivo = null">
        
        {{-- Modal Cliente (TODOS) --}}
        <div x-show="modalActivo === 'cliente'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Nuevo Cliente</h3>
            <form @submit.prevent="guardarNuevoCliente">
                <div class="space-y-4">
                    <div><label for="cliente_nombre" class="block font-semibold">Nombre</label><input type="text" id="cliente_nombre" x-model="nuevoCliente.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="cliente_telefono" class="block font-semibold">Teléfono (Opcional)</label><input type="text" id="cliente_telefono" x-model="nuevoCliente.telefono" class="w-full p-2 border rounded-lg mt-1"></div>
                    <div><label for="cliente_email" class="block font-semibold">Email (Opcional)</label><input type="email" id="cliente_email" x-model="nuevoCliente.email" class="w-full p-2 border rounded-lg mt-1"></div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar Cliente</button></div>
            </form>
        </div>
        
        {{-- Modal Apartado (TODOS) --}}
        <div x-show="modalActivo === 'apartado'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Crear Apartado</h3>
            <form @submit.prevent="guardarNuevoApartado">
                <div class="space-y-4">
                    <div><label for="apartado_pago" class="block font-semibold">Monto Pagado</label><input type="number" id="apartado_pago" step="0.01" x-model.number="nuevoApartado.monto_pagado" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="apartado_vencimiento" class="block font-semibold">Fecha de Vencimiento</label><input type="date" id="apartado_vencimiento" x-model="nuevoApartado.fecha_vencimiento" class="w-full p-2 border rounded-lg mt-1" required></div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar Apartado</button></div>
            </form>
        </div>
    </div>
</div>

{{-- // --- MODALES SOLO PARA ADMIN (PRODUCTO, PROVEEDOR, EDITAR, ETC) --- // --}}
@if(Auth::user()->role === 'admin')
<div x-show="modalActivo && modalActivo !== 'cliente' && modalActivo !== 'apartado'" class="modal-container" x-transition>
    <div class="bg-sky-50 rounded-xl shadow-xl p-8 w-full max-w-lg" @click.away="modalActivo = null">
        
        {{-- Modal Editar Apartado (ADMIN) --}}
        <div x-show="modalActivo === 'editarApartado'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Apartado #<span x-text="apartadoEditando.id"></span></h3>
            <form @submit.prevent="guardarEdicionApartado">
                <div class="space-y-4">
                    <div><label class="block font-semibold">Cliente</label><input type="text" :value="apartadoEditando.nombre_cliente" class="w-full p-2 border rounded-lg mt-1 bg-gray-100" disabled></div>
                    <div><label class="block font-semibold">Monto Total</label><input type="text" :value="`$${apartadoEditando.monto_total}`" class="w-full p-2 border rounded-lg mt-1 bg-gray-100" disabled></div>
                    
                    <div><label for="edit_apartado_pago" class="block font-semibold">Monto Pagado</label><input type="number" id="edit_apartado_pago" step="0.01" x-model.number="apartadoEditando.monto_pagado" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="edit_apartado_vencimiento" class="block font-semibold">Fecha de Vencimiento</label><input type="date" id="edit_apartado_vencimiento" x-model="apartadoEditando.fecha_vencimiento" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div>
                        <label for="edit_apartado_estado" class_level="block font-semibold">Estado</label>
                        <select id="edit_apartado_estado" x-model="apartadoEditando.estado" class="w-full p-2 border rounded-lg mt-1">
                            <option value="vigente">Vigente</option>
                            <option value="pagado">Pagado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar Cambios</button></div>
            </form>
        </div>
        
        {{-- Modal Producto (ADMIN) --}}
        <div x-show="modalActivo === 'producto'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Nuevo Producto</h3>
            <form @submit.prevent="guardarNuevoProducto">
                <div class="space-y-4">
                    <div><label for="prod_nombre" class="block font-semibold">Nombre</label><input type="text" id="prod_nombre" x-model="nuevoProducto.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="prod_precio" class="block font-semibold">Precio</label><input type="number" id="prod_precio" step="0.01" x-model.number="nuevoProducto.precio" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="prod_existencias" class="block font-semibold">Existencias</label><input type="number" id="prod_existencias" x-model.number="nuevoProducto.existencias" class="w-full p-2 border rounded-lg mt-1" required></div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar</button></div>
            </form>
        </div>
        
        {{-- Modal Editar Producto (ADMIN) --}}
        <div x-show="modalActivo === 'editarProducto'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Producto</h3>
            <form @submit.prevent="guardarEdicionProducto">
                <div class="space-y-4">
                    <div><label for="edit_prod_nombre" class="block font-semibold">Nombre</label><input type="text" id="edit_prod_nombre" x-model="productoEditando.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="edit_prod_precio" class="block font-semibold">Precio</label><input type="number" id="edit_prod_precio" step="0.01" x-model.number="productoEditando.precio" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="edit_prod_existencias" class="block font-semibold">Existencias</label><input type="number" id="edit_prod_existencias" x-model.number="productoEditando.existencias" class="w-full p-2 border rounded-lg mt-1" required></div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar Cambios</button></div>
            </form>
        </div>

        {{-- Modal Proveedor (ADMIN) --}}
        <div x-show="modalActivo === 'proveedor'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Nuevo Proveedor</h3>
            <form @submit.prevent="guardarNuevoProveedor">
            <div class="space-y-4">
                    <div><label for="prov_nombre" class="block font-semibold">Nombre</label><input type="text" id="prov_nombre" x-model="nuevoProveedor.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="prov_tel" class="block font-semibold">Teléfono</label><input type="text" id="prov_tel" x-model="nuevoProveedor.telefono" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="prov_email" class="block font-semibold">Email (Opcional)</label><input type="email" id="prov_email" x-model="nuevoProveedor.email" class="w-full p-2 border rounded-lg mt-1"></div>
                    <div><label for="prov_desc" class="block font-semibold">Descripción (¿Qué vende?)</label><textarea id="prov_desc" x-model="nuevoProveedor.descripcion" class="w-full p-2 border rounded-lg mt-1"></textarea></div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar</button></div>
            </form>
        </div>

        {{-- Modal Editar Proveedor (ADMIN) --}}
        <div x-show="modalActivo === 'editarProveedor'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Proveedor</h3>
            <form @submit.prevent="guardarEdicionProveedor">
                <div class="space-y-4">
                    <div><label for="edit_prov_nombre" class="block font-semibold">Nombre</label><input type="text" id="edit_prov_nombre" x-model="proveedorEditando.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="edit_prov_tel" class="block font-semibold">Teléfono</label><input type="text" id="edit_prov_tel" x-model="proveedorEditando.telefono" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="edit_prov_email" class="block font-semibold">Email (Opcional)</label><input type="email" id="edit_prov_email" x-model="proveedorEditando.email" class="w-full p-2 border rounded-lg mt-1"></div>
                    <div><label for="edit_prov_desc" class="block font-semibold">Descripción (¿Qué vende?)</LabeL><textarea id="edit_prov_desc" x-model="proveedorEditando.descripcion" class="w-full p-2 border rounded-lg mt-1"></textarea></div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar Cambios</button></div>
            </form>
        </div>
        
        {{-- Modal Factura (ADMIN) --}}
        <div x-show="modalActivo === 'factura'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Factura a <span x-text="proveedorSeleccionado?.nombre"></span></h3>
            <form @submit.prevent="guardarNuevaFactura" id="formNuevaFactura">
                <div class="space-y-4">
                    <div><label for="fact_num" class="block font-semibold">Número de Factura</label><input type="text" id="fact_num" x-model="nuevaFactura.numero_factura" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="fact_monto" class="block font-semibold">Monto</label><input type="number" id="fact_monto" step="0.01" x-model.number="nuevaFactura.monto" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="fact_fecha" class="block font-semibold">Fecha de Emisión</label><input type="date" id="fact_fecha" x-model="nuevaFactura.fecha_emision" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div>
                        <label for="factura_imagen" class="block font-semibold">Imagen de la Factura (Opcional)</label>
                        <input type="file" id="factura_imagen" class="w-full p-2 border rounded-lg mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar</button></div>
            </form>
        </div>

        {{-- Modal Editar Factura (ADMIN) --}}
        <div x-show="modalActivo === 'editarFactura'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Factura</h3>
            <form @submit.prevent="guardarEdicionFactura" id="formEditarFactura">
                <div class="space-y-4">
                    <div><label for="edit_fact_num" class="block font-semibold">Número de Factura</label><input type="text" id="edit_fact_num" x-model="facturaEditando.numero_factura" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="edit_fact_monto" class="block font-semibold">Monto</label><input type="number" id="edit_fact_monto" step="0.01" x-model.number="facturaEditando.monto" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div><label for="edit_fact_fecha" class="block font-semibold">Fecha de Emisión</label><input type="date" id="edit_fact_fecha" x-model="facturaEditando.fecha_emision" class="w-full p-2 border rounded-lg mt-1" required></div>
                    <div>
                        <label for="edit_fact_estado" class="block font-semibold">Estado</label>
                        <select id="edit_fact_estado" x-model="facturaEditando.estado" class="w-full p-2 border rounded-lg mt-1">
                            <option value="pendiente">Pendiente</option>
                            <option value="pagada">Pagada</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_factura_imagen" class="block font-semibold">Reemplazar Imagen (Opcional)</label>
                        <input type="file" id="edit_factura_imagen" class="w-full p-2 border rounded-lg mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        <template x-if="facturaEditando.imagen_url">
                            <a :href="`/storage/${facturaEditando.imagen_url}`" target="_blank" class="text-sm text-sky-600 hover:underline">Ver imagen actual</a>
                        </template>
                        </div>
                </div>
                <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar Cambios</button></div>
            </form>
        </div>

        {{-- Modal Editar Venta (ADMIN) --}}
        <div x-show="modalActivo === 'editarVenta'">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Venta #<span x-text="ventaEditando.id"></span></h3>
            <form @submit.prevent="guardarEdicionVenta">
                <div class="space-y-4">
                    <div>
                        <label for="edit_metodo_pago" class="block font-semibold">Método de Pago</label>
                        <select id="edit_metodo_pago" x-model="ventaEditando.metodo_pago" class="w-full p-2 border rounded-lg mt-1">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-4">
                    <button type="button" @click="modalActivo = null" class="px-4 py-2 bg-sky-100 text-sky-700 hover:bg-sky-200 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg">Guardar Cambios</button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endif
{{-- // --- FIN CAMBIO ADMIN --- // --}}