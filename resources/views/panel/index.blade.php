<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - TPV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; min-height: 100vh; }
        [x-cloak] { display: none !important; }
        .modal-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 50; }
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
        <div 
            x-show="mostrarNotificacion" 
            x-transition
            :class="notificacion.exito ? 'bg-green-600' : 'bg-red-600'"
            class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 max-w-sm"
        >
            <span x-text="notificacion.mensaje"></span>
        </div>

        <div x-show="mostrarModalUniversal" class="modal-container">
            <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-xl" @click.away="mostrarModalUniversal = false">
                <h3 class="text-2xl font-bold mb-4 border-b pb-2" x-text="tituloModal"></h3>
                <div x-html="contenidoModal" class="overflow-y-auto max-h-96"></div>
                <div class="mt-6 flex justify-end"><button @click="mostrarModalUniversal = false" class="px-4 py-2 bg-gray-200 rounded-lg">Cerrar</button></div>
            </div>
        </div>

        <header class="bg-white rounded-lg shadow-xl p-4 mb-4 mx-4 sm:mx-8 mt-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl sm:text-2xl font-bold">Panel TPV</h1>
                <button @click="init()" class="bg-gray-200 text-sm px-3 py-1.5 rounded-full hover:bg-gray-300">
                    <i class="fas fa-arrows-rotate mr-1" :class="{ 'fa-spin': cargando }"></i> Recargar
                </button>
            </div>
            <nav class="mt-4 border-t pt-2">
                <div class="flex space-x-2 sm:space-x-4">
                    <template x-for="pestaña in pestañas" :key="pestaña.nombre">
                        <button @click="pestañaActiva = pestaña.nombre" :class="{'bg-indigo-600 text-white': pestañaActiva === pestaña.nombre, 'bg-gray-200 hover:bg-gray-300': pestañaActiva !== pestaña.nombre}" class="px-3 py-1.5 rounded-full text-sm flex items-center gap-2">
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
                    <h2 class="text-xl font-bold border-b pb-2 mb-4"><i class="fas fa-boxes mr-2"></i> Inventario</h2>
                    <div class="relative mb-4"><input type="text" x-model="busqueda" placeholder="Buscar..." class="w-full pl-10 pr-4 py-2 border rounded-full"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i></div>
                    <div class="flex-1 overflow-y-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-gray-50 sticky top-0"><tr><th class="px-6 py-3">Producto</th><th class="px-6 py-3 text-center">Stock</th><th class="px-6 py-3">Precio</th><th class="px-6 py-3 text-center">Acción</th></tr></thead>
                            <tbody class="divide-y">
                                <template x-for="producto in productosFiltrados" :key="producto.id">
                                    <tr class="hover:bg-gray-50"><td class="px-6 py-4 font-medium" x-text="producto.nombre"></td><td class="px-6 py-4 text-center" x-text="producto.existencias"></td><td class="px-6 py-4" x-text="`$${producto.precio}`"></td><td class="px-6 py-4 text-center"><button @click="añadirACarrito(producto)" :disabled="producto.existencias <= 0" class="text-blue-600 disabled:opacity-50"><i class="fas fa-plus-circle text-lg"></i></button></td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-3 bg-white rounded-lg shadow-xl p-4 flex flex-col">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4"><i class="fas fa-shopping-cart mr-2"></i> Carrito</h2>
                    <div class="flex-1 overflow-y-auto space-y-3">
                        <template x-if="carrito.length === 0"><p class="text-center mt-8">El carrito está vacío.</p></template>
                        <template x-for="item in carrito" :key="item.id">
                            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                                <div class="flex-grow"><p class="font-semibold text-sm" x-text="item.nombre"></p><p class="text-xs" x-text="`Subtotal: $${(item.precio * item.cantidad)}`"></p></div>
                                <div class="flex items-center gap-2"><input type="number" x-model.number="item.cantidad" @change="actualizarCantidad(item.id, $event.target.value)" min="1" :max="item.existencias" class="w-12 text-center border rounded-lg text-sm"><button @click="eliminarDeCarrito(item.id)" class="text-red-500"><i class="fas fa-trash-alt text-lg"></i></button></div>
                            </div>
                        </template>
                    </div>
                    <div class="mt-4 pt-4 border-t-2"><div class="flex justify-between items-center"><span class="font-semibold">Total:</span><span class="font-bold text-2xl text-indigo-600" x-text="`$${totalVenta}`"></span></div></div>
                </div>
                <div class="col-span-12 lg:col-span-4 bg-white rounded-lg shadow-xl p-4 flex flex-col">
                    <h2 class="text-xl font-bold border-b pb-2 mb-4"><i class="fas fa-cash-register mr-2"></i> Caja</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="font-semibold">Cliente</label>
                            <div class="flex items-center gap-2">
                                <select class="w-full p-2 border rounded-lg" x-model.number="clienteSeleccionadoId">
                                    <template x-for="cliente in clientes" :key="cliente.id">
                                        <option :value="cliente.id" x-text="cliente.nombre"></option>
                                    </template>
                                </select>
                                <button @click="mostrarModalAñadirCliente = true" class="bg-indigo-600 text-white p-2 rounded-lg"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center"><p>Total a pagar:</p><p class="text-4xl font-bold" x-text="`$${totalVenta}`"></p></div>
                        <div><label for="monto_recibido">Monto Recibido:</label><input type="number" id="monto_recibido" x-model.number="montoRecibido" class="w-full p-2 border rounded-lg text-lg"></div>
                        <div class="bg-yellow-100 p-4 rounded-lg text-center"><p>Cambio:</p><p class="text-3xl font-bold" x-text="textoCambio"></p></div>
                    </div>
                    <div class="mt-auto pt-4 space-y-2 border-t">
                        <button @click="finalizarVenta" :disabled="carrito.length === 0 || montoRecibido < totalVenta" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-money-check-dollar mr-2"></i> FINALIZAR VENTA</button>
                        <button @click="restablecerVenta" :disabled="carrito.length === 0" class="w-full bg-red-500 text-white font-bold py-3 rounded-lg disabled:bg-gray-400"><i class="fas fa-times mr-2"></i> CANCELAR</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
    function appTPV() {
        return {
            cargandoInicial: true,
            cargando: false,
            productos: [],
            clientes: [],
            carrito: [],
            pestañaActiva: 'Inventario',
            busqueda: '',
            clienteSeleccionadoId: 1,
            montoRecibido: null,
            mostrarModalAñadirCliente: false,
            pestañas: [
                { nombre: 'Inventario', icono: 'fas fa-cash-register' },
                { nombre: 'Estadísticas', icono: 'fas fa-chart-line' },
                { nombre: 'Proveedores', icono: 'fas fa-truck-fast' },
                { nombre: 'Apartados', icono: 'fas fa-inbox' },
            ],
            mostrarNotificacion: false,
            notificacion: { mensaje: '', exito: true },

            async init() {
                console.log('Alpine inicializado. Cargando datos...');
                this.cargandoInicial = true;
                try {
                    await Promise.all([
                        this.obtenerProductos(),
                        this.obtenerClientes()
                    ]);
                    console.log('Datos cargados. Sistema listo.');
                } catch (error) {
                    this.notificar("No se pudieron cargar los datos iniciales. El servidor falló.", false);
                } finally {
                    this.cargandoInicial = false;
                }
            },

            get totalVenta() {
                return this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0).toFixed(2);
            },
            get textoCambio() {
                if (this.montoRecibido === null || this.montoRecibido < this.totalVenta) {
                    return `$0.00`;
                }
                return `$${(this.montoRecibido - this.totalVenta).toFixed(2)}`;
            },
            get productosFiltrados() {
                if (!this.busqueda) return this.productos;
                return this.productos.filter(p => p.nombre.toLowerCase().includes(this.busqueda.toLowerCase()));
            },

            async fetchAPI(endpoint, options = {}) {
                this.cargando = true;
                try {
                    options.headers = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        ...options.headers,
                    };
                    const response = await fetch(`/api${endpoint}`, options);
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({ message: `Error ${response.status}` }));
                        throw new Error(errorData.message || `Error ${response.status}`);
                    }
                    return await response.json();
                } catch (error) {
                    this.notificar(`Error de API: ${error.message}`, false);
                    throw error;
                } finally {
                    this.cargando = false;
                }
            },
            async obtenerProductos() {
                const data = await this.fetchAPI('/inventory/productos');
                this.productos = data.productos;
            },
            async obtenerClientes() {
                const data = await this.fetchAPI('/inventory/clientes');
                this.clientes = data.clientes;
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
                const cant = parseInt(qty);
                if (cant < 1) item.cantidad = 1;
                else if (cant > item.existencias) item.cantidad = item.existencias;
                else item.cantidad = cant;
            },
            eliminarDeCarrito(id) {
                this.carrito = this.carrito.filter(i => i.id !== id);
            },
            restablecerVenta() {
                this.carrito = [];
                this.montoRecibido = null;
                this.clienteSeleccionadoId = 1;
            },
            async finalizarVenta() {
                // Lógica para finalizar la venta
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