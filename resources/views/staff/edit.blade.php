@extends('layouts.documed')

@section('title', 'Editar Personal')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas {{ $staff->type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
            Editar {{ $staff->type_name }}: {{ $staff->name }}
        </h1>
        <a href="{{ route('staff.show', [$company, $staff]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ route('staff.update', [$company, $staff]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <!-- Documentos -->
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-file-alt"></i> 
                            Documentos {{ $staff->type_name }}
                            <span class="badge bg-info">{{ $staff->documents->count() }} subidos</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @include('staff._form_documents_edit', ['documents' => $documents, 'staff' => $staff])
                    </div>
                </div>
            </div>

            <!-- Información lateral -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Información</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> {{ $staff->name }}</p>
                        <p><strong>DNI:</strong> {{ $staff->dni }}</p>
                        <p><strong>Tipo:</strong> 
                            <span class="badge {{ $staff->type == 'professional' ? 'bg-primary' : 'bg-success' }}">
                                {{ $staff->type_name }}
                            </span>
                        </p>
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-info">{{ $staff->status }}</span>
                        </p>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('staff.show', [$company, $staff]) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection