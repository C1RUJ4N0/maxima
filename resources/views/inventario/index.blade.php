@extends('layouts.app')

@section('title', 'Inventario de Productos')

@section('content')
    <main class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="inventory()">

        <!-- Columna de Listado -->
        <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-lg shadow-xl border">
            <div class="flex justify-between items-center mb-4 flex-wrap gap-4">
                <h2 class="text-xl font-semibold text-gray-700">Productos en Stock</h2>
                <input type="text" x-model="searchInventory" placeholder="Buscar Producto..." class="w-full max-w-sm pl-4 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>
            <div class="overflow-x-auto">
                <template x-if="cargando">
                    <p class="text-center py-8 text-gray-500">Cargando inventario...</p>
                </template>
                <template x-if="!cargando && filteredInventory.length === 0">
                    <p class="text-center py-8 text-gray-500" x-text="searchInventory ? 'No se encontraron productos.' : 'No hay productos registrados.'"></p>
                </template>
                <template x-if="!cargando && filteredInventory.length > 0">
                    <table class="min-w-full bg-white rounded-lg shadow border">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Producto</th>
                                <th class="py-3 px-6 text-center">Existencias</th>
                                <th class="py-3 px-6 text-right">Precio</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            <template x-for="product in filteredInventory" :key="product.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left font-medium" x-text="product.nombre"></td>
                                    <td class="py-3 px-6 text-center" x-text="product.existencias"></td>
                                    <td class="py-3 px-6 text-right" x-text="`$${product.precio.toLocaleString('es-MX')}`"></td>
                                    <td class="py-3 px-6 text-center space-x-2">
                                        <button @click="openEditModal(product)" class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button @click="confirmDelete(product.id, product.nombre)" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>
            </div>
        </div>

        <!-- Columna de Registro (Ahora Botón que abre Modal) -->
        <div class="col-span-1 bg-white p-6 rounded-lg shadow-xl border h-fit sticky top-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Gestión de Productos</h2>
            <p class="text-gray-600 mb-4">Utiliza el modal para registrar un producto nuevo o editar uno existente.</p>
            <button @click="openCreateModal()" class="w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-lg">
                <i class="fas fa-plus mr-2"></i> Registrar Nuevo Producto
            </button>
        </div>
    </main>

    <!-- Modal de Creación/Edición -->
    <div x-cloak x-show="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div @click.away="showModal = false; resetForm()" class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 transform transition-all duration-300" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <h3 class="text-xl font-bold mb-4 text-gray-800" x-text="esEditando ? 'Editar Producto' : 'Registrar Nuevo Producto'"></h3>
            
            <form @submit.prevent="saveProducto()" class="space-y-4">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="nombre" x-model.defer="form.nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="precio" class="block text-sm font-medium text-gray-700">Precio</label>
                    <input type="number" step="0.01" id="precio" x-model.number.defer="form.precio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="existencias" class="block text-sm font-medium text-gray-700">Existencias (Stock)</label>
                    <input type="number" id="existencias" x-model.number.defer="form.existencias" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                
                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" @click="showModal = false; resetForm()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors duration-200" 
                        :class="cargandoForm ? 'bg-indigo-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                        :disabled="cargandoForm"
                        x-text="cargandoForm ? 'Guardando...' : (esEditando ? 'Actualizar Producto' : 'Guardar Producto')">
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Notificación (Toast) -->
    <div x-cloak x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 transform transition-all" :class="notification.success ? 'bg-green-500' : 'bg-red-500'">
        <span x-text="notification.message"></span>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('inventory', () => ({
                productos: [],
                cargando: true,
                cargandoForm: false,
                searchInventory: '',
                showModal: false,
                esEditando: false,
                form: {
                    id: null,
                    nombre: '',
                    precio: null,
                    existencias: null,
                },
                showNotification: false,
                notification: { message: '', success: true },

                init() {
                    this.fetchProductos();
                },

                // Lógica de filtrado en el cliente (rápido)
                get filteredInventory() {
                    if (this.searchInventory === '') {
                        return this.productos;
                    }
                    const searchTerm = this.searchInventory.toLowerCase();
                    return this.productos.filter(p => p.nombre.toLowerCase().includes(searchTerm));
                },

                // --- API CALLS ---
                async fetchProductos() {
                    this.cargando = true;
                    try {
                        // Endpoint: /api/inventario -> InventarioController::index
                        const response = await fetch('/api/inventario');
                        if (!response.ok) throw new Error('Error al cargar productos.');
                        const data = await response.json();
                        this.productos = data.productos;
                    } catch (error) {
                        this.notify('Error al cargar el inventario.', false);
                        console.error('Fetch Error:', error);
                    } finally {
                        this.cargando = false;
                    }
                },
                
                async saveProducto() {
                    this.cargandoForm = true;
                    const isUpdating = this.esEditando;
                    const url = isUpdating ? `/api/inventario/${this.form.id}` : '/api/inventario';
                    const method = isUpdating ? 'PUT' : 'POST';
                    
                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Requerido para Laravel
                            },
                            body: JSON.stringify({
                                nombre: this.form.nombre,
                                precio: this.form.precio,
                                existencias: this.form.existencias,
                            })
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            const message = errorData.message || `Error al ${isUpdating ? 'actualizar' : 'crear'} el producto.`;
                            throw new Error(message);
                        }

                        this.notify(`Producto ${isUpdating ? 'actualizado' : 'creado'} con éxito!`);
                        this.fetchProductos(); // Recargar la lista
                        this.showModal = false;
                        this.resetForm();

                    } catch (error) {
                        this.notify(error.message, false);
                        console.error('Save Error:', error);
                    } finally {
                        this.cargandoForm = false;
                    }
                },

                async confirmDelete(id, nombre) {
                    // Usar un modal o confirmación visual en lugar de window.confirm
                    if (!confirm(`¿Estás seguro de que quieres eliminar el producto: ${nombre}? Esta acción es irreversible.`)) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/api/inventario/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        if (!response.ok) throw new Error('Error al eliminar el producto.');

                        this.notify(`El producto "${nombre}" ha sido eliminado.`);
                        this.fetchProductos(); // Recargar la lista

                    } catch (error) {
                        this.notify(error.message, false);
                        console.error('Delete Error:', error);
                    }
                },

                // --- MODAL & UTILITY FUNCTIONS ---
                openCreateModal() {
                    this.resetForm();
                    this.esEditando = false;
                    this.showModal = true;
                },

                openEditModal(product) {
                    this.form.id = product.id;
                    this.form.nombre = product.nombre;
                    this.form.precio = product.precio;
                    this.form.existencias = product.existencias;
                    this.esEditando = true;
                    this.showModal = true;
                },

                resetForm() {
                    this.form.id = null;
                    this.form.nombre = '';
                    this.form.precio = null;
                    this.form.existencias = null;
                    this.esEditando = false;
                },

                notify(message, success = true) {
                    this.notification = { message, success };
                    this.showNotification = true;
                    setTimeout(() => this.showNotification = false, 3000);
                }
            }));
        });
    </script>
@endsection
