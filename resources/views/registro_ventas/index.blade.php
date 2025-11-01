@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Registro de Ventas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel.index') }}">Panel</a></li>
        <li class="breadcrumb-item active">Registro de Ventas</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-receipt me-1"></i>
            Ventas Realizadas
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Método de Pago</th>
                            <th>Fecha y Hora</th>
                            <th>Productos</th>
                            {{-- // --- INICIO CAMBIO ADMIN --- // --}}
                            @if(Auth::user()->role === 'admin')
                            <th>Acciones</th>
                            @endif
                            {{-- // --- FIN CAMBIO ADMIN --- // --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                        <tr>
                            <td>{{ $venta->id }}</td>
                            <td>{{ $venta->cliente->nombre ?? 'Cliente General' }}</td>
                            <td>${{ number_format($venta->total, 2) }}</td>
                            <td>{{ ucfirst($venta->metodo_pago) }}</td>
                            <td>{{ $venta->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach($venta->items as $item)
                                        <li>
                                            {{ $item->cantidad }} x {{ $item->producto->nombre }} 
                                            (${{ number_format($item->precio_unitario, 2) }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            
                            {{-- // --- INICIO CAMBIO ADMIN --- // --}}
                            @if(Auth::user()->role === 'admin')
                            <td>
                                {{-- Tu ruta de eliminar venta es API (apiDestroy), no WEB --}}
                                <form action="{{-- route('ventas.destroy', $venta) --}}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta venta? Esta acción ajustará el stock de vuelta.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Venta" disabled>
                                        <i class="fas fa-trash"></i> (API)
                                    </button>
                                </form>
                                <small class="text-muted d-block">Gestión vía API TPV</small>
                            </td>
                            @endif
                            {{-- // --- FIN CAMBIO ADMIN --- // --}}
                        </tr>
                        @empty
                        <tr>
                            {{-- Ajustamos el colspan dinámicamente --}}
                            <td colspan="@if(Auth::user()->role === 'admin') 7 @else 6 @endif" class="text-center text-muted">No hay ventas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                 {{ $ventas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection