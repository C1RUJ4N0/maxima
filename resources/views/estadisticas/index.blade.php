@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Estadísticas Generales</h1>
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header">Ventas de los Últimos 30 Días</div>
                <div class="card-body">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-header">Top 5 Productos Más Vendidos</div>
                <div class="card-body">
                    <canvas id="productosChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gráfico de Ventas
    const ctxVentas = document.getElementById('ventasChart').getContext('2d');
    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: @json($fechasVentas),
            datasets: [{
                label: 'Total de Ventas ($)',
                data: @json($montosVentas),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.1
            }]
        }
    });

    // Gráfico de Productos
    const ctxProductos = document.getElementById('productosChart').getContext('2d');
    new Chart(ctxProductos, {
        type: 'bar',
        data: {
            labels: @json($nombresProductos),
            datasets: [{
                label: 'Cantidad Vendida',
                data: @json($cantidadesVendidas),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ]
            }]
        },
        options: {
            indexAxis: 'y',
        }
    });
});
</script>
@endsection