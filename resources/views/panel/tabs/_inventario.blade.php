<div x-show="pestañaActiva === 'Inventario'" class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-5 bg-sky-50 rounded-lg shadow-xl p-4 flex flex-col">
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h2 class="text-xl font-bold"><i class="fas fa-boxes mr-2"></i>Inventario</h2>
            @if(Auth::user()->role === 'admin')
            <button @click="abrirModal('producto')" class="bg-sky-600 text-white px-3 py-1.5 rounded-lg text-sm font-semibold hover:bg-sky-700"><i class="fas fa-plus mr-1"></i> Añadir Producto</button>
            @endif
        </div>
        <div class="relative mb-4">
            <label for="busqueda_producto" class="sr-only">Buscar Producto</label>
            <input type="text" id="busqueda_producto" x-model.debounce.300ms="busqueda" @keyup="buscarProductos" placeholder="Escribe para buscar productos..." class="w-full pl-10 pr-4 py-2 border rounded-full">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-sky-100 sticky top-0"> 
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
                            <tr class="hover:bg-sky-100"> 
                                <td class="px-6 py-4 font-medium" x-text="producto.nombre"></td>
                                <td class="px-6 py-4 text-center" x-text="producto.existencias"></td>
                                <td class="px-6 py-4" x-text="`$${producto.precio}`"></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-3">
                                        <button @click="añadirACarrito(producto)" :disabled="producto.existencias <= 0" class="text-sky-600 disabled:opacity-50" title="Añadir al carrito">
                                            <i class="fas fa-plus-circle text-lg"></i>
                                        </button>
                                        @if(Auth::user()->role === 'admin')
                                        <button @click="iniciarEdicionProducto(producto)" class="text-sky-600 hover:text-sky-800" title="Editar Producto">
                                            <i class="fas fa-pencil-alt text-lg"></i>
                                        </button>
                                        <button @click="confirmarEliminarProducto(producto.id)" class="text-red-600 hover:text-red-900" title="Eliminar Producto">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                        @endif
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
    <div class="col-span-12 lg:col-span-3 bg-sky-50 rounded-lg shadow-xl p-4 flex flex-col">
        <h2 class="text-xl font-bold border-b pb-2 mb-4"><i class="fas fa-shopping-cart mr-2"></i>Carrito</h2>
        <div class="flex-1 overflow-y-auto space-y-3">
            <template x-if="carrito.length === 0"><p class="text-center mt-8">El carrito está vacío.</p></template>
            <template x-for="item in carrito" :key="item.id"><div class="flex items-center bg-sky-100 p-3 rounded-lg"><div class="flex-grow"><p class="font-semibold text-sm" x-text="item.nombre"></p><p class="text-xs" x-text="`Subtotal: $${(item.precio * item.cantidad).toFixed(2)}`"></p></div><div class="flex items-center gap-2"><label :for="'qty-' + item.id" class="sr-only">Cantidad</label><input type="number" :id="'qty-' + item.id" x-model.number="item.cantidad" @change="actualizarCantidad(item.id, $event.target.value)" min="1" :max="item.existencias" class="w-12 text-center border rounded-lg text-sm"><button @click="eliminarDeCarrito(item.id)" class="text-red-500"><i class="fas fa-trash-alt text-lg"></i></button></div></div></template>
        </div>
        <div class="mt-4 pt-4 border-t-2"><div class="flex justify-between items-center"><span class="font-semibold">Total:</span><span class="font-bold text-2xl text-sky-600" x-text="`$${totalVenta}`"></span></div></div>
    </div>
    <div class="col-span-12 lg:col-span-4 bg-sky-50 rounded-lg shadow-xl p-4 flex flex-col">
        <h2 class="text-xl font-bold border-b pb-2 mb-4"><i class="fas fa-cash-register mr-2"></i>Caja</h2>
        <div class="space-y-4">
            <div>
                <label for="cliente_select" class="font-semibold">Cliente</label>
                <div class="flex items-center gap-2">
                    <select id="cliente_select" class="w-full p-2 border rounded-lg" x-model.number="clienteSeleccionadoId">
                        <template x-for="cliente in clientes" :key="cliente.id"><option :value="cliente.id" x-text="cliente.nombre"></option></template>
                    </select>
                    <button @click="abrirModal('cliente')" class="bg-sky-600 hover:bg-sky-700 text-white p-2 rounded-lg"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="bg-sky-100 p-4 rounded-lg text-center"><p>Total a pagar:</p><p class="text-4xl font-bold" x-text="`$${totalVenta}`"></p></div>
            <div><label for="monto_recibido">Monto Recibido:</label><input type="number" id="monto_recibido" x-model.number="montoRecibido" class="w-full p-2 border rounded-lg text-lg"></div>
            
            <div class="bg-sky-100 p-4 rounded-lg text-center"><p>Cambio:</p><p class="text-3xl font-bold" x-text="textoCambio"></p></div>

            <div>
                <label class="font-semibold">Método de Pago</label>
                <div class="grid grid-cols-3 gap-2 mt-2">
                    <button @click="metodoPago = 'efectivo'" :class="{'bg-sky-600 text-white': metodoPago === 'efectivo', 'bg-sky-100 text-sky-700 hover:bg-sky-200': metodoPago !== 'efectivo'}" class="p-2 rounded-lg text-sm transition-colors">Efectivo</button>
                    <button @click="metodoPago = 'tarjeta'" :class="{'bg-sky-600 text-white': metodoPago === 'tarjeta', 'bg-sky-100 text-sky-700 hover:bg-sky-200': metodoPago !== 'tarjeta'}" class="p-2 rounded-lg text-sm transition-colors">Tarjeta</button>
                    <button @click="metodoPago = 'transferencia'" :class="{'bg-sky-600 text-white': metodoPago === 'transferencia', 'bg-sky-100 text-sky-700 hover:bg-sky-200': metodoPago !== 'transferencia'}" class="p-2 rounded-lg text-sm transition-colors">Transferencia</button>
                </div>
            </div>
        </div>
        <div class="mt-auto pt-4 space-y-2 border-t">
            <button @click="abrirModal('apartado')" :disabled="esClienteGeneral() || carrito.length === 0" class="w-full bg-sky-600 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-inbox mr-2"></i> CREAR APARTADO</button>
            <button @click="finalizarVenta" :disabled="carrito.length === 0 || montoRecibido < totalVenta" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-money-check-dollar mr-2"></i> FINALIZAR VENTA</button>
            <button @click="restablecerVenta" :disabled="carrito.length === 0" class="w-full bg-red-500 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-times mr-2"></i> CANCELAR</button>
        </div>
    </div>
</div>