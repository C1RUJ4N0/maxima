@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Registro de Ventas Diarias</h1>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase">Fecha</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase">Efectivo</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase">Transferencia</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase">Tarjeta</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase">Apartado</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase">Total del Día</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventasPorDia as $venta)
                <tr class="hover:bg-gray-100">
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">${{ number_format($venta->efectivo, 2) }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">${{ number_format($venta->transferencia, 2) }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">${{ number_format($venta->tarjeta, 2) }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm">${{ number_format($venta->apartado, 2) }}</td>
                    <td class="px-5 py-4 border-b border-gray-200 text-sm font-bold text-gray-900">${{ number_format($venta->total_dia, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">No hay ventas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $ventasPorDia->links() }}
    </div>
</div>
@endsection