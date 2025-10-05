@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Estadísticas Generales</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold">Beneficios Totales</h2>
            <p class="text-3xl font-bold mt-2">${{ number_format($beneficios ?? 0, 2) }}</p>
        </div>
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold">Ventas Totales</h2>
            <p class="text-3xl font-bold mt-2">${{ number_format($totalVentas ?? 0, 2) }}</p>
        </div>
        <div class="bg-red-500 text-white p-6 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold">Egresos Totales</h2>
            <p class="text-3xl font-bold mt-2">${{ number_format($totalEgresos ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">
        <div class="lg:col-span-3 bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold mb-2">Ventas de los Últimos 30 Días</h3>
            <canvas id="ventasChart" data-fechas="{{ $fechasVentas->toJson() }}" data-montos="{{ $montosVentas->toJson() }}"></canvas>
        </div>
        <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold mb-2">Top 5 Productos Más Vendidos</h3>
            <canvas id="productosChart" data-nombres="{{ $nombresProductos->toJson() }}" data-cantidades="{{ $cantidadesVendidas->toJson() }}"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold mb-2 text-yellow-600">Productos con Bajo Stock (10 o menos)</h3>
            <div class="overflow-y-auto h-64">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr><th class="py-2 px-3 text-left">Producto</th><th class="py-2 px-3 text-center">Stock</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($productosBajoStock as $producto)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-3">{{ $producto->nombre }}</td>
                                <td class="py-2 px-3 text-center font-bold text-red-600">{{ $producto->existencias }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-4 text-center text-gray-500">No hay productos con bajo stock.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold mb-2 text-blue-600">Apartados Vigentes</h3>
            <div class="overflow-y-auto h-64">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr><th class="py-2 px-3 text-left">Cliente</th><th class="py-2 px-3 text-left">Monto</th><th class="py-2 px-3 text-left">Vencimiento</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($apartadosVigentes as $apartado)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-3">{{ $apartado->cliente->nombre ?? 'N/A' }}</td>
                                <td class="py-2 px-3">${{ number_format($apartado->monto_total, 2) }}</td>
                                <td class="py-2 px-3">{{ \Carbon\Carbon::parse($apartado->fecha_vencimiento)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-500">No hay apartados vigentes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ventasCanvas = document.getElementById('ventasChart');
    if (ventasCanvas) {
        const fechas = JSON.parse(ventasCanvas.dataset.fechas);
        const montos = JSON.parse(ventasCanvas.dataset.montos);
        if (fechas && fechas.length > 0) {
            new Chart(ventasCanvas.getContext('2d'), { type: 'line', data: { labels: fechas, datasets: [{ label: 'Ventas ($)', data: montos, borderColor: 'rgba(59, 130, 246, 1)', backgroundColor: 'rgba(59, 130, 246, 0.2)', fill: true, tension: 0.1 }] } });
        } else {
            const ctx = ventasCanvas.getContext('2d'); ctx.textAlign = 'center'; ctx.font = "16px Arial"; ctx.fillText('No hay datos de ventas', ventasCanvas.width/2, ventasCanvas.height/2);
        }
    }
    const productosCanvas = document.getElementById('productosChart');
    if (productosCanvas) {
        const nombres = JSON.parse(productosCanvas.dataset.nombres);
        const cantidades = JSON.parse(productosCanvas.dataset.cantidades);
        if (nombres && nombres.length > 0) {
            new Chart(productosCanvas.getContext('2d'), { type: 'bar', data: { labels: nombres, datasets: [{ label: 'Cantidad Vendida', data: cantidades, backgroundColor: ['rgba(239, 68, 68, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(139, 92, 246, 0.7)'] }] }, options: { indexAxis: 'y' } });
        } else {
            const ctx = productosCanvas.getContext('2d'); ctx.textAlign = 'center'; ctx.font = "16px Arial"; ctx.fillText('No hay productos vendidos', productosCanvas.width/2, productosCanvas.height/2);
        }
    }
});
</script>
@endpush