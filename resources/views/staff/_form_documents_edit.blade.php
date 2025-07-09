@php
    // Obtener documentos según el tipo de staff
    $availableDocuments = $documents->where('category', $staff->type);
    $uploadedDocumentIds = $staff->documents->pluck('id')->toArray();
    $missingDocuments = $availableDocuments->whereNotIn('id', $uploadedDocumentIds);
    $totalDocuments = $availableDocuments->count();
    $uploadedCount = $availableDocuments->whereIn('id', $uploadedDocumentIds)->count();
@endphp

<div class="alert alert-info mb-3">
    <i class="fas fa-info-circle"></i> 
    <strong>Progreso:</strong> {{ $uploadedCount }}/{{ $totalDocuments }} documentos completados.
    @if($missingDocuments->count() > 0)
        <strong>Faltan {{ $missingDocuments->count() }} documentos por subir.</strong>
    @else
        <strong class="text-success">¡Todos los documentos están completos!</strong>
    @endif
</div>

@if($missingDocuments->count() > 0)
    <div class="alert alert-warning mb-3">
        <i class="fas fa-upload"></i> 
        <strong>Documentos pendientes:</strong> A continuación puedes subir los documentos que faltan.
    </div>

    <div class="row">
        @foreach($missingDocuments as $document)
            <div class="col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="card-title text-warning">
                                <i class="fas fa-exclamation-triangle"></i> {{ $document->name }}
                            </h6>
                            <span class="badge bg-warning text-dark">
                                📋 Pendiente
                            </span>
                        </div>
                        
                        <div class="alert alert-warning py-2 mb-2">
                            <small><i class="fas fa-upload"></i> <strong>Este documento es requerido</strong></small>
                        </div>
                        
                        <div class="mt-2">
                            <label class="form-label small fw-bold">Subir archivo:</label>
                            <input type="file" 
                                   class="form-control form-control-sm border-warning"
                                   name="documents[{{ $document->id }}]"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   onchange="showFileName({{ $document->id }})">
                            <small class="text-muted">
                                <i class="fas fa-file"></i> Formatos: PDF, JPG, JPEG, PNG (máx. 4MB)
                            </small>
                        </div>

                        <!-- Mostrar archivo seleccionado -->
                        <div id="file-selected-{{ $document->id }}" style="display: none;" class="alert alert-success py-2 mt-2">
                            <i class="fas fa-check-circle"></i>
                            <small><strong>Archivo seleccionado:</strong> <span id="filename-{{ $document->id }}"></span></small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> 
        <strong>¡Perfecto!</strong> Ya has subido todos los documentos requeridos.
    </div>
@endif

@if($uploadedCount > 0)
    <hr>
    <h6 class="text-success">
        <i class="fas fa-check-circle"></i> Documentos ya subidos ({{ $uploadedCount }})
    </h6>
    
    <div class="row">
        @foreach($staff->documents as $uploadedDoc)
            <div class="col-md-6 mb-3">
                <div class="card border-success bg-light">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="card-title text-success">
                                <i class="fas fa-check"></i> {{ $uploadedDoc->name }}
                            </h6>
                            <span class="badge bg-success">
                                ✅ Completado
                            </span>
                        </div>
                        
                        <div class="alert alert-success py-2 mt-2 mb-2">
                            <small>
                                <i class="fas fa-file"></i> 
                                <strong>{{ $uploadedDoc->pivot->original_file_name }}</strong>
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> Subido: {{ $uploadedDoc->pivot->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        
                        <div class="mt-2">
                            <a href="{{ Storage::url($uploadedDoc->pivot->path) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-success me-1">
                                <i class="fas fa-eye"></i> Ver archivo
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-warning"
                                    onclick="toggleReplaceFile({{ $uploadedDoc->id }})">
                                <i class="fas fa-sync"></i> Reemplazar
                            </button>
                        </div>
                        
                        <!-- Campo para reemplazar archivo (oculto por defecto) -->
                        <div id="replace-{{ $uploadedDoc->id }}" class="mt-2" style="display: none;">
                            <div class="alert alert-info py-2">
                                <small><i class="fas fa-info-circle"></i> Selecciona un nuevo archivo para reemplazar el actual</small>
                            </div>
                            <input type="file" 
                                   class="form-control form-control-sm"
                                   name="documents[{{ $uploadedDoc->id }}]"
                                   accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
function showFileName(documentId) {
    const input = document.querySelector(`input[name="documents[${documentId}]"]`);
    const fileDiv = document.getElementById('file-selected-' + documentId);
    const filenameSpan = document.getElementById('filename-' + documentId);
    
    if (input && input.files.length > 0) {
        const fileName = input.files[0].name;
        filenameSpan.textContent = fileName;
        fileDiv.style.display = 'block';
    } else {
        fileDiv.style.display = 'none';
    }
}

function toggleReplaceFile(documentId) {
    const replaceDiv = document.getElementById('replace-' + documentId);
    if (replaceDiv.style.display === 'none') {
        replaceDiv.style.display = 'block';
    } else {
        replaceDiv.style.display = 'none';
    }
}
</script>