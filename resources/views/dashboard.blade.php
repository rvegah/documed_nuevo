@extends('layouts.documed')

@section('title', 'Dashboard')

@section('content-documed')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-tachometer-alt"></i> Dashboard - Bienvenido {{ auth()->user()->name }}
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    ¡Has iniciado sesión exitosamente en DocuMed!
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-building fa-2x mb-2"></i>
                                <h4>{{ \App\Models\Company::count() }}</h4>
                                <p>Empresas Registradas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h4>{{ \App\Models\Staff::count() }}</h4>
                                <p>Personal Registrado</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                <h4>{{ \App\Models\Company::with('documents')->get()->sum('documents.count') + \App\Models\Staff::with('documents')->get()->sum('documents.count') }}</h4>
                                <p>Documentos Totales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                @php
                                    $pendingCompanyDocs = \App\Models\Company::with(['documents' => function($q) { 
                                        $q->where('valid', false)->orWhereNull('valid'); 
                                    }])->get()->sum(function($company) { 
                                        return $company->documents->count(); 
                                    });
                                    
                                    $pendingStaffDocs = \App\Models\Staff::with(['documents' => function($q) { 
                                        $q->where('valid', false)->orWhereNull('valid'); 
                                    }])->get()->sum(function($staff) { 
                                        return $staff->documents->count(); 
                                    });
                                    
                                    $totalPending = $pendingCompanyDocs + $pendingStaffDocs;
                                @endphp
                                <h4>{{ $totalPending }}</h4>
                                <p>Docs. Pendientes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-rocket"></i> Acciones Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('companies.index') }}" class="btn btn-primary">
                                        <i class="fas fa-building"></i> Ver Empresas
                                    </a>
                                    <!--<a href="{{ route('companies.wizard') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Nueva Empresa
                                    </a>-->
                                    <a href="{{ route('companies.wizard') }}" class="btn btn-info">
                                        <i class="fas fa-magic"></i> Nueva de Empresa
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.document-approval.index') }}" class="btn btn-warning">
                                            <i class="fas fa-check-circle"></i> Panel de Aprobación
                                            @if($totalPending > 0)
                                                <span class="badge bg-danger">{{ $totalPending }}</span>
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-user"></i> Tu Perfil</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Nombre:</strong> {{ auth()->user()->name }}</p>
                                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                <p><strong>Rol:</strong> 
                                    <span class="badge {{ auth()->user()->isAdmin() ? 'bg-danger' : 'bg-info' }}">
                                        {{ auth()->user()->isAdmin() ? 'Administrador' : 'Usuario' }}
                                    </span>
                                </p>
                                <p><strong>Miembro desde:</strong> {{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i> Editar Perfil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <!-- Panel administrativo -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Panel Administrativo</h5>
                                </div>
                                <div class="card-body">
                                    <p>Como administrador, tienes acceso completo al sistema:</p>
                                    <ul>
                                        <li>Gestión de todas las empresas</li>
                                        <li>Aprobación/rechazo de documentos</li>
                                        <li>Supervisión del personal</li>
                                        <li>Reportes y estadísticas</li>
                                    </ul>
                                    @if($totalPending > 0)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Atención:</strong> Hay {{ $totalPending }} documentos esperando tu revisión.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection