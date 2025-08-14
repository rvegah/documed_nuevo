@extends('layouts.documed')

@section('title', 'Editar Empresa')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Empresa: {{ $company->company_name }}</h1>
        <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data" id="company-form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Información básica -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-building"></i> Información de la Empresa</h5>
                    </div>
                    <div class="card-body">
                        <!-- Campos básicos (no editables) -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="company_name">Nombre de la Empresa *</label>
                                <input type="text" name="company_name" id="company_name" 
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name', $company->company_name) }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="legal_representative_dni">DNI Representante Legal *</label>
                                <input type="text" name="legal_representative_dni" id="legal_representative_dni" 
                                       class="form-control @error('legal_representative_dni') is-invalid @enderror"
                                       value="{{ old('legal_representative_dni', $company->legal_representative_dni) }}" required>
                                @error('legal_representative_dni')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rn_owner">RC del Titular *</label>
                                <input type="text" name="rn_owner" id="rn_owner" 
                                       class="form-control @error('rn_owner') is-invalid @enderror"
                                       value="{{ old('rn_owner', $company->rn_owner) }}" required>
                                @error('rn_owner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- SECCIÓN PARA PERSONAL DEL CENTRO -->
                        @if($company->documents->count() >= 19)
                            <hr>
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle"></i> Documentación Básica Completada</h6>
                                <p class="mb-0">Has subido {{ $company->documents->count() }} documentos. Ahora puedes proceder con el personal del centro.</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="has_center_staff" id="has_center_staff" 
                                               class="form-check-input" value="1"
                                               {{ old('has_center_staff', $company->has_center_staff) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_center_staff">
                                            <strong>¿Tiene personal del centro?</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Marque esta opción si la empresa tiene personal profesional o clínico que requiere documentación adicional.
                                    </small>
                                </div>
                            </div>
                        @else
                            <hr>
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Documentación Pendiente</h6>
                                <p class="mb-0">Complete la subida de documentos básicos para poder registrar personal del centro.</p>
                                <p class="mb-0"><strong>Documentos subidos:</strong> {{ $company->documents->count() }} / 19 documentos básicos</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 🚀 NUEVA SECCIÓN: CONTINUAR SUBIENDO DOCUMENTOS -->
                @if($company->documents->count() < 19)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5><i class="fas fa-file-upload"></i> Continuar Subiendo Documentos 
                                <span class="badge bg-info">{{ $company->documents->count() }}/19</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @include('companies._form_documents_edit', ['documents' => $documents, 'company' => $company])
                        </div>
                    </div>
                @endif
            </div>

            <!-- Estado y acciones -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Estado Actual</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-info">{{ $company->status }}</span>
                        </p>
                        <p><strong>Creado:</strong> {{ $company->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Documentos:</strong> {{ $company->documents->count() }}/19</p>
                        
                        {{-- 🚀 NUEVO: Progreso de documentos obligatorios --}}
                        @php
                            $requiredDocs = $documents->where('category', 'basic')->where('required', true);
                            $uploadedRequiredDocs = $company->documents->whereIn('id', $requiredDocs->pluck('id'));
                            $missingRequiredDocs = $requiredDocs->whereNotIn('id', $uploadedRequiredDocs->pluck('id'));
                            $requiredProgress = $requiredDocs->count() > 0 ? ($uploadedRequiredDocs->count() / $requiredDocs->count()) * 100 : 100;
                        @endphp

                        @if($requiredDocs->count() > 0)
                            <p><strong>Obligatorios:</strong> {{ $uploadedRequiredDocs->count() }}/{{ $requiredDocs->count() }}</p>
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar {{ $requiredProgress == 100 ? 'bg-success' : 'bg-danger' }}" 
                                     style="width: {{ $requiredProgress }}%">
                                    {{ round($requiredProgress) }}%
                                </div>
                            </div>

                            @if($missingRequiredDocs->count() > 0)
                                <div class="alert alert-danger py-2">
                                    <small><strong>Documentos obligatorios faltantes:</strong></small>
                                    <ul class="mb-0 small">
                                        @foreach($missingRequiredDocs->take(3) as $doc)
                                            <li>{{ $doc->name }}</li>
                                        @endforeach
                                        @if($missingRequiredDocs->count() > 3)
                                            <li>... y {{ $missingRequiredDocs->count() - 3 }} más</li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        @endif
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            {{-- 🚀 NUEVO: Botón deshabilitado si faltan documentos obligatorios --}}
                            @if($missingRequiredDocs->count() > 0)
                                <button type="button" class="btn btn-success" disabled id="submit-btn">
                                    <i class="fas fa-exclamation-triangle"></i> Faltan documentos obligatorios
                                </button>
                                <small class="text-danger">Debe subir todos los documentos obligatorios para guardar.</small>
                            @else
                                <button type="submit" class="btn btn-success" id="submit-btn">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            @endif
                            <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Próximos pasos -->
                @if($company->documents->count() > 0 && $company->has_center_staff)
                    <div class="card mt-3">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0"><i class="fas fa-users"></i> Personal del Centro</h6>
                        </div>
                        <div class="card-body">
                            <p class="small">Esta empresa tiene personal del centro. Puede proceder a registrar:</p>
                            <ul class="small">
                                <li>Documentación Profesional</li>
                                <li>Documentación Personal Clínica</li>
                            </ul>
                            <a href="{{ route('staff.index', $company) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-users"></i> Gestionar Personal
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>

    <!-- Documentos existentes -->
    @if($company->documents->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-file-alt"></i> Documentos Subidos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($company->documents as $document)
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        {{ $document->name }}
                                        @if($document->required)
                                            <span class="badge bg-danger">Obligatorio</span>
                                        @else
                                            <span class="badge bg-secondary">Opcional</span>
                                        @endif
                                    </h6>
                                    <p class="card-text">
                                        <small class="text-muted">{{ $document->pivot->original_file_name }}</small>
                                    </p>
                                    @if($document->pivot->path)
                                        <a href="{{ Storage::url($document->pivot->path) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- 🚀 NUEVO: JavaScript para validación en tiempo real --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lista de documentos obligatorios (obtenida del backend)
        const requiredDocuments = @json($requiredDocs->pluck('id')->toArray());
        const uploadedDocuments = @json($uploadedRequiredDocs->pluck('id')->toArray());
        
        console.log('Documentos obligatorios:', requiredDocuments);
        console.log('Documentos ya subidos:', uploadedDocuments);
        
        // Función para validar documentos obligatorios
        function validateRequiredDocuments() {
            const form = document.getElementById('company-form');
            const submitBtn = document.getElementById('submit-btn');
            const fileInputs = form.querySelectorAll('input[type="file"]');
            
            let missingDocuments = [...requiredDocuments];
            
            // Remover documentos ya subidos
            uploadedDocuments.forEach(docId => {
                const index = missingDocuments.indexOf(docId);
                if (index > -1) {
                    missingDocuments.splice(index, 1);
                }
            });
            
            // Verificar nuevos archivos seleccionados
            fileInputs.forEach(input => {
                if (input.files && input.files.length > 0) {
                    // Extraer document ID del name del input
                    const match = input.name.match(/documents\[(\d+)\]/);
                    if (match) {
                        const docId = parseInt(match[1]);
                        const index = missingDocuments.indexOf(docId);
                        if (index > -1) {
                            missingDocuments.splice(index, 1);
                        }
                    }
                }
            });
            
            console.log('Documentos que aún faltan:', missingDocuments);
            
            // Actualizar estado del botón
            if (missingDocuments.length === 0) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
                submitBtn.className = 'btn btn-success';
            } else {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Faltan ' + missingDocuments.length + ' documentos obligatorios';
                submitBtn.className = 'btn btn-secondary';
            }
            
            return missingDocuments.length === 0;
        }
        
        // Validar al cambiar archivos
        document.addEventListener('change', function(e) {
            if (e.target.type === 'file') {
                setTimeout(validateRequiredDocuments, 100); // Pequeño delay para asegurar que el cambio se registre
            }
        });
        
        // Validar al enviar formulario
        document.getElementById('company-form').addEventListener('submit', function(e) {
            if (!validateRequiredDocuments()) {
                e.preventDefault();
                alert('Debe subir todos los documentos obligatorios antes de guardar.');
                return false;
            }
        });
        
        // Validación inicial
        validateRequiredDocuments();
    });
    </script>
@endsection