<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Maxima</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; min-height: 100vh; }
        [x-cloak] { display: none !important; }
        .modal-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 50; overflow-y: auto; padding-top: 4rem; padding-bottom: 4rem;}
        .action-btn { padding: 4px 8px; border-radius: 6px; }
        .action-btn i { font-size: 0.9rem; }
    </style>
</head>
<body x-data="appTPV()">

    <div x-show="cargandoInicial" class="flex items-center justify-center min-h-screen" x-cloak>
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-indigo-600"></i>
            <p class="mt-4 text-lg font-semibold text-gray-700">Inicializando sistema...</p>
        </div>
    </div>

    <div x-show="!cargandoInicial" x-cloak>
        <div x-show="mostrarNotificacion" x-transition :class="notificacion.exito ? 'bg-green-600' : 'bg-red-600'" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 max-w-sm">
            <span x-text="notificacion.mensaje"></span>
        </div>

        <div x-show="modalActivo" class="modal-container" x-transition>
            <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-lg" @click.away="modalActivo = null">
                
                <div x-show="modalActivo === 'cliente'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Nuevo Cliente</h3>
                    <form @submit.prevent="guardarNuevoCliente">
                        <div class="space-y-4">
                            <div><label for="cliente_nombre" class="block font-semibold">Nombre</label><input type="text" id="cliente_nombre" x-model="nuevoCliente.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="cliente_telefono" class="block font-semibold">Teléfono (Opcional)</label><input type="text" id="cliente_telefono" x-model="nuevoCliente.telefono" class="w-full p-2 border rounded-lg mt-1"></div>
                            <div><label for="cliente_email" class="block font-semibold">Email (Opcional)</label><input type="email" id="cliente_email" x-model="nuevoCliente.email" class="w-full p-2 border rounded-lg mt-1"></div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar Cliente</button></div>
                    </form>
                </div>
                
                <div x-show="modalActivo === 'apartado'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Crear Apartado</h3>
                    <form @submit.prevent="guardarNuevoApartado">
                        <div class="space-y-4">
                            <div><label for="apartado_pago" class="block font-semibold">Monto Pagado</label><input type="number" id="apartado_pago" step="0.01" x-model.number="nuevoApartado.monto_pagado" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="apartado_vencimiento" class="block font-semibold">Fecha de Vencimiento</label><input type="date" id="apartado_vencimiento" x-model="nuevoApartado.fecha_vencimiento" class="w-full p-2 border rounded-lg mt-1" required></div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar Apartado</button></div>
                    </form>
                </div>

                <div x-show="modalActivo === 'editarApartado'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Apartado #<span x-text="apartadoEditando.id"></span></h3>
                    <form @submit.prevent="guardarEdicionApartado">
                        <div class="space-y-4">
                            <div><label class="block font-semibold">Cliente</label><input type="text" :value="apartadoEditando.nombre_cliente" class="w-full p-2 border rounded-lg mt-1 bg-gray-100" disabled></div>
                            <div><label class="block font-semibold">Monto Total</label><input type="text" :value="`$${apartadoEditando.monto_total}`" class="w-full p-2 border rounded-lg mt-1 bg-gray-100" disabled></div>
                            
                            <div><label for="edit_apartado_pago" class="block font-semibold">Monto Pagado</label><input type="number" id="edit_apartado_pago" step="0.01" x-model.number="apartadoEditando.monto_pagado" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="edit_apartado_vencimiento" class="block font-semibold">Fecha de Vencimiento</label><input type="date" id="edit_apartado_vencimiento" x-model="apartadoEditando.fecha_vencimiento" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div>
                                <label for="edit_apartado_estado" class="block font-semibold">Estado</label>
                                <select id="edit_apartado_estado" x-model="apartadoEditando.estado" class="w-full p-2 border rounded-lg mt-1">
                                    <option value="vigente">Vigente</option>
                                    <option value="pagado">Pagado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar Cambios</button></div>
                    </form>
                </div>
                
                <div x-show="modalActivo === 'producto'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Nuevo Producto</h3>
                    <form @submit.prevent="guardarNuevoProducto">
                        <div class="space-y-4">
                            <div><label for="prod_nombre" class="block font-semibold">Nombre</label><input type="text" id="prod_nombre" x-model="nuevoProducto.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="prod_precio" class="block font-semibold">Precio</label><input type="number" id="prod_precio" step="0.01" x-model.number="nuevoProducto.precio" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="prod_existencias" class="block font-semibold">Existencias</label><input type="number" id="prod_existencias" x-model.number="nuevoProducto.existencias" class="w-full p-2 border rounded-lg mt-1" required></div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar</button></div>
                    </form>
                </div>
                
                <div x-show="modalActivo === 'editarProducto'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Producto</h3>
                    <form @submit.prevent="guardarEdicionProducto">
                        <div class="space-y-4">
                            <div><label for="edit_prod_nombre" class="block font-semibold">Nombre</label><input type="text" id="edit_prod_nombre" x-model="productoEditando.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="edit_prod_precio" class="block font-semibold">Precio</label><input type="number" id="edit_prod_precio" step="0.01" x-model.number="productoEditando.precio" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="edit_prod_existencias" class="block font-semibold">Existencias</label><input type="number" id="edit_prod_existencias" x-model.number="productoEditando.existencias" class="w-full p-2 border rounded-lg mt-1" required></div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar Cambios</button></div>
                    </form>
                </div>

                <div x-show="modalActivo === 'proveedor'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Nuevo Proveedor</h3>
                    <form @submit.prevent="guardarNuevoProveedor">
                    <div class="space-y-4">
                            <div><label for="prov_nombre" class="block font-semibold">Nombre</label><input type="text" id="prov_nombre" x-model="nuevoProveedor.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="prov_tel" class="block font-semibold">Teléfono</label><input type="text" id="prov_tel" x-model="nuevoProveedor.telefono" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="prov_email" class="block font-semibold">Email (Opcional)</label><input type="email" id="prov_email" x-model="nuevoProveedor.email" class="w-full p-2 border rounded-lg mt-1"></div>
                            <div><label for="prov_desc" class="block font-semibold">Descripción (¿Qué vende?)</label><textarea id="prov_desc" x-model="nuevoProveedor.descripcion" class="w-full p-2 border rounded-lg mt-1"></textarea></div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar</button></div>
                    </form>
                </div>

                <div x-show="modalActivo === 'editarProveedor'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Editar Proveedor</h3>
                    <form @submit.prevent="guardarEdicionProveedor">
                        <div class="space-y-4">
                            <div><label for="edit_prov_nombre" class="block font-semibold">Nombre</label><input type="text" id="edit_prov_nombre" x-model="proveedorEditando.nombre" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="edit_prov_tel" class="block font-semibold">Teléfono</label><input type="text" id="edit_prov_tel" x-model="proveedorEditando.telefono" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="edit_prov_email" class="block font-semibold">Email (Opcional)</label><input type="email" id="edit_prov_email" x-model="proveedorEditando.email" class="w-full p-2 border rounded-lg mt-1"></div>
                            <div><label for="edit_prov_desc" class="block font-semibold">Descripción (¿Qué vende?)</LabeL><textarea id="edit_prov_desc" x-model="proveedorEditando.descripcion" class="w-full p-2 border rounded-lg mt-1"></textarea></div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar Cambios</button></div>
                    </form>
                </div>
                
                <div x-show="modalActivo === 'factura'">
                    <h3 class="text-2xl font-bold mb-4 border-b pb-2">Añadir Factura a <span x-text="proveedorSeleccionado?.nombre"></span></h3>
                    <form @submit.prevent="guardarNuevaFactura" id="formNuevaFactura">
                        <div class="space-y-4">
                            <div><label for="fact_num" class="block font-semibold">Número de Factura</label><input type="text" id="fact_num" x-model="nuevaFactura.numero_factura" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="fact_monto" class="block font-semibold">Monto</label><input type="number" id="fact_monto" step="0.01" x-model.number="nuevaFactura.monto" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div><label for="fact_fecha" class="block font-semibold">Fecha de Emisión</label><input type="date" id="fact_fecha" x-model="nuevaFactura.fecha_emision" class="w-full p-2 border rounded-lg mt-1" required></div>
                            <div>
                                <label for="factura_imagen" class="block font-semibold">Imagen de la Factura (Opcional)</label>
                                <input type="file" id="factura_imagen" class="w-full p-2 border rounded-lg mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar</button></div>
                    </form>
                </div>

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
                                <input type="file" id="edit_factura_imagen" class="w-full p-2 border rounded-lg mt-1 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <template x-if="facturaEditando.imagen_url">
                                    <a :href="`/storage/${facturaEditando.imagen_url}`" target="_blank" class="text-sm text-indigo-600 hover:underline">Ver imagen actual</a>
                                </template>
                                </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-4"><button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar Cambios</button></div>
                    </form>
                </div>

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
                            <button type="button" @click="modalActivo = null" class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
        <header class="bg-white rounded-lg shadow-xl p-4 mb-4 mx-4 sm:mx-8 mt-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl sm:text-2xl font-bold">Maxima</h1>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white text-sm px-3 py-1.5 rounded-full hover:bg-red-600">
                            <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                        </button>
                    </form>
                    <button @click="init()" class="bg-gray-200 text-sm px-3 py-1.5 rounded-full hover:bg-gray-300">
                        <i class="fas fa-arrows-rotate mr-1" :class="{ 'fa-spin': cargando }"></i> Recargar
                    </button>
                </div>
            </div>
            <nav class="mt-4 border-t pt-2">
                <div class="flex flex-wrap space-x-2 sm:space-x-4">
                    <template x-for="pestaña in pestañas" :key="pestaña.nombre">
                        <button @click="cambiarPestaña(pestaña.nombre)" :class="{'bg-indigo-600 text-white': pestañaActiva === pestaña.nombre, 'bg-gray-200 hover:bg-gray-300': pestañaActiva !== pestaña.nombre}" class="px-3 py-1.5 rounded-full text-sm flex items-center gap-2 mb-2">
                            <i :class="pestaña.icono"></i>
                            <span x-text="pestaña.nombre"></span>
                        </button>
                    </template>
                </div>
            </nav>
        </header>

        <main class="mx-4 sm:mx-8">
            <div x-show="pestañaActiva === 'Inventario'" class="grid grid-cols-12 gap-6">
                <div class="col-span-12 lg:col-span-5 bg-white rounded-lg shadow-xl p-4 flex flex-col">
                    <div class="flex justify-between items-center border-b pb-2 mb-4">
                        <h2 class="text-xl font-bold"><i class="fas fa-boxes mr-2"></i>Inventario</h2>
                        <button @click="abrirModal('producto')" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-indigo-700"><i class="fas fa-plus mr-1"></i> Añadir Producto</button>
                    </div>
                    <div class="relative mb-4">
                        <label for="busqueda_producto" class="sr-only">Buscar Producto</label>
                        <input type="text" id="busqueda_producto" x-model.debounce.300ms="busqueda" @keyup="buscarProductos" placeholder="Escribe para buscar productos..." class="w-full pl-10 pr-4 py-2 border rounded-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3">Producto</th>
                                    <th class="px-6 py-3 text-center">Stock</th>
                                    <th class="px-6 py-3">Precio</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <template x-if="busqueda.length > 0 && productos.length > 0">
                                    <template x-for="producto in productos" :key="producto.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium" x-text="producto.nombre"></td>
                                            <td class="px-6 py-4 text-center" x-text="producto.existencias"></td>
                                            <td class="px-6 py-4" x-text="`$${producto.precio}`"></td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex justify-center gap-3">
                                                    <button @click="añadirACarrito(producto)" :disabled="producto.existencias <= 0" class="text-blue-600 disabled:opacity-50" title="Añadir al carrito">
                                                        <i class="fas fa-plus-circle text-lg"></i>
                                                    </button>
                                                    <button @click="iniciarEdicionProducto(producto)" class="text-indigo-600 hover:text-indigo-900" title="Editar Producto">
                                                        <i class="fas fa-pencil-alt text-lg"></i>
                                                    </button>
                                                    <button @click="confirmarEliminarProducto(producto.id)" class="text-red-600 hover:text-red-900" title="Eliminar Producto">
                                                        <i class="fas fa-trash-alt text-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="busqueda.length > 0 && productos.length === 0"><tr><td colspan="4" class="text-center p-8 text-gray-500">No se encontraron productos.</td></tr></template>
                                <template x-if="busqueda.length === 0"><tr><td colspan="4" class="text-center p-8 text-gray-500">Escribe en la barra para buscar productos.</td></tr></template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-3 bg-white rounded-lg shadow-xl p-4 flex flex-col">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4"><i class="fas fa-shopping-cart mr-2"></i>Carrito</h2>
                    <div class="flex-1 overflow-y-auto space-y-3">
                        <template x-if="carrito.length === 0"><p class="text-center mt-8">El carrito está vacío.</p></template>
                        <template x-for="item in carrito" :key="item.id"><div class="flex items-center bg-gray-50 p-3 rounded-lg"><div class="flex-grow"><p class="font-semibold text-sm" x-text="item.nombre"></p><p class="text-xs" x-text="`Subtotal: $${(item.precio * item.cantidad).toFixed(2)}`"></p></div><div class="flex items-center gap-2"><label :for="'qty-' + item.id" class="sr-only">Cantidad</label><input type="number" :id="'qty-' + item.id" x-model.number="item.cantidad" @change="actualizarCantidad(item.id, $event.target.value)" min="1" :max="item.existencias" class="w-12 text-center border rounded-lg text-sm"><button @click="eliminarDeCarrito(item.id)" class="text-red-500"><i class="fas fa-trash-alt text-lg"></i></button></div></div></template>
                    </div>
                    <div class="mt-4 pt-4 border-t-2"><div class="flex justify-between items-center"><span class="font-semibold">Total:</span><span class="font-bold text-2xl text-indigo-600" x-text="`$${totalVenta}`"></span></div></div>
                </div>
                <div class="col-span-12 lg:col-span-4 bg-white rounded-lg shadow-xl p-4 flex flex-col">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4"><i class="fas fa-cash-register mr-2"></i>Caja</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="cliente_select" class="font-semibold">Cliente</label>
                            <div class="flex items-center gap-2">
                                <select id="cliente_select" class="w-full p-2 border rounded-lg" x-model.number="clienteSeleccionadoId">
                                    <template x-for="cliente in clientes" :key="cliente.id"><option :value="cliente.id" x-text="cliente.nombre"></option></template>
                                </select>
                                <button @click="abrirModal('cliente')" class="bg-indigo-600 text-white p-2 rounded-lg"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center"><p>Total a pagar:</p><p class="text-4xl font-bold" x-text="`$${totalVenta}`"></p></div>
                        <div><label for="monto_recibido">Monto Recibido:</label><input type="number" id="monto_recibido" x-model.number="montoRecibido" class="w-full p-2 border rounded-lg text-lg"></div>
                        <div class="bg-yellow-100 p-4 rounded-lg text-center"><p>Cambio:</p><p class="text-3xl font-bold" x-text="textoCambio"></p></div>
                        
                        <div>
                            <label class="font-semibold">Método de Pago</label>
                            <div class="grid grid-cols-3 gap-2 mt-2">
                                <button @click="metodoPago = 'efectivo'" :class="{'bg-indigo-600 text-white': metodoPago === 'efectivo', 'bg-gray-200 hover:bg-gray-300': metodoPago !== 'efectivo'}" class="p-2 rounded-lg text-sm transition-colors">Efectivo</button>
                                <button @click="metodoPago = 'tarjeta'" :class="{'bg-indigo-600 text-white': metodoPago === 'tarjeta', 'bg-gray-200 hover:bg-gray-300': metodoPago !== 'tarjeta'}" class="p-2 rounded-lg text-sm transition-colors">Tarjeta</button>
                                <button @click="metodoPago = 'transferencia'" :class="{'bg-indigo-600 text-white': metodoPago === 'transferencia', 'bg-gray-200 hover:bg-gray-300': metodoPago !== 'transferencia'}" class="p-2 rounded-lg text-sm transition-colors">Transfer</button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto pt-4 space-y-2 border-t">
                        <button @click="abrirModal('apartado')" :disabled="esClienteGeneral() || carrito.length === 0" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-inbox mr-2"></i> CREAR APARTADO</button>
                        <button @click="finalizarVenta" :disabled="carrito.length === 0 || montoRecibido < totalVenta" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-money-check-dollar mr-2"></i> FINALIZAR VENTA</button>
                        <button @click="restablecerVenta" :disabled="carrito.length === 0" class="w-full bg-red-500 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-times mr-2"></i> CANCELAR</button>
                    </div>
                </div>
            </div>

            <div x-show="pestañaActiva === 'Estadísticas'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-xl text-center"><h3 class="text-lg font-semibold text-gray-500">Ventas del Día</h3><p class="text-4xl font-bold mt-2" x-text="`$${parseFloat(estadisticas.ventasHoy).toFixed(2)}`"></p></div>
                    <div class="bg-white p-6 rounded-lg shadow-xl text-center"><h3 class="text-lg font-semibold text-gray-500">Ventas del Mes</h3><p class="text-4xl font-bold mt-2" x-text="`$${parseFloat(estadisticas.ventasMes).toFixed(2)}`"></p></div>
                    <div class="bg-white p-6 rounded-lg shadow-xl text-center"><h3 class="text-lg font-semibold text-gray-500">Egresos Totales (Pagados)</h3><p class="text-4xl font-bold mt-2 text-red-500" x-text="`$${parseFloat(estadisticas.egresos).toFixed(2)}`"></p></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-xl"><h3 class="text-xl font-bold mb-4">Productos con Bajo Stock</h3><ul class="divide-y"><template x-for="p in estadisticas.productosBajoStock" :key="p.id"><li class="py-2 flex justify-between"><span x-text="p.nombre"></span><span class="font-semibold text-red-500" x-text="`Stock: ${p.existencias}`"></span></li></template></ul></div>
                    <div class="bg-white p-6 rounded-lg shadow-xl"><h3 class="text-xl font-bold mb-4">Apartados Vigentes</h3><ul class="divide-y"><template x-for="a in estadisticas.apartadosVigentes" :key="a.id"><li class="py-2"><span x-text="a.cliente_nombre"></span><div class="flex justify-between text-sm"><span class="font-semibold" x-text="`$${parseFloat(a.monto_total).toFixed(2)}`"></span><span class="text-gray-500" x-text="`Vence: ${a.fecha_vencimiento}`"></span></div></li></template></ul></div>
                </div>
            </div>

            <div x-show="pestañaActiva === 'Proveedores'" class="grid grid-cols-12 gap-6">
                <div class="col-span-12 md:col-span-4 bg-white p-4 rounded-lg shadow-xl">
                    <div class="flex justify-between items-center border-b pb-2 mb-2"><h2 class="text-xl font-bold">Proveedores</h2><button @click="abrirModal('proveedor')" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm"><i class="fas fa-plus"></i></button></div>
                    <ul class="divide-y -mx-4">
                        <template x-for="p in proveedores" :key="p.id">
                            <li class="p-4 hover:bg-gray-100" :class="{'bg-indigo-100': proveedorSeleccionado?.id === p.id}">
                                <div @click="seleccionarProveedor(p.id)" class="cursor-pointer">
                                    <p class="font-bold" x-text="p.nombre"></p>
                                    <p class="text-sm text-gray-500" x-text="p.telefono"></p>
                                </div>
                                <div class="flex gap-3 mt-2">
                                    <button @click.stop="iniciarEdicionProveedor(p)" class="action-btn bg-indigo-100 text-indigo-700 hover:bg-indigo-200"><i class="fas fa-pencil-alt"></i></button>
                                    <button @click.stop="confirmarEliminarProveedor(p.id)" class="action-btn bg-red-100 text-red-700 hover:bg-red-200"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="col-span-12 md:col-span-8 bg-white p-4 rounded-lg shadow-xl">
                    <div x-show="!proveedorSeleccionado" class="text-center mt-16 text-gray-500">Selecciona un proveedor para ver sus detalles</div>
                    <div x-show="proveedorSeleccionado">
                        <div class="flex justify-between items-center border-b pb-2 mb-2"><h2 class="text-xl font-bold" x-text="proveedorSeleccionado?.nombre"></h2><button @click="abrirModal('factura')" class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-sm"><i class="fas fa-plus mr-1"></i>Añadir Factura</button></div>
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
                                                        <a :href="`/storage/${f.imagen_url}`" target="_blank" class="text-blue-600 hover:text-blue-900" title="Ver Imagen">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </template>
                                                    <button @click="iniciarEdicionFactura(f)" class="text-indigo-600 hover:text-indigo-900" title="Editar Factura">
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

            <div x-show="pestañaActiva === 'Apartados'" class="bg-white p-6 rounded-lg shadow-xl">
                <h2 class="text-xl font-bold mb-4">Gestión de Apartados</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase bg-gray-50">
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
                                    <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs" :class="{'bg-blue-200 text-blue-800': a.estado === 'vigente', 'bg-green-200 text-green-800': a.estado === 'pagado', 'bg-red-200 text-red-800': a.estado === 'cancelado'}" x-text="a.estado"></span></td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3">
                                            <button @click="iniciarEdicionApartado(a)" class="text-indigo-600 hover:text-indigo-900" title="Editar Apartado">
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
            
            <div x-show="pestañaActiva === 'Ventas'" class="bg-white p-6 rounded-lg shadow-xl">
                <h2 class="text-xl font-bold mb-4">Registro de Ventas Recientes</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase bg-gray-50">
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
                                                'bg-blue-200 text-blue-800': v.metodo_pago === 'tarjeta',
                                                'bg-purple-200 text-purple-800': v.metodo_pago === 'transferencia'
                                            }" 
                                            x-text="v.metodo_pago">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4" x-text="new Date(v.created_at).toLocaleString()"></td>
                                    <td class="px-6 py-4 flex gap-3">
                                        <button @click="iniciarEdicionVenta(v)" class="text-indigo-600 hover:text-indigo-900" title="Editar Método de Pago">
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
                    </table>
                </div>
            </div>

        </main>
    </div>

<script>
    function appTPV() {
        return {
            // State
            cargandoInicial: true, cargando: false, productos: [], clientes: [], carrito: [],
            pestañaActiva: 'Inventario', busqueda: '', clienteSeleccionadoId: null, montoRecibido: null,
            metodoPago: 'efectivo',
            modalActivo: null,
            nuevoCliente: { nombre: '', telefono: '', email: '' },
            nuevoApartado: { monto_pagado: null, fecha_vencimiento: '' },
            nuevoProducto: { nombre: '', precio: null, existencias: null },
            nuevoProveedor: { nombre: '', telefono: '', email: '', descripcion: '' },
            nuevaFactura: { numero_factura: '', monto: null, fecha_emision: '', imagen_factura: null },
            clienteGeneralId: null,
            mostrarNotificacion: false, notificacion: { mensaje: '', exito: true },
            estadisticas: { ventasHoy: 0, ventasMes: 0, egresos: 0, productosBajoStock: [], apartadosVigentes: [] },
            proveedores: [],
            proveedorSeleccionado: null,
            apartados: [],
            ventas: [],
            
            // State de Edición
            productoEditando: { id: null, nombre: '', precio: null, existencias: null },
            apartadoEditando: { id: null, nombre_cliente: '', monto_total: 0, monto_pagado: 0, fecha_vencimiento: '', estado: 'vigente' },
            proveedorEditando: { id: null, nombre: '', telefono: '', email: '', descripcion: '' },
            ventaEditando: { id: null, metodo_pago: '' },
            facturaEditando: { id: null, numero_factura: '', monto: 0, fecha_emision: '', estado: 'pendiente', imagen_url: '', nueva_imagen: null },
            
            pestañas: [
                { nombre: 'Inventario', icono: 'fas fa-cash-register' }, 
                { nombre: 'Ventas', icono: 'fas fa-receipt' },
                { nombre: 'Estadísticas', icono: 'fas fa-chart-line' },
                { nombre: 'Proveedores', icono: 'fas fa-truck-fast' }, 
                { nombre: 'Apartados', icono: 'fas fa-inbox' },
            ],

            // Init
            async init() {
                this.cargandoInicial = true;
                try {
                    await this.obtenerClientes(); 
                    await this.obtenerEstadisticas();
                } catch (error) {
                    console.error("Error fatal al iniciar la aplicación:", error);
                    this.notificar("No se pudo conectar con el servidor.", false);
                } finally {
                    this.cargandoInicial = false;
                }
            },

            // Getters
            get totalVenta() { return this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0).toFixed(2); },
            get textoCambio() {
                if (this.montoRecibido === null || this.montoRecibido < this.totalVenta) return `$0.00`;
                return `$${(this.montoRecibido - this.totalVenta).toFixed(2)}`;
            },
            esClienteGeneral() { return this.clienteSeleccionadoId == this.clienteGeneralId; },

            // API Methods
            async fetchAPI(endpoint, options = {}) {
                this.cargando = true;
                try {
                    // Si el body es FormData, deja que el navegador ponga el Content-Type
                    if (!(options.body instanceof FormData)) {
                         options.headers = {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                             ...options.headers,
                        };
                    } else {
                         options.headers = {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                             ...options.headers,
                        };
                    }

                    const response = await fetch(`/api${endpoint}`, options);
                    if (response.status === 204) {
                        return { success: true, message: 'Recurso eliminado.' }; 
                    }
                    const data = await response.json();
                    if (!response.ok) {
                        const error = new Error(data.message || `Error ${response.status}`);
                        error.errors = data.errors;
                        throw error;
                    }
                    return data;
                } catch (error) {
                    console.error('Error en fetchAPI:', error);
                    if (error.errors) {
                        this.notificar(Object.values(error.errors)[0][0], false);
                    } else {
                        this.notificar(error.message || 'Error de comunicación con la API.', false);
                    }
                    throw error;
                } finally { this.cargando = false; }
            },

            // --- Métodos de Carga (Obtener) ---
            async obtenerClientes() {
                const data = await this.fetchAPI('/inventario/clientes');
                if (data && Array.isArray(data.clientes)) {
                    this.clientes = data.clientes;
                    if (data.clienteGeneralId) {
                        this.clienteGeneralId = data.clienteGeneralId;
                        if (this.clienteSeleccionadoId === null) {
                            this.$nextTick(() => {
                                this.clienteSeleccionadoId = data.clienteGeneralId;
                            });
                        }
                    }
                } else { throw new Error("La respuesta de la API de clientes no tiene el formato esperado."); }
            },
            async buscarProductos() {
                if (this.busqueda.trim() === '') { this.productos = []; return; }
                const data = await this.fetchAPI(`/inventario/productos?q=${this.busqueda}`);
                this.productos = data.productos || [];
            },
            async obtenerEstadisticas() {
                const data = await this.fetchAPI('/estadisticas');
                this.estadisticas = { ...data, loaded: true };
            },
            async obtenerProveedores() {
                const data = await this.fetchAPI('/proveedores');
                this.proveedores = data.proveedores;
                this.proveedorSeleccionado = null; 
            },
            async obtenerApartados() {
                const data = await this.fetchAPI('/apartados');
                this.apartados = data;
            },
            async obtenerVentas() {
                const data = await this.fetchAPI('/ventas');
                this.ventas = data;
            },
            
            // --- Métodos de TPV (Inventario, Carrito, Venta) ---
            async guardarNuevoCliente() {
                try {
                    const data = await this.fetchAPI('/inventario/clientes', { method: 'POST', body: JSON.stringify(this.nuevoCliente) });
                    this.notificar(`Cliente '${data.cliente.nombre}' añadido.`);
                    this.modalActivo = null;
                    await this.obtenerClientes();
                    this.clienteSeleccionadoId = data.cliente.id; 
                } catch (error) { /* Ya notificado */ }
            },
            async guardarNuevoApartado() {
                try {
                    const apartadoData = { 
                        cliente_id: this.clienteSeleccionadoId, 
                        monto_total: this.totalVenta, 
                        monto_pagado: this.nuevoApartado.monto_pagado, fecha_vencimiento: this.nuevoApartado.fecha_vencimiento, 
                        items: this.carrito.map(p => ({ id: p.id, cantidad: p.cantidad })) 
                    };
                    await this.fetchAPI('/apartados', { method: 'POST', body: JSON.stringify(apartadoData) });
                    this.notificar('Apartado creado exitosamente.');
                    this.modalActivo = null;
                    this.restablecerVenta();
                    if(this.pestañaActiva === 'Apartados') await this.obtenerApartados();
                    await this.obtenerEstadisticas();
                } catch (error) { /* Ya notificado */ }
            },
            async guardarNuevoProducto() {
                try {
                    const productoCreado = await this.fetchAPI('/inventario', { method: 'POST', body: JSON.stringify(this.nuevoProducto) });
                    this.notificar(`Producto '${productoCreado.nombre}' añadido.`);
                    this.modalActivo = null;
                    this.buscarProductos(); 
                } catch (error) { /* Ya notificado */ }
            },
            async finalizarVenta() {
                if (this.clienteSeleccionadoId === null) {
                    this.notificar('Error: No hay ningún cliente seleccionado.', false);
                    return;
                }
                const ventaData = {
                    carrito: this.carrito.map(item => ({ id: item.id, cantidad: item.cantidad, precio_venta: item.precio })),
                    cliente_id: this.clienteSeleccionadoId,
                    monto_recibido: this.montoRecibido,
                    metodo_pago: this.metodoPago
                };
                try {
                    const resultado = await this.fetchAPI('/ventas', { method: 'POST', body: JSON.stringify(ventaData) });
                    this.notificar(resultado.message || 'Venta finalizada con éxito.', true);
                    this.restablecerVenta();
                    await this.obtenerEstadisticas();
                } catch (error) { console.error("Error al finalizar la venta:", error); }
            },
            
            // --- CRUD de Pestañas ---
            
            async cambiarPestaña(pestaña) {
                this.pestañaActiva = pestaña;
                try {
                    if (pestaña === 'Estadísticas') await this.obtenerEstadisticas();
                    if (pestaña === 'Proveedores') await this.obtenerProveedores();
                    if (pestaña === 'Apartados') await this.obtenerApartados();
                    if (pestaña === 'Ventas') await this.obtenerVentas();
                } catch (error) { /* Ya notificado */ }
            },
            
            // -- Productos --
            iniciarEdicionProducto(producto) {
                this.productoEditando = { ...producto }; 
                this.abrirModal('editarProducto');
            },
            async guardarEdicionProducto() {
                try {
                    const data = await this.fetchAPI(`/inventario/${this.productoEditando.id}`, {
                        method: 'PUT',
                        body: JSON.stringify(this.productoEditando)
                    });
                    this.notificar(data.message || 'Producto actualizado.');
                    this.modalActivo = null;
                    await this.buscarProductos(); 
                } catch (error) { /* Ya notificado */ }
            },
            confirmarEliminarProducto(id) {
                if (confirm('¿Seguro que quieres eliminar este producto? Esta acción es irreversible.')) {
                    this.eliminarProducto(id);
                }
            },
            async eliminarProducto(id) {
                try {
                    const data = await this.fetchAPI(`/inventario/${id}`, { method: 'DELETE' });
                    this.notificar(data.message || 'Producto eliminado.');
                    await this.buscarProductos();
                } catch (error) { /* Ya notificado */ }
            },

            // -- Apartados --
            iniciarEdicionApartado(apartado) {
                this.apartadoEditando = { ...apartado };
                this.apartadoEditando.fecha_vencimiento = apartado.fecha_vencimiento.split('T')[0];
                this.abrirModal('editarApartado');
            },
            async guardarEdicionApartado() {
                try {
                    const data = await this.fetchAPI(`/apartados/${this.apartadoEditando.id}`, {
                        method: 'PUT',
                        body: JSON.stringify({
                            monto_pagado: this.apartadoEditando.monto_pagado,
                            fecha_vencimiento: this.apartadoEditando.fecha_vencimiento,
                            estado: this.apartadoEditando.estado
                        })
                    });
                    this.notificar(data.message || 'Apartado actualizado.');
                    this.modalActivo = null;
                    await this.obtenerApartados();
                    await this.obtenerEstadisticas(); 
                } catch (error) { /* Ya notificado */ }
            },
            confirmarEliminarApartado(id) {
                if (confirm('¿Seguro que quieres eliminar este apartado? El stock será devuelto si el estado era "vigente" o "cancelado".')) {
                    this.eliminarApartado(id);
                }
            },
            async eliminarApartado(id) {
                try {
                    const data = await this.fetchAPI(`/apartados/${id}`, { method: 'DELETE' });
                    this.notificar(data.message || 'Apartado eliminado.');
                    await this.obtenerApartados();
                    await this.obtenerEstadisticas(); 
                } catch (error) { /* Ya notificado */ }
            },

            // -- Proveedores --
            async seleccionarProveedor(id) {
                try {
                    const data = await this.fetchAPI(`/proveedores/${id}`);
                    this.proveedorSeleccionado = data;
                } catch (error) { /* Ya notificado */ }
            },
            // *** ¡¡¡ARREGLO DEL ERROR!!! ***
            // Esta función faltaba
            async guardarNuevoProveedor() {
                try {
                    const proveedorData = { nombre: this.nuevoProveedor.nombre, telefono: this.nuevoProveedor.telefono, email: this.nuevoProveedor.email, descripcion: this.nuevoProveedor.descripcion };
                    const data = await this.fetchAPI('/proveedores', { method: 'POST', body: JSON.stringify(proveedorData) });
                    this.notificar(`Proveedor '${data.nombre}' añadido.`);
                    this.modalActivo = null;
                    await this.obtenerProveedores();
                    this.seleccionarProveedor(data.id);
                } catch (error) {
                    /* Ya notificado por fetchAPI */
                }
            },
            // *** FIN DEL ARREGLO ***
            iniciarEdicionProveedor(proveedor) {
                this.proveedorEditando = { ...proveedor };
                this.abrirModal('editarProveedor');
            },
            async guardarEdicionProveedor() {
                try {
                    const data = await this.fetchAPI(`/proveedores/${this.proveedorEditando.id}`, {
                        method: 'PUT',
                        body: JSON.stringify(this.proveedorEditando)
                    });
                    this.notificar(data.message || 'Proveedor actualizado.');
                    this.modalActivo = null;
                    await this.obtenerProveedores(); 
                } catch (error) { /* Ya notificado */ }
            },
            confirmarEliminarProveedor(id) {
                if (confirm('¿Seguro que quieres eliminar este proveedor? Todas sus facturas asociadas también serán eliminadas.')) {
                    this.eliminarProveedor(id);
                }
            },
            async eliminarProveedor(id) {
                try {
                    const data = await this.fetchAPI(`/proveedores/${id}`, { method: 'DELETE' });
                    this.notificar(data.message || 'Proveedor eliminado.');
                    await this.obtenerProveedores(); 
                } catch (error) { /* Ya notificado */ }
            },

            // -- Facturas (CRUD) --
            async guardarNuevaFactura() {
                try {
                    const formData = new FormData();
                    formData.append('numero_factura', this.nuevaFactura.numero_factura);
                    formData.append('monto', this.nuevaFactura.monto);
                    formData.append('fecha_emision', this.nuevaFactura.fecha_emision);
                    
                    const inputFile = document.querySelector('#factura_imagen');
                    if (inputFile.files.length > 0) {
                        formData.append('imagen_factura', inputFile.files[0]);
                    }
                    
                    this.cargando = true;
                    const response = await fetch(`/api/proveedores/${this.proveedorSeleccionado.id}/facturas`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    
                    this.cargando = false;
                    const data = await response.json();
                    
                    if (!response.ok) {
                         const error = new Error(data.message || `Error ${response.status}`);
                         error.errors = data.errors;
                         throw error;
                    }

                    this.notificar(`Factura #${data.numero_factura} añadida.`);
                    this.modalActivo = null;
                    await this.seleccionarProveedor(this.proveedorSeleccionado.id);

                } catch (error) { 
                    this.cargando = false;
                    if (error.errors) this.notificar(Object.values(error.errors)[0][0], false);
                    else this.notificar(error.message, false);
                }
            },
            
            iniciarEdicionFactura(factura) {
                this.facturaEditando = { ...factura };
                this.facturaEditando.fecha_emision = factura.fecha_emision.split('T')[0];
                this.facturaEditando.nueva_imagen = null; 
                this.abrirModal('editarFactura');
            },
            
            async guardarEdicionFactura() {
                try {
                    const formData = new FormData();
                    formData.append('numero_factura', this.facturaEditando.numero_factura);
                    formData.append('monto', this.facturaEditando.monto);
                    formData.append('fecha_emision', this.facturaEditando.fecha_emision);
                    formData.append('estado', this.facturaEditando.estado);
                    
                    const inputFile = document.querySelector('#edit_factura_imagen');
                    if (inputFile.files.length > 0) {
                        formData.append('imagen_factura', inputFile.files[0]);
                    }
                    
                    formData.append('_method', 'PUT');

                    this.cargando = true;
                    const response = await fetch(`/api/facturas/${this.facturaEditando.id}`, {
                        method: 'POST', 
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    
                    this.cargando = false;
                    const data = await response.json();
                    
                    if (!response.ok) {
                         const error = new Error(data.message || `Error ${response.status}`);
                         error.errors = data.errors;
                         throw error;
                    }

                    this.notificar(data.message || 'Factura actualizada.');
                    this.modalActivo = null;
                    await this.seleccionarProveedor(this.proveedorSeleccionado.id); 
                    await this.obtenerEstadisticas(); 

                } catch (error) { 
                    this.cargando = false;
                    if (error.errors) this.notificar(Object.values(error.errors)[0][0], false);
                    else this.notificar(error.message, false);
                }
            },
            confirmarEliminarFactura(id) {
                if (confirm('¿Seguro que quieres eliminar esta factura? Solo puedes eliminar facturas "pendientes".')) {
                    this.eliminarFactura(id);
                }
            },
            async eliminarFactura(id) {
                try {
                    const data = await this.fetchAPI(`/facturas/${id}`, { method: 'DELETE' });
                    this.notificar(data.message || 'Factura eliminada.');
                    await this.seleccionarProveedor(this.proveedorSeleccionado.id); 
                } catch (error) { /* Ya notificado */ }
            },

            // -- Ventas (CRUD Pestaña) --
            iniciarEdicionVenta(venta) {
                this.ventaEditando.id = venta.id;
                this.ventaEditando.metodo_pago = venta.metodo_pago;
                this.abrirModal('editarVenta');
            },
            async guardarEdicionVenta() {
                try {
                    const data = await this.fetchAPI(`/ventas/${this.ventaEditando.id}`, {
                        method: 'PUT',
                        body: JSON.stringify({ metodo_pago: this.ventaEditando.metodo_pago })
                    });
                    this.notificar(data.message || 'Método de pago actualizado.');
                    this.modalActivo = null;
                    await this.obtenerVentas();
                } catch (error) { /* Ya notificado */ }
            },
            confirmarEliminarVenta(id) {
                if (confirm('¿Seguro que quieres eliminar esta venta? Esta acción es irreversible y devolverá el stock al inventario.')) {
                    this.eliminarVenta(id);
                }
            },
            async eliminarVenta(id) {
                try {
                    const data = await this.fetchAPI(`/ventas/${id}`, { method: 'DELETE' });
                    this.notificar(data.message || 'Venta eliminada.');
                    await this.obtenerVentas(); 
                    await this.obtenerEstadisticas();
                } catch (error) { /* Ya notificado */ }
            },

            // --- Métodos de UI (Helpers) ---
            abrirModal(tipo) {
                if (tipo === 'cliente') this.nuevoCliente = { nombre: '', telefono: '', email: '' };
                if (tipo === 'apartado') this.nuevoApartado = { monto_pagado: null, fecha_vencimiento: '' };
                if (tipo === 'producto') this.nuevoProducto = { nombre: '', precio: null, existencias: null };
                if (tipo === 'proveedor') this.nuevoProveedor = { nombre: '', telefono: '', email: '', descripcion: '' };
                if (tipo === 'factura') {
                    this.nuevaFactura = { numero_factura: '', monto: null, fecha_emision: '', imagen_factura: null };
                    const inputFile = document.querySelector('#factura_imagen');
                    if(inputFile) inputFile.value = '';
                }
                if (tipo === 'editarFactura') {
                    const editInputFile = document.querySelector('#edit_factura_imagen');
                    if(editInputFile) editInputFile.value = '';
                }
                this.modalActivo = tipo;
            },
            añadirACarrito(producto) {
                const item = this.carrito.find(i => i.id === producto.id);
                if (item) {
                    if (item.cantidad < producto.existencias) item.cantidad++;
                    else this.notificar('Stock máximo alcanzado', false);
                } else {
                    if (producto.existencias > 0) this.carrito.push({ ...producto, cantidad: 1 });
                }
            },
            actualizarCantidad(id, qty) {
                const item = this.carrito.find(i => i.id === id);
                if (!item) return;
                const cant = parseInt(qty);
                if (cant < 1) item.cantidad = 1;
                else if (cant > item.existencias) {
                    item.cantidad = item.existencias;
                    this.notificar('Stock máximo alcanzado', false);
                } else item.cantidad = cant;
            },
            eliminarDeCarrito(id) { this.carrito = this.carrito.filter(i => i.id !== id); },
            restablecerVenta() {
                this.carrito = [];
                this.montoRecibido = null;
                this.clienteSeleccionadoId = this.clienteGeneralId; // REGLA DE NEGOCIO: Vuelve a Cliente General
                this.busqueda = '';
                this.productos = [];
                this.metodoPago = 'efectivo';
            },
            notificar(mensaje, exito = true) {
                this.notificacion = { mensaje, exito };
                this.mostrarNotificacion = true;
                setTimeout(() => this.mostrarNotificacion = false, 3000);
            },
        }
    }
</script>
</body>
</html>