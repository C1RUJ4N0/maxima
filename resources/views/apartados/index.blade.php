@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Gestión de Apartados</h1>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createApartadoModal">
        Crear Apartado
    </button>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha Apartado</th>
                        <th>Fecha Límite</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apartados as $apartado)
                    <tr>
                        <td>{{ $apartado->id }}</td>
                        <td>{{ $apartado->cliente->nombre ?? 'N/A' }}</td>
                        <td>{{ $apartado->fecha_apartado }}</td>
                        <td>{{ $apartado->fecha_limite }}</td>
                        <td>${{ number_format($apartado->total, 2) }}</td>
                        <td><span class="badge bg-info">{{ $apartado->estado }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay apartados registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createApartadoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Apartado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('apartados.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-control" required>
                            <option value="">Seleccione un cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha del Apartado</label>
                            <input type="date" name="fecha_apartado" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Límite</label>
                            <input type="date" name="fecha_limite" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number" step="0.01" name="total" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-control">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Pagado">Pagado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Apartado</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection