@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Gestión de Proveedores y Facturas</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <h2 class="text-xl font-semibold">Listado de Proveedores</h2>
        </div>
        @forelse ($proveedores as $proveedor)
            <div class="border-t">
                <div class="p-4 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg">{{ $proveedor->nombre }}</h3>
                        <p class="text-sm text-gray-600">{{ $proveedor->telefono }} - {{ $proveedor->email }}</p>
                    </div>
                    
                    {{-- // --- INICIO CAMBIO ADMIN --- // --}}
                    @if(Auth::user()->role === 'admin')
                    <div class="flex space-x-2 items-center">
                        <button type="button" class="px-3 py-1 text-xs font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors" 
                                data-bs-toggle="modal" data-bs-target="#editProveedorModal{{ $proveedor->id }}">
                            <i class="fas fa-edit mr-1"></i> Modificar
                        </button>
                        
                        <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar el proveedor {{ $proveedor->nombre }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-trash mr-1"></i> Eliminar
                            </button>
                        </form>
                    </div>
                    @endif
                    {{-- // --- FIN CAMBIO ADMIN --- // --}}
                </div>
                
                {{-- // --- INICIO CAMBIO ADMIN --- // --}}
                @if(Auth::user()->role === 'admin')
                    @include('proveedores.edit', ['proveedor' => $proveedor])
                @endif
                {{-- // --- FIN CAMBIO ADMIN --- // --}}

                <div class="p-4">
                    <h4 class="font-semibold mb-2 text-gray-700">Facturas Asociadas</h4>
                    @if($proveedor->facturas->isEmpty())
                        <p class="text-sm text-gray-500 italic">Este proveedor no tiene facturas registradas.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-3 text-left">N° Factura</th>
                                        <th class="py-2 px-3 text-left">Monto</th>
                                        <th class="py-2 px-3 text-left">Fecha Emisión</th>
                                        <th class="py-2 px-3 text-left">Estado</th>
                                        <th class="py-2 px-3 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proveedor->facturas as $factura)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-2 px-3">{{ $factura->numero_factura }}</td>
                                            <td class="py-2 px-3">${{ number_format($factura->monto, 2) }}</td>
                                            <td class="py-2 px-3">{{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') }}</td>
                                            <td class="py-2 px-3">
                                                @if ($factura->estado == 'pagada')
                                                    <span class="px-2 py-1 font-semibold text-xs leading-tight text-green-700 bg-green-100 rounded-full">Pagada</span>
                                                @else
                                                    <span class="px-2 py-1 font-semibold text-xs leading-tight text-yellow-700 bg-yellow-100 rounded-full">Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 text-center">
                                                {{-- // --- INICIO CAMBIO ADMIN --- // --}}
                                                @if(Auth::user()->role === 'admin')
                                                    @if ($factura->estado == 'pendiente')
                                                        <form action="{{ route('facturas.update', $factura) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="estado" value="pagada">
                                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs transition duration-300">
                                                                Pagar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span>-</span>
                                                    @endif
                                                @else
                                                    <span>-</span>
                                                @endif
                                                {{-- // --- FIN CAMBIO ADMIN --- // --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="p-4 text-center text-gray-500">No hay proveedores registrados.</p>
        @endforelse
    </div>
</div>
@endsection