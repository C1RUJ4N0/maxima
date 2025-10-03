@extends('layouts.app')

@section('title', 'Gestión de Apartados')

@section('content')
    <main class="bg-white p-6 rounded-lg shadow-xl border" x-data="apartados()">
        <div class="flex items-center space-x-4 mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Listado de Apartados</h2>
            <select x-model="filtroEstado" @change="fetchApartados()" class="p-2 border rounded-lg">
                <option value="todos">Todos</option>
                <template x-for="estado in ['vigente', 'vencido', 'pagado']" :key="estado">
                    <option :value="estado" x-text="estado.charAt(0).toUpperCase() + estado.slice(1)"></option>
                </template>
            </select>
        </div>
        <div class="overflow-x-auto">
            <template x-if="cargando">
                <p class="text-center py-8 text-gray-500">Cargando apartados...</p>
            </template>
            <template x-if="!cargando && apartados.length === 0">
                <p class="text-center py-8 text-gray-500">No hay apartados registrados o vigentes.</p>
            </template>
            <template x-if="!cargando && apartados.length > 0">
                <table class="min-w-full bg-white rounded-lg shadow border">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Cliente</th>
                            <th class="py-3 px-6 text-left">Número de Teléfono</th>
                            <th class="py-3 px-6 text-right">Abonado</th>
                            <th class="py-3 px-6 text-right">Faltante</th>
                            <th class="py-3 px-6 text-left">Vencimiento</th>
                            <th class="py-3 px-6 text-center">Estado</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <template x-for="apartado in apartados" :key="apartado.id">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-6 text-left font-medium" x-text="apartado.cliente_nombre"></td>
                                <td class="py-3 px-6 text-left" x-text="apartado.telefono"></td>
                                <td class="py-3 px-6 text-right" x-text="`$${apartado.monto_abonado ? apartado.monto_abonado.toLocaleString('es-MX') : '0.00'}`"></td>
                                <td class="py-3 px-6 text-right" x-text="`$${(apartado.monto_total - (apartado.monto_abonado || 0)).toLocaleString('es-MX')}`"></td>
                                <td class="py-3 px-6 text-left" x-text="apartado.fecha_vencimiento"></td>
                                <td class="py-3 px-6 text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full"
                                        :class="{
                                            'bg-green-200 text-green-800': apartado.estado === 'vigente',
                                            'bg-red-200 text-red-800': apartado.estado === 'vencido',
                                            'bg-blue-200 text-blue-800': apartado.estado === 'pagado',
                                        }"
                                        x-text="apartado.estado.charAt(0).toUpperCase() + apartado.estado.slice(1)">
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center space-x-2">
                                    <button @click="printApartadoTicket(apartado)" class="bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full hover:bg-gray-300 transition-colors">Imprimir Ticket</button>
                                    <button @click="notifyClient(apartado)" class="bg-yellow-200 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full hover:bg-yellow-300 transition-colors">Notificar</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </div>
    </main>

    <div x-cloak x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 transform transition-all" :class="notification.success ? 'bg-green-500' : 'bg-red-500'">
        <span x-text="notification.message"></span>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('apartados', () => ({
                apartados: [],
                cargando: true,
                filtroEstado: 'vigente',
                showNotification: false,
                notification: { message: '', success: true },

                init() {
                    this.fetchApartados();
                },

                async fetchApartados() {
                    this.cargando = true;
                    try {
                        // El controlador VentaController tiene la función getApartados
                        const url = `/api/apartados?estado=${this.filtroEstado === 'todos' ? '' : this.filtroEstado}`;
                        const response = await fetch(url);
                        
                        if (!response.ok) {
                            throw new Error('Error al obtener los apartados.');
                        }

                        const result = await response.json();
                        
                        // Mapeo de datos: La respuesta del controlador ya viene casi lista
                        // El controlador devuelve: nombre_cliente, telefono, monto_total, fecha_vencimiento, estado
                        // Agregaremos monto_abonado manualmente (o debe venir de la DB)
                        this.apartados = result.apartados.map(a => ({
                            id: a.id, // Asumiendo que el ID viene en la respuesta
                            cliente_nombre: a.nombre_cliente,
                            telefono: a.telefono,
                            monto_total: a.monto_total,
                            monto_abonado: a.monto_abonado || 0.00, // Asumimos un campo monto_abonado, sino usa el abonado por defecto 0
                            fecha_vencimiento: a.fecha_vencimiento,
                            estado: a.estado
                        }));
                        
                    } catch (error) {
                        this.notify('No se pudieron cargar los apartados: ' + error.message, false);
                        console.error(error);
                    } finally {
                        this.cargando = false;
                    }
                },

                printApartadoTicket(apartado) {
                    this.notify(`Imprimiendo ticket del apartado de ${apartado.cliente_nombre}...`, true);
                    // Aquí iría la lógica real de impresión
                },

                notifyClient(apartado) {
                    this.notify(`Notificando al cliente ${apartado.cliente_nombre}...`, true);
                    // Aquí iría la lógica real de notificación
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
