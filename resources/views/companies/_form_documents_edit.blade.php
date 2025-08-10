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
            $isMultipleFile = $company->isMultipleFileDocument($document->id);
            
            if ($isMultipleFile) {
                // Para documentos múltiples, obtener todos los archivos
                $documentFiles = $company->getDocumentFiles($document->id);
                $hasDocument = $documentFiles->count() > 0;
            } else {
                // Para documentos únicos, lógica original
                $hasDocument = $company->documents->contains($document->id);
                $documentPivot = $hasDocument ? $company->documents->find($document->id)->pivot : null;
            }
        @endphp
        
        <div class="col-md-6 mb-3">
            <div class="card {{ $isMultipleFile ? 'border-primary' : '' }}">
                <div class="card-body">
                    <h6 class="card-title">
                        {{ $document->name }}
                        @if($isMultipleFile)
                            <span class="badge bg-primary">Múltiples archivos</span>
                        @endif
                    </h6>
                    <p class="card-text text-muted small">
                        <span class="badge bg-danger">Requerido</span>
                        @if($isMultipleFile)
                            <span class="badge bg-info">Hasta 6 archivos</span>
                        @endif
                    </p>
                    
                    @if($isMultipleFile)
                        {{-- DOCUMENTO CON MÚLTIPLES ARCHIVOS --}}
                        
                        {{-- Mostrar archivos existentes --}}
                        @if($hasDocument)
                            <div class="mb-3">
                                <strong class="text-success">Archivos subidos ({{ $documentFiles->count() }}):</strong>
                                @foreach($documentFiles as $file)
                                    <div class="alert alert-success py-2 mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file"></i>
                                                <strong>{{ $file->original_file_name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Subido: {{ $file->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ Storage::url($file->path) }}" 
                                                target="_blank" 
                                                class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- Inputs para nuevos archivos --}}
                        <div id="multiple-files-{{ $document->id }}">
                            <strong>Agregar nuevos archivos:</strong>
                            <div class="file-inputs-container" data-document-id="{{ $document->id }}">
                                <div class="input-group mb-2">
                                    <input type="file" 
                                        class="form-control form-control-sm"
                                        name="documents[{{ $document->id }}][]"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="showMultipleFileName({{ $document->id }}, this)">
                                </div>
                            </div>
                            
                            @if($documentFiles->count() < 6)
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary add-file-btn"
                                        onclick="addFileInput({{ $document->id }})"
                                        data-document-id="{{ $document->id }}">
                                    <i class="fas fa-plus"></i> Agregar otro archivo
                                </button>
                            @endif
                        </div>
                        
                    @else
                        {{-- DOCUMENTO ÚNICO (lógica original) --}}
                        @if($hasDocument)
                            <div class="mb-2">
                                <input type="file" 
                                    class="form-control form-control-sm"
                                    name="documents[{{ $document->id }}]"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    id="file_{{ $document->id }}"
                                    onchange="showFileName({{ $document->id }})">
                            </div>
                            
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
                            
                            <div id="new-file-{{ $document->id }}" style="display: none;" class="alert alert-warning py-2 mt-2">
                                <i class="fas fa-sync"></i>
                                <small><strong>Nuevo archivo seleccionado:</strong> <span id="filename-{{ $document->id }}"></span></small>
                                <br>
                                <small class="text-muted">Este archivo reemplazará al actual al guardar.</small>
                            </div>
                            
                        @else
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
                            
                            <div id="new-file-{{ $document->id }}" style="display: none;" class="alert alert-success py-2 mt-2">
                                <i class="fas fa-check-circle"></i>
                                <small><strong>Archivo seleccionado:</strong> <span id="filename-{{ $document->id }}"></span></small>
                            </div>
                        @endif
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
// Función original para documentos únicos
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

// Nueva función para documentos múltiples
function showMultipleFileName(documentId, inputElement) {
    // Crear mensaje de archivo seleccionado si no existe
    let messageDiv = inputElement.parentNode.querySelector('.file-selected-message');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.className = 'file-selected-message alert alert-info py-1 mt-1';
        inputElement.parentNode.appendChild(messageDiv);
    }
    
    if (inputElement.files.length > 0) {
        const fileName = inputElement.files[0].name;
        messageDiv.innerHTML = `<i class="fas fa-check-circle"></i> <small><strong>Archivo seleccionado:</strong> ${fileName}</small>`;
        messageDiv.style.display = 'block';
    } else {
        messageDiv.style.display = 'none';
    }
}

function addFileInput(documentId) {
    const container = document.querySelector(`#multiple-files-${documentId} .file-inputs-container`);
    
    if (!container) {
        console.error(`No se encontró el contenedor para documento ${documentId}`);
        return;
    }
    
    // Contar archivos existentes (los que están en la sección "Archivos subidos")
    const existingFilesSection = container.closest('.card-body').querySelector('.mb-3');
    const existingFiles = existingFilesSection ? existingFilesSection.querySelectorAll('.alert-success').length : 0;
    
    // Contar inputs nuevos
    const currentInputs = container.querySelectorAll('input[type="file"]');
    
    const totalFiles = existingFiles + currentInputs.length;

    console.log(`Documento ${documentId}: ${existingFiles} existentes + ${currentInputs.length} nuevos = ${totalFiles} total`);

    if (totalFiles >= 6) {
        alert(`Máximo 6 archivos permitidos. Tienes ${existingFiles} archivos existentes y ${currentInputs.length} nuevos.`);
        return;
    }
        
    // Crear nuevo input group
    const newInputGroup = document.createElement('div');
    newInputGroup.className = 'input-group mb-2';
    newInputGroup.innerHTML = `
        <input type="file" 
            class="form-control form-control-sm"
            name="documents[${documentId}][]"
            accept=".pdf,.jpg,.jpeg,.png"
            onchange="showMultipleFileName(${documentId}, this)">
        <button type="button" 
            class="btn btn-outline-danger btn-sm"
            onclick="removeFileInput(this)"
            title="Eliminar este campo">
            <i class="fas fa-trash"></i>
        </button>
    `;
        
    // Agregar al contenedor
    container.appendChild(newInputGroup);
      
    // Ocultar botón "Agregar otro" si llegamos al límite
    const addButton = document.querySelector(`[data-document-id="${documentId}"] .add-file-btn`);
    if (currentInputs.length + 1 >= 6) {
        addButton.style.display = 'none';
    }
}

// Función para eliminar un input de archivo
function removeFileInput(buttonElement) {
    const inputGroup = buttonElement.parentNode;
    const container = inputGroup.parentNode;
    const documentId = container.getAttribute('data-document-id');
    
    // Eliminar el input group
    inputGroup.remove();
    
    // Mostrar el botón "Agregar otro" si estamos bajo el límite
    const remainingInputs = container.querySelectorAll('input[type="file"]');
    const addButton = document.querySelector(`[data-document-id="${documentId}"] .add-file-btn`);
    if (remainingInputs.length < 6 && addButton) {
        addButton.style.display = 'inline-block';
    }
}

// Inicializar: ocultar botones "Agregar otro" si ya hay 6 archivos
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('=== FORMULARIO ENVIÁNDOSE ===');
            
            // Verificar todos los inputs de archivo
            const fileInputs = form.querySelectorAll('input[type="file"]');
            fileInputs.forEach((input, index) => {
                if (input.files.length > 0) {
                    console.log(`Input ${index}: ${input.name} = ${input.files.length} archivos`);
                    for (let i = 0; i < input.files.length; i++) {
                        console.log(`  - Archivo ${i}: ${input.files[i].name}`);
                    }
                } else {
                    console.log(`Input ${index}: ${input.name} = vacío`);
                }
            });
            
            // NO prevenir el envío, solo debuggear
            console.log('=== FORMULARIO CONTINÚA ===');
        });
    }
});
</script>