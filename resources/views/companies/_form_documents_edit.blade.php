@php
    // Obtener solo los documentos básicos que NO han sido subidos
    $basicDocuments = $documents->where('category', 'basic');
    $uploadedDocumentIds = $company->documents->pluck('id')->toArray();
    $missingDocuments = $basicDocuments->whereNotIn('id', $uploadedDocumentIds);
    $totalBasicDocuments = $basicDocuments->count();
    $uploadedCount = $basicDocuments->whereIn('id', $uploadedDocumentIds)->count();
@endphp

<div class="alert alert-info mb-3">
    <i class="fas fa-info-circle"></i> 
    <strong>Progreso:</strong> {{ $uploadedCount }}/{{ $totalBasicDocuments }} documentos básicos completados.
    @if($missingDocuments->count() > 0)
        <strong>Faltan {{ $missingDocuments->count() }} documentos por subir.</strong>
    @else
        <strong class="text-success">¡Todos los documentos básicos están completos!</strong>
    @endif
</div>

<div class="row">
    @foreach($basicDocuments as $document)
        @php
            $hasDocument = $company->documents->contains($document->id);
            $documentPivot = $hasDocument ? $company->documents->find($document->id)->pivot : null;
        @endphp
        
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">{{ $document->name }}</h6>
                    <p class="card-text text-muted small">
                        <span class="badge bg-danger">Requerido</span>
                    </p>
                    
                    @if($hasDocument)
                        <!-- Documento YA SUBIDO - Estilo como el wizard -->
                        <div class="mb-2">
                            <input type="file" 
                                   class="form-control form-control-sm"
                                   name="documents[{{ $document->id }}]"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   id="file_{{ $document->id }}"
                                   onchange="showFileName({{ $document->id }})">
                        </div>
                        
                        <!-- Mostrar archivo actual con fondo verde (como wizard) -->
                        <div class="alert alert-success py-2 mt-2">
                            <i class="fas fa-check-circle"></i>
                            <small><strong>Archivo actual:</strong> {{ $documentPivot->original_file_name }}</small>
                            <br>
                            <small class="text-muted">
                                Subido: {{ $documentPivot->created_at->format('d/m/Y H:i') }}
                            </small>
                            <div class="mt-1">
                                <a href="{{ Storage::url($documentPivot->path) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Ver archivo
                                </a>
                            </div>
                        </div>
                        
                        <!-- Mensaje cuando seleccione un nuevo archivo -->
                        <div id="new-file-{{ $document->id }}" style="display: none;" class="alert alert-warning py-2 mt-2">
                            <i class="fas fa-sync"></i>
                            <small><strong>Nuevo archivo seleccionado:</strong> <span id="filename-{{ $document->id }}"></span></small>
                            <br>
                            <small class="text-muted">Este archivo reemplazará al actual al guardar.</small>
                        </div>
                        
                    @else
                        <!-- Documento PENDIENTE - Sin subir -->
                        <div class="mb-2">
                            <input type="file" 
                                   class="form-control form-control-sm"
                                   name="documents[{{ $document->id }}]"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   id="file_{{ $document->id }}"
                                   onchange="showFileName({{ $document->id }})">
                        </div>
                        
                        <div class="alert alert-warning py-2">
                            <i class="fas fa-upload"></i>
                            <small><strong>Este documento es requerido</strong></small>
                        </div>
                        
                        <!-- Mensaje cuando seleccione archivo -->
                        <div id="new-file-{{ $document->id }}" style="display: none;" class="alert alert-success py-2 mt-2">
                            <i class="fas fa-check-circle"></i>
                            <small><strong>Archivo seleccionado:</strong> <span id="filename-{{ $document->id }}"></span></small>
                        </div>
                    @endif
                    
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle"></i> Formatos: PDF, JPG, JPEG, PNG (máx. 4MB)
                    </small>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
function showFileName(documentId) {
    const input = document.getElementById('file_' + documentId);
    const fileDiv = document.getElementById('new-file-' + documentId);
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