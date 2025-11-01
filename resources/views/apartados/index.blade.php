@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Apartados</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel.index') }}">Panel</a></li>
        <li class="breadcrumb-item active">Apartados</li>
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
            <i class="fas fa-box-open me-1"></i>
            Listado de Apartados
        </div>
        <div class="card-body">

            {{-- // --- INICIO CAMBIO ADMIN --- // --}}
            @if(Auth::user()->role === 'admin')
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevoApartado">
                <i class="fas fa-plus me-1"></i> Nuevo Apartado
            </button>
            @endif
            {{-- // --- FIN CAMBIO ADMIN --- // --}}

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total Apartado</th>
                            <th>Total Abonado</th>
                            <th>Saldo Pendiente</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            {{-- // --- INICIO CAMBIO ADMIN --- // --}}
                            @if(Auth::user()->role === 'admin')
                            <th>Acciones</th>
                            @endif
                            {{-- // --- FIN CAMBIO ADMIN --- // --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($apartados as $apartado)
                        <tr data-id="{{ $apartado->id }}"
                            data-cliente-id="{{ $apartado->cliente_id }}"
                            data-total-abonado="{{ $apartado->total_abonado }}"
                            data-estado="{{ $apartado->estado }}">
                            
                            <td>{{ $apartado->id }}</td>
                            <td>
                                @if($apartado->cliente)
                                    {{ $apartado->cliente->nombre }}
                                @else
                                    Cliente General
                                @endif
                            </td>
                            <td>${{ number_format($apartado->total, 2) }}</td>
                            <td>${{ number_format($apartado->total_abonado, 2) }}</td>
                            <td>
                                @php
                                    $saldoPendiente = $apartado->total - $apartado->total_abonado;
                                @endphp
                                ${{ number_format($saldoPendiente, 2) }}
                            </td>
                            <td>{{ $apartado->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($apartado->estado == 'entregado')
                                    <span class="badge bg-success">Entregado</span>
                                @elseif($apartado->estado == 'cancelado')
                                    <span class="badge bg-danger">Cancelado</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @endif
                            </td>
                            
                            {{-- // --- INICIO CAMBIO ADMIN --- // --}}
                            @if(Auth::user()->role === 'admin')
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-editar-apartado" title="Editar Apartado"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarApartado"
                                        data-url-update="{{ route('apartados.update', $apartado) }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                @if($apartado->estado != 'entregado' && $apartado->estado != 'cancelado')
                                <form action="{{ route('apartados.destroy', $apartado) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas cancelar este apartado? La acción no se puede revertir.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancelar Apartado">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
                            {{-- // --- FIN CAMBIO ADMIN --- // --}}
                        </tr>
                        @empty
                        <tr>
                            <td colspan="@if(Auth::user()->role === 'admin') 8 @else 7 @endif" class="text-center text-muted">No hay apartados registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                 {{ $apartados->links() }}
            </div>
        </div>
    </div>
</div>

{{-- 
    MODALES
    (Envolvemos TODOS los modales en el @if de admin)
--}}
@if(Auth::user()->role === 'admin')

<div class="modal fade" id="modalNuevoApartado" tabindex="-1" aria-labelledby="modalNuevoApartadoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('apartados.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevoApartadoLabel">Registrar Nuevo Apartado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id">
                            <option value="">Cliente General</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="total" class="form-label">Total del Apartado</label>
                        <input type="number" step="0.01" class="form-control" id="total" name="total" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_abonado" class="form-label">Monto Abonado (Seña)</label>
                        <input type="number" step="0.01" class="form-control" id="total_abonado" name="total_abonado" required>
                    </div>
                    <p class="text-muted">Nota: Los productos del apartado se gestionan desde el TPV.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Apartado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarApartado" tabindex="-1" aria-labelledby="modalEditarApartadoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditarApartado" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarApartadoLabel">Editar Apartado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="edit_cliente_id" name="cliente_id">
                            <option value="">Cliente General</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_total_abonado" class="form-label">Total Abonado</label>
                        <input type="number" step="0.01" class="form-control" id="edit_total_abonado" name="total_abonado" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_estado" class="form-label">Estado</label>
                        <select class="form-select" id="edit_estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="entregado">Entregado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Apartado</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif
{{-- // --- FIN WRAPPER ADMIN MODALES --- // --}}

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // --- Lógica para modal Editar Apartado (Web) ---
    var modalEditarApartado = document.getElementById('modalEditarApartado');
    if(modalEditarApartado) {
        modalEditarApartado.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var row = button.closest('tr');
            
            var clienteId = row.dataset.clienteId;
            var totalAbonado = row.dataset.totalAbonado;
            var estado = row.dataset.estado;
            var urlUpdate = button.dataset.urlUpdate;

            var modal = this;
            modal.querySelector('#edit_cliente_id').value = clienteId;
            modal.querySelector('#edit_total_abonado').value = totalAbonado;
            modal.querySelector('#edit_estado').value = estado;
            modal.querySelector('#formEditarApartado').action = urlUpdate;
        });
    }
});
</script>
@endpush