@extends('layouts.documed')

@section('title', 'Ver Empresa')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Información de la Empresa</h1>
        <div>
            <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información básica -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-building"></i> Datos de la Empresa</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> {{ $company->company_name ?: 'No especificado' }}</p>
                            <p><strong>DNI Representante Legal:</strong> {{ $company->legal_representative_dni }}</p>
                            <p><strong>RC del Titular:</strong> {{ $company->rn_owner }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-info">{{ $company->status }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $company->email ?: 'No especificado' }}</p>
                            <p><strong>Teléfono:</strong> {{ $company->phone ?: 'No especificado' }}</p>
                            <p><strong>Tipo:</strong> {{ ucfirst($company->company_type ?? 'clinic') }}</p>
                            <p><strong>Personal del Centro:</strong> 
                                <span class="badge {{ $company->has_center_staff ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $company->has_center_staff ? 'Sí' : 'No' }}
                                </span>
                                @if($company->has_center_staff)
                                    <small class="text-success d-block">
                                        <i class="fas fa-info-circle"></i> Puede registrar personal del centro
                                    </small>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($company->address)
                        <hr>
                        <p><strong>Dirección:</strong> 
                            {{ $company->address }}
                            @if($company->city), {{ $company->city }}@endif
                            @if($company->postal_code) ({{ $company->postal_code }})@endif
                            @if($company->province), {{ $company->province }}@endif
                        </p>
                    @endif
                    
                    @if($company->notes)
                        <hr>
                        <p><strong>Notas:</strong> {{ $company->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estado y fechas -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar"></i> Información de Registro</h5>
                </div>
                <div class="card-body">
                    <p><strong>Creado:</strong> {{ $company->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Actualizado:</strong> {{ $company->updated_at->format('d/m/Y H:i') }}</p>
                    @if($company->completed_at)
                        <p><strong>Completado:</strong> {{ $company->completed_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documentos -->
    @if($company->documents->count() > 0)
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Documentos Subidos ({{ $company->documents->count() }})
                </h5>
                <!-- BOTÓN NUEVO: Descargar Todo en ZIP -->
                <div>
                    <a href="{{ route('companies.documents.download-all', $company) }}" 
                    class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i>
                        <i class="fas fa-file-archive"></i>
                        Descargar Todo ZIP
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Información del ZIP -->
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Descarga Completa:</strong> El botón "Descargar Todo ZIP" incluye todos los documentos de la empresa y su personal organizados en carpetas.
                </div>
                
                <div class="row">
                    @foreach($company->documents as $document)
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $document->name }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <strong>Archivo:</strong> {{ $document->pivot->original_file_name }}<br>
                                            <strong>Subido:</strong> {{ $document->pivot->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </p>
                                    @if($document->pivot->path)
                                        <!-- BOTÓN MODIFICADO: Ahora usa la ruta controlada -->
                                        <a href="{{ route('companies.documents.download', [$company, $document]) }}" 
                                        class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Descargar
                                        </a>
                                        
                                        <!-- Botón adicional para ver en navegador -->
                                        <a href="{{ Storage::url($document->pivot->path) }}" 
                                        target="_blank" 
                                        class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($company->staff()->count() > 0)
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-users"></i>
                        <strong>Documentos del Personal:</strong> Esta empresa tiene {{ $company->staff()->count() }} personas registradas con documentos adicionales que también se incluirán en el ZIP.
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-body text-center">
                <p class="text-muted">No hay documentos subidos para esta empresa.</p>
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Subir Documentos
                </a>
            </div>
        </div>
    @endif
@endsection