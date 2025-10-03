<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TPV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
        }
        [x-cloak] { display: none !important; }
        /* Estilos para el modal */
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
    </style>
</head>
<body x-data="appTPV()">

    <div 
        x-show="mostrarNotificacion" 
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
        :class="notificacion.exito ? 'bg-green-600' : 'bg-red-600'"
        class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 max-w-sm"
    >
        <span x-text="notificacion.mensaje"></span>
    </div>

    <div x-show="mostrarModalUniversal" x-cloak class="modal-container">
        <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-xl" @click.away="mostrarModalUniversal = false">
            <h3 class="text-2xl font-bold mb-4 border-b pb-2" x-text="tituloModal"></h3>
            <div x-html="contenidoModal" class="overflow-y-auto max-h-96"></div>
            <div class="mt-6 flex justify-end space-x-2">
                <button @click="mostrarModalUniversal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cerrar</button>
            </div>
        </div>
    </div>


    <header class="bg-white rounded-lg shadow-xl p-4 mb-4 mx-4 sm:mx-8 mt-4">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Panel TPV (Laravel/MySQL)</h1>
            <button @click="obtenerProductos()" class="bg-gray-200 text-gray-800 text-sm font-semibold px-3 py-1.5 rounded-full hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrows-rotate mr-1" :class="{ 'fa-spin': cargando }"></i> Recargar Inventario
            </button>
        </div>
        
        <nav class="mt-4 border-t pt-2">
            <div class="flex space-x-2 sm:space-x-4">
                <template x-for="pestaña in pestañas" :key="pestaña.nombre">
                    <button
                        @click="setPestañaActiva(pestaña.nombre)"
                        :class="{'bg-indigo-600 text-white shadow-md': pestañaActiva === pestaña.nombre, 'bg-gray-200 text-gray-700 hover:bg-gray-300': pestañaActiva !== pestaña.nombre}"
                        class="px-3 py-1.5 rounded-full font-semibold text-sm transition-colors duration-200 flex items-center gap-2"
                    >
                        <i :class="pestaña.icono"></i>
                        <span x-text="pestaña.nombre"></span>
                    </button>
                </template>
            </div>
        </nav>
    </header>

    <div class="mx-4 sm:mx-8">
        
        <div x-show="pestañaActiva === 'Inventario'" class="grid grid-cols-12 gap-4 lg:gap-6">
            
            <div class="col-span-12 lg:col-span-5 bg-white rounded-lg shadow-xl p-4 flex flex-col min-h-[70vh]">
                <h2 class="text-xl font-bold border-b pb-2 mb-4 text-indigo-700"><i class="fas fa-boxes mr-2"></i> Inventario de Productos</h2>
                <div class="relative mb-4">
                    <input 
                        type="text" 
                        x-model="busqueda" 
                        placeholder="Buscar Producto por nombre..." 
                        class="w-full pl-10 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <div class="flex-1 overflow-y-auto">
                    <template x-if="cargando">
                        <p class="text-center py-8 text-indigo-500 font-semibold"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos desde MySQL...</p>
                    </template>
                    <template x-if="!cargando && productos.length === 0">
                        <p class="text-center py-8 text-gray-500">
                            No hay productos cargados en la base de datos.<br>
                            <button @click="mostrarModalAñadirProducto = true" class="mt-2 text-indigo-600 font-semibold hover:underline">Añadir uno ahora</button>
                        </p>
                    </template>
                    <table class="w-full text-sm text-left text-gray-500 rounded-lg overflow-hidden" x-show="!cargando && productos.length > 0">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-3 sm:px-6 py-3">Producto</th> 
                                <th class="px-3 sm:px-6 py-3 text-center">Stock</th>
                                <th class="px-3 sm:px-6 py-3">Precio</th>
                                <th class="px-3 sm:px-6 py-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="producto in productosFiltrados" :key="producto.id">
                                <tr class="bg-white hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-3 sm:px-6 py-4 font-medium text-gray-900" x-text="producto.nombre"></td>
                                    <td class="px-3 sm:px-6 py-4 text-center" x-text="producto.stock"></td>
                                    <td class="px-3 sm:px-6 py-4" x-text="`$${parseFloat(producto.precio).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`"></td> 
                                    <td class="px-3 sm:px-6 py-4 text-center">
                                        <button 
                                            @click="añadirACarrito(producto)"
                                            class="text-blue-600 hover:text-blue-800 text-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            :disabled="producto.stock <= 0"
                                            title="Añadir al carrito"
                                        >
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex gap-4">
                    <button @click="mostrarModalAñadirProducto = true" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 text-center transition-colors">
                        Añadir Producto (MySQL)
                    </button>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-3 bg-white rounded-lg shadow-xl p-4 flex flex-col min-h-[70vh]">
                <h2 class="text-xl font-bold border-b pb-2 mb-4 text-gray-700"><i class="fas fa-shopping-cart mr-2"></i> Carrito de Ventas</h2>
                <div class="flex-1 overflow-y-auto space-y-3">
                    <template x-if="carrito.length === 0">
                        <p class="text-gray-500 text-center mt-8">El carrito está vacío.</p>
                    </template>
                    <template x-for="item in carrito" :key="item.id">
                        <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                            <img :src="item.image" :alt="item.nombre" class="w-10 h-10 object-cover rounded-md mr-3" 
                                onerror="this.onerror=null; this.src='https://placehold.co/150x150/CCCCCC/333333?text=Sin+Imagen'">
                            <div class="flex-grow">
                                <p class="font-semibold text-sm" x-text="item.nombre"></p>
                                <p class="text-xs text-gray-600" x-text="`Subtotal: $${ (item.precio * item.cantidad).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }`"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    x-model.number="item.cantidad" 
                                    @change="actualizarCantidad(item.id, item.cantidad)"
                                    min="1" 
                                    :max="item.stock" 
                                    class="w-10 text-center border rounded-lg text-sm"
                                />
                                <button @click="eliminarDeCarrito(item.id)" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar del carrito">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-4 pt-4 border-t-2">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-lg">Total:</span>
                        <span class="font-bold text-2xl text-indigo-600" x-text="`$${totalVenta.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`"></span>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4 bg-white rounded-lg shadow-xl p-4 flex flex-col min-h-[70vh]">
                <h2 class="text-xl font-bold border-b pb-2 mb-4 text-gray-700"><i class="fas fa-cash-register mr-2"></i> Caja y Pago</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="font-semibold text-gray-700">Cliente</label>
                        <div class="flex items-center gap-2">
                            <select 
                                class="w-full p-2 border rounded-lg bg-white" 
                                x-model.number="clienteSeleccionadoId"
                            >
                                <template x-for="cliente in clientes" :key="cliente.id">
                                    <option :value="cliente.id" x-text="cliente.name"></option>
                                </template>
                            </select>
                            <button @click="notificar('La funcionalidad de añadir cliente requiere la creación de la interfaz y un endpoint POST en Laravel.', false)" class="bg-black text-white p-2 rounded-lg hover:bg-gray-800 whitespace-nowrap transition-colors"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="text-gray-600">Total a pagar:</p>
                        <p class="text-4xl font-bold text-gray-800" x-text="`$${totalVenta.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`"></p>
                    </div>
                    
                    <div>
                        <label for="monto_recibido" class="font-semibold text-gray-700">Monto Recibido:</label>
                        <input 
                            type="number" 
                            id="monto_recibido" 
                            x-model.number="montoRecibido"
                            @input="calcularCambio"
                            class="w-full p-2 border rounded-lg text-lg" 
                            placeholder="$0"
                        />
                    </div>
                    
                    <div class="bg-yellow-100 p-4 rounded-lg text-center">
                        <p class="text-gray-600">Cambio:</p>
                        <p class="text-3xl font-bold text-gray-800" x-text="textoCambio"></p>
                    </div>
                </div>
                
                <div class="mt-auto pt-4 space-y-2 border-t">
                    <button 
                        @click="finalizarVenta" 
                        :disabled="carrito.length === 0 || montoRecibido < totalVenta" 
                        class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                    >
                        <i class="fas fa-money-check-dollar mr-2"></i> FINALIZAR VENTA Y ACTUALIZAR STOCK
                    </button>
                    <button 
                        @click="restablecerEstado" 
                        :disabled="carrito.length === 0" 
                        class="w-full bg-red-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                    >
                        <i class="fas fa-times mr-2"></i> CANCELAR Y VACIAR CARRITO
                    </button>
                </div>
            </div>
            
            <div x-show="mostrarModalAñadirProducto" x-cloak class="modal-container">
                <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-lg" @click.away="mostrarModalAñadirProducto = false">
                    <h3 class="text-2xl font-bold mb-4">Añadir Nuevo Producto (MySQL)</h3>
                    <p class="text-sm text-gray-500 mb-4">Se enviará al endpoint POST /api/inventory/productos de Laravel.</p>
                    <div class="space-y-4">
                        <div>
                            <label for="newProductName" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" id="newProductName" x-model="nuevoProductoNombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="newProductPrice" class="block text-sm font-medium text-gray-700">Precio</label>
                            <input type="number" step="0.01" id="newProductPrice" x-model.number="nuevoProductoPrecio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="newProductStock" class="block text-sm font-medium text-gray-700">Stock</label>
                            <input type="number" id="newProductStock" x-model.number="nuevoProductoStock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button @click="mostrarModalAñadirProducto = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cancelar</button>
                        <button @click="guardarNuevoProducto" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">Guardar en DB</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div x-show="pestañaActiva === 'Estadísticas'" x-init="pestañaActiva === 'Estadísticas' && obtenerEstadisticas()" class="bg-white rounded-lg shadow-xl p-6 min-h-[70vh]">
            <h2 class="text-2xl font-bold border-b pb-3 mb-6 text-indigo-700"><i class="fas fa-chart-line mr-2"></i> Reporte de Estadísticas</h2>
            
            <template x-if="cargandoEstadisticas">
                <p class="text-center py-8 text-indigo-500 font-semibold"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando datos estadísticos...</p>
            </template>
            <template x-if="!cargandoEstadisticas">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-green-100 p-5 rounded-lg border-l-4 border-green-600 shadow-md">
                        <p class="text-lg font-medium text-green-700">Ventas del Día</p>
                        <p class="text-3xl font-extrabold text-green-800" x-text="`$${estadisticas.ventasHoy.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`"></p>
                    </div>
                    <div class="bg-blue-100 p-5 rounded-lg border-l-4 border-blue-600 shadow-md">
                        <p class="text-lg font-medium text-blue-700">Ventas del Mes</p>
                        <p class="text-3xl font-extrabold text-blue-800" x-text="`$${estadisticas.ventasMes.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`"></p>
                    </div>
                    <div class="bg-red-100 p-5 rounded-lg border-l-4 border-red-600 shadow-md">
                        <p class="text-lg font-medium text-red-700">Egresos (DUMMY)</p>
                        <p class="text-3xl font-extrabold text-red-800" x-text="`$${estadisticas.egresos.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`"></p>
                    </div>
                    <div class="bg-yellow-100 p-5 rounded-lg border-l-4 border-yellow-600 shadow-md">
                        <p class="text-lg font-medium text-yellow-700">Apartados Vigentes</p>
                        <p class="text-3xl font-extrabold text-yellow-800" x-text="estadisticas.apartadosVigentes.length"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white p-5 rounded-lg shadow-lg border">
                        <h3 class="text-xl font-semibold mb-4 text-orange-600 border-b pb-2"><i class="fas fa-exclamation-triangle mr-2"></i> Productos con Bajo Stock (<= 10)</h3>
                        <div class="max-h-64 overflow-y-auto">
                            <template x-if="estadisticas.productosBajoStock.length === 0">
                                <p class="text-gray-500 text-center py-4">¡Todo en orden! No hay productos con bajo stock.</p>
                            </template>
                            <template x-for="p in estadisticas.productosBajoStock" :key="p.id">
                                <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                                    <span class="font-medium" x-text="p.nombre"></span>
                                    <span class="text-red-500 font-bold" x-text="p.existencias + ' unid.'"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-lg shadow-lg border">
                        <h3 class="text-xl font-semibold mb-4 text-purple-600 border-b pb-2"><i class="fas fa-users-viewfinder mr-2"></i> Clientes con Apartados Vigentes</h3>
                        <div class="max-h-64 overflow-y-auto">
                            <template x-if="estadisticas.apartadosVigentes.length === 0">
                                <p class="text-gray-500 text-center py-4">No hay apartados vigentes actualmente.</p>
                            </template>
                            <template x-for="a in estadisticas.apartadosVigentes" :key="a.id">
                                <div class="py-2 border-b last:border-b-0">
                                    <p class="font-medium text-gray-800" x-text="a.cliente_nombre"></p>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>Total: $<span x-text="a.monto_total"></span></span>
                                        <span>Vencimiento: <span x-text="new Date(a.fecha_vencimiento).toLocaleDateString()"></span></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <div x-show="pestañaActiva === 'Proveedores'" x-init="pestañaActiva === 'Proveedores' && obtenerProveedores()" class="bg-white rounded-lg shadow-xl p-6 min-h-[70vh]">
            <h2 class="text-2xl font-bold border-b pb-3 mb-6 text-indigo-700"><i class="fas fa-truck-fast mr-2"></i> Listado de Proveedores</h2>
            
            <template x-if="cargandoProveedores">
                <p class="text-center py-8 text-indigo-500 font-semibold"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando proveedores...</p>
            </template>
            <template x-if="!cargandoProveedores && proveedores.length === 0">
                <p class="text-center py-8 text-gray-500">No hay proveedores registrados en la base de datos.</p>
            </template>
            <table class="w-full text-sm text-left text-gray-500 rounded-lg overflow-hidden" x-show="!cargandoProveedores && proveedores.length > 0">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Contacto</th>
                        <th class="px-6 py-3">Teléfono</th>
                        <th class="px-6 py-3">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="p in proveedores" :key="p.id">
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="p.nombre"></td>
                            <td class="px-6 py-4" x-text="p.persona_contacto"></td>
                            <td class="px-6 py-4" x-text="p.telefono"></td>
                            <td class="px-6 py-4" x-text="p.email"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <div x-show="pestañaActiva === 'Apartados'" x-init="pestañaActiva === 'Apartados' && obtenerApartados()" class="bg-white rounded-lg shadow-xl p-6 min-h-[70vh]">
            <h2 class="text-2xl font-bold border-b pb-3 mb-6 text-indigo-700"><i class="fas fa-inbox mr-2"></i> Listado de Apartados</h2>
            
            <template x-if="cargandoApartados">
                <p class="text-center py-8 text-indigo-500 font-semibold"><i class="fas fa-spinner fa-spin mr-2"></i> Cargando apartados...</p>
            </template>
            <template x-if="!cargandoApartados && apartados.length === 0">
                <p class="text-center py-8 text-gray-500">No hay apartados activos.</p>
            </template>
            <table class="w-full text-sm text-left text-gray-500 rounded-lg overflow-hidden" x-show="!cargandoApartados && apartados.length > 0">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Cliente</th>
                        <th class="px-6 py-3">Teléfono</th>
                        <th class="px-6 py-3">Monto Total</th>
                        <th class="px-6 py-3">Vencimiento</th>
                        <th class="px-6 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="a in apartados" :key="a.id">
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="a.nombre_cliente"></td>
                            <td class="px-6 py-4" x-text="a.telefono"></td>
                            <td class="px-6 py-4 font-bold" x-text="`$${a.monto.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`"></td>
                            <td class="px-6 py-4" x-text="new Date(a.fecha_vencimiento).toLocaleDateString()"></td>
                            <td class="px-6 py-4">
                                <span 
                                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-800': a.estado === 'vigente',
                                        'bg-red-100 text-red-800': a.estado === 'vencido',
                                        'bg-gray-100 text-gray-800': a.estado === 'finalizado'
                                    }"
                                    x-text="a.estado"
                                ></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </div>

<script>
    // Se utiliza el nombre 'appTPV' para el componente Alpine
    function appTPV() {
        // Función auxiliar para mostrar el modal universal (Recibo)
        const mostrarModal = (titulo, contenido) => {
            this.tituloModal = titulo;
            this.contenidoModal = contenido;
            this.mostrarModalUniversal = true;
        };
        
        return {
            // Estado de la Aplicación (Variables en español)
            cargando: true,
            productos: [],
            clientes: [],
            carrito: [],
            busqueda: '',
            clienteSeleccionadoId: 1,
            montoRecibido: null,
            
            // Pestañas
            pestañas: [
                { nombre: 'Inventario', icono: 'fas fa-cash-register' },
                { nombre: 'Estadísticas', icono: 'fas fa-chart-line' },
                { nombre: 'Proveedores', icono: 'fas fa-truck-fast' },
                { nombre: 'Apartados', icono: 'fas fa-inbox' },
            ],
            pestañaActiva: 'Inventario',

            // Modal y Notificación
            mostrarNotificacion: false,
            notificacion: { mensaje: '', exito: true },
            mostrarModalAñadirProducto: false,
            mostrarModalUniversal: false,
            tituloModal: '',
            contenidoModal: '',
            
            // Nuevas variables de Producto (Nomenclatura completa en español)
            nuevoProductoNombre: '',
            nuevoProductoPrecio: 0.00,
            nuevoProductoStock: 0,

            // INICIO DE CAMBIOS DE ALPINE.JS
            // Nuevas variables para Pestañas de Datos
            cargandoEstadisticas: false,
            estadisticas: {
                ventasHoy: 0,
                ventasMes: 0,
                egresos: 0,
                productosBajoStock: [],
                apartadosVigentes: []
            },
            
            cargandoProveedores: false,
            proveedores: [],
            
            cargandoApartados: false,
            apartados: [],
            // FIN DE NUEVAS VARIABLES
            
            // Inicialización
            init() {
                // Se usa window.location.origin para rutas absolutas
                this.obtenerProductos();
                this.obtenerClientes();
            },

            setPestañaActiva(nombrePestaña) {
                this.pestañaActiva = nombrePestaña;
            },

            // --- CÁLCULOS (Propiedades computadas en español) ---
            get totalVenta() {
                // CAMBIADO: usa item.precio y item.cantidad
                return this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
            },

            get textoCambio() {
                const total = this.totalVenta;
                const recibido = this.montoRecibido;
                if (recibido === null || recibido < total) {
                    const falta = Math.max(0, total - (recibido || 0));
                    return `Faltan: $${falta.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }
                const cambio = recibido - total;
                return `$${cambio.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            },

            get productosFiltrados() {
                if (this.busqueda === '') {
                    return this.productos;
                }
                const terminoBusqueda = this.busqueda.toLowerCase();
                // CAMBIADO: filtra por producto.nombre
                return this.productos.filter(producto =>
                    producto.nombre.toLowerCase().includes(terminoBusqueda)
                );
            },
            
            // --- GESTIÓN DE CARRITO (Funciones en español) ---
            añadirACarrito(producto) {
                const itemExistente = this.carrito.find(item => item.id === producto.id);
                
                if (itemExistente) {
                    // CAMBIADO: usa item.cantidad
                    if (itemExistente.cantidad < producto.stock) {
                        itemExistente.cantidad++;
                        this.notificar(`Añadido ${producto.nombre}. Cantidad: ${itemExistente.cantidad}`, true);
                    } else {
                        this.notificar(`Stock insuficiente para ${producto.nombre}. Máx: ${producto.stock}`, false);
                    }
                } else {
                    if (producto.stock > 0) {
                        // CAMBIADO: El item del carrito almacena 'cantidad' en lugar de 'quantity'
                        this.carrito.push({ ...producto, cantidad: 1 });
                        this.notificar(`Añadido ${producto.nombre} al carrito.`, true);
                    } else {
                        this.notificar(`${producto.nombre} está agotado.`, false);
                    }
                }
            },

            actualizarCantidad(productoId, nuevaCantidad) {
                const item = this.carrito.find(i => i.id === productoId);
                const productoEnStock = this.productos.find(p => p.id === productoId);

                if (!item || !productoEnStock) return;

                const cantidad = parseInt(nuevaCantidad) || 1;

                if (cantidad < 1) {
                    item.cantidad = 1;
                    this.notificar('La cantidad mínima es 1.', false);
                } else if (cantidad > productoEnStock.stock) {
                    item.cantidad = productoEnStock.stock;
                    this.notificar(`Máxima cantidad permitida: ${productoEnStock.stock}`, false);
                } else {
                    item.cantidad = cantidad;
                }
                
                this.calcularCambio();
            },

            eliminarDeCarrito(productoId) {
                this.carrito = this.carrito.filter(item => item.id !== productoId);
                this.notificar('Producto eliminado del carrito.', false);
                this.calcularCambio();
            },
            
            // --- CONEXIÓN CON LARAVEL/API (Funciones en español) ---

            // 1. Obtener Productos
            async obtenerProductos() {
                this.cargando = true;
                try {
                    const response = await fetch(`${window.location.origin}/api/inventory/productos`);
                    const data = await response.json();
                    
                    if (response.ok) {
                        // AJUSTE: La API devuelve 'existencias', la mapeamos a 'stock' para el frontend.
                        this.productos = data.productos.map(p => ({
                            ...p,
                            nombre: p.nombre, 
                            precio: parseFloat(p.precio), 
                            stock: parseInt(p.existencias) // CAMBIO: Usar p.existencias
                        }));
                        this.notificar('Inventario cargado con éxito desde MySQL.', true);
                    } else {
                        throw new Error(data.error || 'Error desconocido al cargar productos.');
                    }
                } catch (error) {
                    console.error('Error fetching products:', error);
                    this.notificar(`Error de API al cargar productos: ${error.message}`, false);
                } finally {
                    this.cargando = false;
                }
            },

            // 2. Obtener Clientes
            async obtenerClientes() {
                 try {
                    const response = await fetch(`${window.location.origin}/api/inventory/clientes`);
                    const data = await response.json();
                    // ESPERANDO: la clave 'clientes' de la API.
                    if (response.ok) {
                        this.clientes = data.clientes;
                    } else {
                         throw new Error(data.error || 'Error al cargar clientes.');
                    }
                } catch (error) {
                    console.error('Error fetching clients:', error);
                    this.notificar('Error al cargar la lista de clientes (dummy).', false);
                }
            },

            // 3. Añadir Nuevo Producto
            async guardarNuevoProducto() {
                if (!this.nuevoProductoNombre || this.nuevoProductoPrecio <= 0 || this.nuevoProductoStock < 0) {
                    this.notificar('Por favor, complete todos los campos correctamente.', false);
                    return;
                }

                this.mostrarModalAñadirProducto = false;
                this.cargando = true;

                try {
                    const response = await fetch(`${window.location.origin}/api/inventory/productos`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        // AJUSTE: Envía 'existencias' a la API, que es lo que espera el controlador
                        body: JSON.stringify({
                            nombre: this.nuevoProductoNombre,
                            precio: this.nuevoProductoPrecio,
                            existencias: this.nuevoProductoStock // CAMBIO: Enviamos 'existencias'
                        })
                    });

                    const data = await response.json();
                    
                    if (response.ok) {
                        // Limpiar campos del modal
                        this.nuevoProductoNombre = '';
                        this.nuevoProductoPrecio = 0.00;
                        this.nuevoProductoStock = 0;
                        
                        // Recargar la lista de productos
                        await this.obtenerProductos();
                        this.notificar(`Producto "${data.producto.nombre}" guardado en MySQL.`, true);

                    } else {
                        const errorMsg = data.mensaje || 'Error al guardar el producto.';
                        this.notificar(errorMsg, false);
                    }

                } catch (error) {
                    console.error('Error saving product:', error);
                    this.notificar(`Error al conectar con el servidor: ${error.message}`, false);
                } finally {
                    this.cargando = false;
                }
            },
            
            // 4. Finalizar Venta y Actualizar Stock (Transacción)
            async finalizarVenta() {
                if (this.carrito.length === 0) {
                    this.notificar('El carrito está vacío.', false);
                    return;
                }
                if (this.montoRecibido < this.totalVenta) {
                    this.notificar('El monto recibido es insuficiente.', false);
                    return;
                }
                
                this.cargando = true;

                // Mapeamos el carrito para el formato que espera el controlador
                const itemsVenta = this.carrito.map(item => ({
                    id: item.id,
                    cantidad: item.cantidad 
                }));

                try {
                    const response = await fetch(`${window.location.origin}/api/inventory/ventas/finalizar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        // Enviamos los campos que el controlador ahora espera
                        body: JSON.stringify({
                            items: itemsVenta,
                            clienteIdSeleccionado: this.clienteSeleccionadoId,
                            montoTotal: this.totalVenta 
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Usamos data.id_venta que es lo que el controlador modificado devuelve
                        this.notificar(`¡Venta #${data.id_venta} finalizada! Stock actualizado.`, true);
                        this.mostrarRecibo(data.id_venta);
                        await this.obtenerProductos(); // Recarga el inventario para ver el stock actualizado
                        this.restablecerEstado();

                    } else if (response.status === 409) {
                        const errorStock = data.error.stock ? data.error.stock[0] : 'Stock insuficiente. Recargue la página.';
                        this.notificar(errorStock, false);
                    } else {
                        throw new Error(data.error || 'Error desconocido al finalizar la venta.');
                    }
                } catch (error) {
                    console.error('Error finalizing sale:', error);
                    this.notificar(`Error de transacción: ${error.message}`, false);
                } finally {
                    this.cargando = false;
                }
            },

            // 5. Obtener Estadísticas (NUEVA FUNCIÓN)
            async obtenerEstadisticas() {
                this.cargandoEstadisticas = true;
                try {
                    // Ruta asumida: /api/estadisticas
                    const response = await fetch(`${window.location.origin}/api/estadisticas`); 
                    const data = await response.json();
                    
                    if (response.ok) {
                        this.estadisticas.ventasHoy = parseFloat(data.ventasHoy || 0);
                        this.estadisticas.ventasMes = parseFloat(data.ventasMes || 0);
                        this.estadisticas.egresos = parseFloat(data.egresos || 0);
                        this.estadisticas.productosBajoStock = data.productosBajoStock;
                        this.estadisticas.apartadosVigentes = data.apartadosVigentes;
                        this.notificar('Estadísticas cargadas con éxito.', true);
                    } else {
                        throw new Error(data.error || 'Error desconocido al cargar estadísticas.');
                    }
                } catch (error) {
                    console.error('Error fetching stats:', error);
                    this.notificar(`Error de API al cargar estadísticas: ${error.message}`, false);
                } finally {
                    this.cargandoEstadisticas = false;
                }
            },
            
            // 6. Obtener Proveedores (NUEVA FUNCIÓN)
            async obtenerProveedores() {
                this.cargandoProveedores = true;
                try {
                    // Ruta asumida: /api/proveedores
                    const response = await fetch(`${window.location.origin}/api/proveedores`);
                    const data = await response.json();
                    
                    if (response.ok) {
                        this.proveedores = data.proveedores;
                        this.notificar('Proveedores cargados con éxito.', true);
                    } else {
                        throw new Error(data.error || 'Error desconocido al cargar proveedores.');
                    }
                } catch (error) {
                    console.error('Error fetching suppliers:', error);
                    this.notificar(`Error de API al cargar proveedores: ${error.message}`, false);
                } finally {
                    this.cargandoProveedores = false;
                }
            },
            
            // 7. Obtener Apartados (NUEVA FUNCIÓN)
            async obtenerApartados() {
                this.cargandoApartados = true;
                try {
                    // Ruta asumida: /api/inventory/apartados
                    const response = await fetch(`${window.location.origin}/api/inventory/apartados`);
                    const data = await response.json();
                    
                    if (response.ok) {
                        this.apartados = data.apartados; 
                        this.notificar('Apartados cargados con éxito.', true);
                    } else {
                        throw new Error(data.error || 'Error desconocido al cargar apartados.');
                    }
                } catch (error) {
                    console.error('Error fetching layaways:', error);
                    this.notificar(`Error de API al cargar apartados: ${error.message}`, false);
                } finally {
                    this.cargandoApartados = false;
                }
            },

            // --- UTILIDADES (Funciones en español) ---
            calcularCambio() {
                // Se activa automáticamente con x-model en montoRecibido
            },

            mostrarRecibo(idVenta) {
                const cliente = this.clientes.find(c => c.id === this.clienteSeleccionadoId)?.name || 'N/A';
                const fecha = new Date().toLocaleString('es-AR');

                let filasItem = this.carrito.map(item => `
                    <div class="flex justify-between text-sm py-1 border-b border-dashed">
                        <span>${item.nombre} x${item.cantidad}</span>
                        <span>$${(item.precio * item.cantidad).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                    </div>
                `).join('');

                const contenidoRecibo = `
                    <div class="p-4 bg-white shadow-inner rounded-lg font-mono">
                        <h4 class="text-center text-xl font-bold mb-3 border-b pb-2">RECIBO DE VENTA</h4>
                        <div class="text-xs mb-3 space-y-1">
                            <p><strong>Venta ID:</strong> #${idVenta}</p>
                            <p><strong>Cliente:</strong> ${cliente}</p>
                            <p><strong>Fecha:</strong> ${fecha}</p>
                        </div>
                        
                        <div class="mb-4 space-y-1">${filasItem}</div>
                        
                        <div class="mt-2 text-base font-semibold border-t pt-2">
                            <div class="flex justify-between">
                                <span>TOTAL:</span>
                                <span>$${this.totalVenta.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                            </div>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span>Monto Recibido:</span>
                            <span>$${(this.montoRecibido || 0).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg mt-2 pt-2 border-t border-dashed">
                            <span>CAMBIO:</span>
                            <span :class="{'text-red-600': textoCambio.includes('Faltan')}">${this.textoCambio.includes('Faltan') ? this.textoCambio : `$${(this.montoRecibido - this.totalVenta).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`}</span>
                        </div>
                        <p class="text-center mt-4 text-sm">¡Gracias por su compra!</p>
                    </div>
                `;
                mostrarModal(`Recibo de Venta #${idVenta}`, contenidoRecibo);
            },
            
            restablecerEstado() {
                this.carrito = [];
                this.montoRecibido = null;
                this.clienteSeleccionadoId = 1;
                this.busqueda = '';
                this.calcularCambio();
            },
            
            notificar(mensaje, exito = true) {
                this.notificacion = { mensaje, exito };
                this.mostrarNotificacion = true;
                setTimeout(() => this.mostrarNotificacion = false, 3000);
            }
        }
    }
</script>

</body>
</html>