@extends('layouts.documed')

@section('title', 'Ver Personal')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas {{ $staff->type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
            {{ $staff->name }}
        </h1>
        <div>
            <a href="{{ route('staff.edit', [$company, $staff]) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('staff.index', $company) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Personal
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del personal -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas {{ $staff->type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
                        Información del {{ $staff->type_name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> {{ $staff->name }}</p>
                            <p><strong>DNI:</strong> {{ $staff->dni }}</p>
                            <p><strong>Tipo:</strong> 
                                <span class="badge {{ $staff->type == 'professional' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $staff->type_name }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $staff->email ?: 'No especificado' }}</p>
                            <p><strong>Teléfono:</strong> {{ $staff->phone ?: 'No especificado' }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-info">{{ $staff->status }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos del personal -->
            @if($staff->documents->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-file-alt"></i> Documentos Subidos ({{ $staff->documents->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($staff->documents as $document)
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
                                                <a href="{{ Storage::url($document->pivot->path) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Descargar
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <p class="text-muted">No hay documentos subidos para este personal.</p>
                        <a href="{{ route('staff.edit', [$company, $staff]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Subir Documentos
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Información lateral -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Información</h5>
                </div>
                <div class="card-body">
                    <p><strong>Empresa:</strong> {{ $company->company_name }}</p>
                    <p><strong>Creado:</strong> {{ $staff->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Actualizado:</strong> {{ $staff->updated_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Documentos:</strong> {{ $staff->documents->count() }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Acciones</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('staff.edit', [$company, $staff]) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Personal
                        </a>
                        <form action="{{ route('staff.destroy', [$company, $staff]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('¿Estás seguro de eliminar este personal?')">
                                <i class="fas fa-trash"></i> Eliminar Personal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection