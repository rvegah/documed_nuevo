{{-- resources/views/companies/index.blade.php --}}
@extends('layouts.documed')

@section('title', 'Listado de Empresas')

@section('content-documed')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-building text-primary"></i> Empresas</h1>
    <a href="{{ route('companies.wizard') }}" class="btn btn-success">
        <i class="fas fa-magic"></i> Crear Nueva Empresa
    </a>
</div>

{{-- Estadísticas Rápidas --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">En Tramitación</h6>
                        <h4 class="mb-0">{{ $companies->where('status', 'Tramitación')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-paper-plane fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Presentadas</h6>
                        <h4 class="mb-0">{{ $companies->where('status', 'Presentada')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Aprobadas</h6>
                        <h4 class="mb-0">{{ $companies->where('status', 'Aprobada')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #007bff !important;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-building fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">Total</h6>
                        <h4 class="mb-0">{{ $companies->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de Empresas --}}
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> Listado de Empresas
        </h5>
    </div>
    <div class="card-body">
        @if($companies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-building"></i> Empresa</th>
                            <th><i class="fas fa-user-tie"></i> Representante Legal</th>
                            <th><i class="fas fa-id-card"></i> Titular</th>
                            <th><i class="fas fa-info-circle"></i> Estado</th>
                            <th><i class="fas fa-users"></i> Personal</th>
                            <th><i class="fas fa-cog"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark">#{{ $company->id }}</span>
                                </td>
                                <td>
                                    <strong>{{ $company->company_name ?? 'N/A' }}</strong>
                                    @if($company->email)
                                        <br><small class="text-muted">{{ $company->email }}</small>
                                    @endif
                                </td>
                                <td>{{ $company->legal_representative_dni }}</td>
                                <td>{{ $company->rn_owner }}</td>
                                <td>
                                    @switch($company->status)
                                        @case('Tramitación')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Tramitación
                                            </span>
                                            @break
                                        @case('Presentada')
                                            <span class="badge bg-info">
                                                <i class="fas fa-paper-plane"></i> Presentada
                                            </span>
                                            @break
                                        @case('Aprobada')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Aprobada
                                            </span>
                                            @break
                                        @case('Resuelta')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-flag-checkered"></i> Resuelta
                                            </span>
                                            @break
                                        @case('Rechazada')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle"></i> Rechazada
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $company->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($company->has_center_staff)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Sí
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-minus"></i> No
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('companies.show', $company) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('companies.edit', $company) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $company->id }}"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modal de Confirmación de Eliminación --}}
                                    <div class="modal fade" id="deleteModal{{ $company->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-exclamation-triangle text-danger"></i>
                                                        Confirmar Eliminación
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de que deseas eliminar la empresa <strong>{{ $company->company_name ?? $company->rn_owner }}</strong>?</p>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Esta acción no se puede deshacer.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times"></i> Cancelar
                                                    </button>
                                                    <form action="{{ route('companies.destroy', $company) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay empresas registradas</h4>
                <p class="text-muted">Comienza creando tu primera empresa</p>
                <a href="{{ route('companies.wizard') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primera Empresa
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Filtros Rápidos (Opcional) --}}
<div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="fas fa-filter"></i> Filtros Rápidos
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="d-grid">
                    <a href="{{ route('companies.index') }}?filter=tramitacion" class="btn btn-outline-warning">
                        <i class="fas fa-clock"></i> En Tramitación ({{ $companies->where('status', 'Tramitación')->count() }})
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <a href="{{ route('companies.index') }}?filter=presentada" class="btn btn-outline-info">
                        <i class="fas fa-paper-plane"></i> Presentadas ({{ $companies->where('status', 'Presentada')->count() }})
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <a href="{{ route('companies.index') }}?filter=aprobada" class="btn btn-outline-success">
                        <i class="fas fa-check-circle"></i> Aprobadas ({{ $companies->where('status', 'Aprobada')->count() }})
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Confirmación de eliminación con SweetAlert si lo tienes instalado
    // O puedes usar el modal de Bootstrap como ya está implementado
</script>
@endpush