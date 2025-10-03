@extends('layouts.app')

@section('title', 'Panel de Estadísticas')

@section('content')
<main class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="dashboard()">

    <!-- Tarjetas de Resumen -->
    <div class="col-span-1 lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <template x-if="cargando">
            <div class="col-span-4 text-center py-4 text-gray-500">Cargando datos del panel...</div>
        </template>

        <!-- Tarjeta 1: VENTAS DE HOY -->
        <div class="bg-white p-6 rounded-lg shadow-md border">
            <p class="font-semibold text-gray-600">VENTAS DE HOY</p>
            <p class="text-3xl font-bold mt-2"
               :class="{'text-indigo-600': !cargando, 'text-gray-400 animate-pulse': cargando}"
               x-text="cargando ? '...' : `$${datos.ventasHoy.toLocaleString('es-MX')}`"></p>
        </div>
        
        <!-- Tarjeta 2: VENTAS DEL MES -->
        <div class="bg-white p-6 rounded-lg shadow-md border">
            <p class="font-semibold text-gray-600">VENTAS DEL MES</p>
            <p class="text-3xl font-bold mt-2"
               :class="{'text-indigo-600': !cargando, 'text-gray-400 animate-pulse': cargando}"
               x-text="cargando ? '...' : `$${datos.ventasMes.toLocaleString('es-MX')}`"></p>
        </div>
        
        <!-- Tarjeta 3: EGRESOS DEL MES -->
        <div class="bg-white p-6 rounded-lg shadow-md border">
            <p class="font-semibold text-gray-600">EGRESOS DEL MES</p>
            <p class="text-3xl font-bold mt-2"
               :class="{'text-red-600': !cargando, 'text-gray-400 animate-pulse': cargando}"
               x-text="cargando ? '...' : `$${datos.egresosMes.toLocaleString('es-MX')}`"></p>
        </div>
        
        <!-- Tarjeta 4: PRODUCTOS CON BAJO STOCK -->
        <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center justify-center border">
            <p class="font-semibold text-gray-600">PRODUCTOS CON BAJO STOCK</p>
            <template x-if="cargando">
                <i class="fas fa-spinner fa-spin text-gray-400 text-4xl mt-2"></i>
            </template>
            <template x-if="!cargando">
                <div class="flex items-center space-x-2 mt-2">
                    <i class="fas"
                       :class="{
                           'fa-exclamation-triangle text-yellow-500': datos.productosBajoStock > 0,
                           'fa-check-circle text-green-500': datos.productosBajoStock === 0
                       }"
                       class="text-4xl"></i>
                    <span class="text-3xl font-bold" x-text="datos.productosBajoStock"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Gráfico de Ventas -->
    <div class="bg-white p-6 rounded-lg shadow-md border">
        <h4 class="text-lg font-semibold mb-2">Ventas de los Últimos 7 Días</h4>
        <canvas id="salesChart" class="w-full h-64"></canvas>
    </div>

    <!-- Apartados por Vencer -->
    <div class="bg-white p-6 rounded-lg shadow-md border overflow-x-auto">
        <h4 class="text-lg font-semibold mb-2">Apartados por Vencer</h4>
        <template x-if="cargandoApartados">
            <p class="text-center py-4 text-gray-500">Cargando apartados...</p>
        </template>
        <template x-if="!cargandoApartados && apartados.length === 0">
            <p class="text-center py-4 text-gray-500">No hay apartados próximos a vencer.</p>
        </template>
        <template x-if="!cargandoApartados && apartados.length > 0">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left font-semibold">Cliente</th>
                        <th class="py-2 px-4 text-left font-semibold">Número de Teléfono</th>
                        <th class="py-2 px-4 text-right font-semibold">Monto Total</th>
                        <th class="py-2 px-4 text-left font-semibold">Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="apartado in apartados" :key="apartado.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4" x-text="apartado.cliente_nombre"></td>
                            <td class="py-2 px-4" x-text="apartado.telefono"></td>
                            <td class="py-2 px-4 text-right" x-text="`$${apartado.monto_total.toLocaleString('es-MX')}`"></td>
                            <td class="py-2 px-4" x-text="apartado.fecha_vencimiento"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </template>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
            datos: {
                ventasHoy: 0,
                ventasMes: 0,
                egresosMes: 0,
                productosBajoStock: 0,
                ventasSieteDias: [], // Datos para el gráfico
                diasSieteDias: [] // Etiquetas para el gráfico
            },
            apartados: [],
            cargando: true,
            cargandoApartados: true,

            init() {
                this.fetchEstadisticas();
                this.fetchApartadosVencer();
            },

            async fetchEstadisticas() {
                this.cargando = true;
                try {
                    // Endpoint: /api/estadisticas -> debe ser manejado por EstadisticasController::index
                    const response = await fetch('/api/estadisticas');
                    
                    if (!response.ok) {
                        throw new Error('Error al obtener estadísticas.');
                    }

                    const result = await response.json();
                    
                    this.datos.ventasHoy = result.ventasHoy || 0;
                    this.datos.ventasMes = result.ventasMes || 0;
                    this.datos.egresosMes = result.egresosMes || 0;
                    this.datos.productosBajoStock = result.productosBajoStock || 0;
                    this.datos.ventasSieteDias = result.ventasSieteDias?.data || [];
                    this.datos.diasSieteDias = result.ventasSieteDias?.labels || [];

                    // Renderizar el gráfico con los datos obtenidos
                    this.renderSalesChart();

                } catch (error) {
                    console.error("Error cargando estadísticas:", error);
                    // Aquí podrías agregar una notificación de error si lo deseas
                } finally {
                    this.cargando = false;
                }
            },
            
            async fetchApartadosVencer() {
                this.cargandoApartados = true;
                try {
                    // Endpoint: /api/apartados-vencer -> debe ser manejado por VentaController::apartadosPorVencer
                    const response = await fetch('/api/apartados-vencer');
                    
                    if (!response.ok) {
                        throw new Error('Error al obtener apartados por vencer.');
                    }

                    const result = await response.json();
                    
                    // Asume que la API devuelve un array de apartados con las claves correctas
                    this.apartados = result.apartados.map(a => ({
                        id: a.id,
                        cliente_nombre: a.cliente_nombre, // Debe venir del join con clientes
                        telefono: a.telefono,
                        monto_total: a.monto_total,
                        fecha_vencimiento: a.fecha_vencimiento,
                    }));

                } catch (error) {
                    console.error("Error cargando apartados por vencer:", error);
                } finally {
                    this.cargandoApartados = false;
                }
            },


            renderSalesChart() {
                const ctx = document.getElementById('salesChart');
                // Destruir el gráfico anterior si existe
                if (window.salesChartInstance) {
                    window.salesChartInstance.destroy();
                }

                window.salesChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.datos.diasSieteDias,
                        datasets: [{
                            label: 'Ventas Diarias',
                            data: this.datos.ventasSieteDias,
                            backgroundColor: 'rgba(79, 70, 229, 0.8)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Monto de Venta ($)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
@endsection
