@extends('layouts.documed')

@section('title', 'Registrar Personal')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas {{ $type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
            Registrar {{ $type == 'professional' ? 'Personal Profesional' : 'Personal Clínico' }}
        </h1>
        <a href="{{ route('staff.index', $company) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Personal
        </a>
    </div>

    <form action="{{ route('staff.store', $company) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        
        <div class="row">
            <!-- Información básica del personal -->
            <div class="col-md-8">
                {{-- DATOS BÁSICOS COMENTADOS TEMPORALMENTE
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="fas {{ $type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
                            Datos del {{ $type == 'professional' ? 'Profesional' : 'Personal Clínico' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name">Nombre Completo *</label>
                                <input type="text" name="name" id="name" 
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dni">DNI *</label>
                                <input type="text" name="dni" id="dni" 
                                       class="form-control @error('dni') is-invalid @enderror"
                                       value="{{ old('dni') }}" required>
                                @error('dni')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" 
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone">Teléfono</label>
                                <input type="text" name="phone" id="phone" 
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                --}}

                <!-- Documentos requeridos -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-file-alt"></i> 
                            Documentos {{ $type == 'professional' ? 'Profesionales' : 'Personal Clínico' }}
                            <span class="badge bg-info">{{ $documents->count() }} documentos</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i> 
                            Se encontraron {{ $documents->count() }} documentos para 
                            {{ $type == 'professional' ? 'personal profesional' : 'personal clínico' }}.
                            Puedes continuar sin subir todos los archivos y completarlos más tarde.
                        </div>

                        <div class="row">
                            @foreach($documents as $document)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $document->name }}</h6>
                                            <p class="card-text text-muted small">
                                                @if($document->required)
                                                    <span class="badge bg-danger">Requerido</span>
                                                @else
                                                    <span class="badge bg-secondary">Opcional</span>
                                                @endif
                                            </p>
                                            
                                            <div class="mb-2">
                                                <input type="file" 
                                                       class="form-control form-control-sm"
                                                       name="documents[{{ $document->id }}]"
                                                       accept=".pdf,.jpg,.jpeg,.png"
                                                       id="file_{{ $document->id }}"
                                                       onchange="showFileName({{ $document->id }})">
                                            </div>
                                            
                                            <!-- Mostrar archivo seleccionado -->
                                            <div id="file-selected-{{ $document->id }}" style="display: none;" class="alert alert-success py-2 mt-2">
                                                <i class="fas fa-check-circle"></i>
                                                <small><strong>Archivo seleccionado:</strong> <span id="filename-{{ $document->id }}"></span></small>
                                            </div>
                                            
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> Formatos: PDF, JPG, JPEG, PNG (máx. 4MB)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                        <p><strong>Empresa:</strong> {{ $company->company_name }}</p>
                        <p><strong>Tipo de Personal:</strong> 
                            <span class="badge {{ $type == 'professional' ? 'bg-primary' : 'bg-success' }}">
                                {{ $type == 'professional' ? 'Profesional' : 'Personal Clínico' }}
                            </span>
                        </p>
                        <p><strong>Documentos Disponibles:</strong> {{ $documents->count() }}</p>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn {{ $type == 'professional' ? 'btn-primary' : 'btn-success' }}">
                                <i class="fas fa-save"></i> Registrar Personal
                            </button>
                            <a href="{{ route('staff.index', $company) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Consejos</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Puedes subir documentos ahora o más tarde</li>
                            <li>Los documentos marcados como "Requerido" son obligatorios</li>
                            <li>Formatos soportados: PDF, JPG, PNG</li>
                            <li>Tamaño máximo: 4MB por archivo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function showFileName(documentId) {
            const input = document.getElementById('file_' + documentId);
            const fileDiv = document.getElementById('file-selected-' + documentId);
            const filenameSpan = document.getElementById('filename-' + documentId);
            
            if (input.files.length > 0) {
                const fileName = input.files[0].name;
                filenameSpan.textContent = fileName;
                fileDiv.style.display = 'block';
            } else {
                fileDiv.style.display = 'none';
            }
        }
    </script>
@endsection