@extends('layouts.documed')

@section('title', 'Panel de Aprobación de Documentos')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-check-circle text-success"></i> Panel de Aprobación de Documentos
        </h1>
        <div>
            <span class="badge bg-info">Admin: {{ auth()->user()->name }}</span>
        </div>
    </div>

    <div class="row">
        <!-- Documentos de Empresas -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building"></i> Documentos de Empresas
                    </h5>
                </div>
                <div class="card-body">
                    @if($companies->where('documents.count', '>', 0)->count() > 0)
                        @foreach($companies as $company)
                            @if($company->documents->count() > 0)
                                <div class="border-bottom pb-3 mb-3">
                                    <h6>{{ $company->company_name }}</h6>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-file"></i> 
                                        {{ $company->documents->count() }} documentos pendientes
                                    </p>
                                    <a href="{{ route('admin.document-approval.company', $company) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Revisar Documentos
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="text-muted">No hay documentos de empresas pendientes de aprobación</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Documentos de Personal -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Documentos de Personal
                    </h5>
                </div>
                <div class="card-body">
                    @if($staffWithPendingDocs->where('documents.count', '>', 0)->count() > 0)
                        @foreach($staffWithPendingDocs as $staff)
                            @if($staff->documents->count() > 0)
                                <div class="border-bottom pb-3 mb-3">
                                    <h6>
                                        <i class="fas {{ $staff->type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
                                        {{ $staff->name }}
                                    </h6>
                                    <p class="text-muted small mb-1">
                                        <strong>Empresa:</strong> {{ $staff->company->company_name }}
                                    </p>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-file"></i> 
                                        {{ $staff->documents->count() }} documentos pendientes
                                    </p>
                                    <a href="{{ route('admin.document-approval.staff', $staff) }}" 
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-eye"></i> Revisar Documentos
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="text-muted">No hay documentos de personal pendientes de aprobación</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar"></i> Estadísticas de Aprobación</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4>{{ $companies->sum(function($company) { return $company->documents->count(); }) }}</h4>
                                    <p>Docs. Empresas Pendientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <i class="fas fa-user-clock fa-2x mb-2"></i>
                                    <h4>{{ $staffWithPendingDocs->sum(function($staff) { return $staff->documents->count(); }) }}</h4>
                                    <p>Docs. Personal Pendientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <i class="fas fa-building fa-2x mb-2"></i>
                                    <h4>{{ $companies->count() }}</h4>
                                    <p>Empresas Registradas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h4>{{ $staffWithPendingDocs->count() }}</h4>
                                    <p>Personal Registrado</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection