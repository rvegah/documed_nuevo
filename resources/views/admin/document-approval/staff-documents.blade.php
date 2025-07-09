@extends('layouts.documed')

@section('title', 'Revisar Documentos de Personal')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas {{ $staff->type == 'professional' ? 'fa-user-md text-primary' : 'fa-user-nurse text-success' }}"></i>
            Revisar Documentos: {{ $staff->name }}
        </h1>
        <a href="{{ route('admin.document-approval.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Panel
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-file-alt"></i> 
                        Documentos Subidos ({{ $staff->documents->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($staff->documents->count() > 0)
                        @foreach($staff->documents as $document)
                            <div class="card mb-3 document-card" id="document-{{ $document->id }}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6>{{ $document->name }}</h6>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-file"></i> 
                                                <strong>Archivo:</strong> {{ $document->pivot->original_file_name }}<br>
                                                <i class="fas fa-clock"></i> 
                                                <strong>Subido:</strong> {{ $document->pivot->created_at->format('d/m/Y H:i') }}
                                            </p>
                                            
                                            <!-- Estado actual -->
                                            <div class="mb-2">
                                                @if($document->pivot->valid === null)
                                                    <span class="badge bg-warning">Pendiente de Revisión</span>
                                                @elseif($document->pivot->valid === true)
                                                    <span class="badge bg-success">Aprobado</span>
                                                @else
                                                    <span class="badge bg-danger">Rechazado</span>
                                                @endif
                                            </div>

                                            <!-- Comentarios anteriores -->
                                            @if($document->pivot->comments)
                                                <div class="alert alert-info py-2">
                                                    <small>
                                                        <i class="fas fa-comment"></i> 
                                                        <strong>Comentarios:</strong> {{ $document->pivot->comments }}
                                                    </small>
                                                </div>
                                            @endif

                                            <!-- Información de aprobación -->
                                            @if($document->pivot->valid_date)
                                                <div class="alert alert-light py-2">
                                                    <small>
                                                        <i class="fas fa-user"></i> 
                                                        <strong>Revisado:</strong> {{ $document->pivot->valid_date->format('d/m/Y H:i') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <!-- Botón ver archivo -->
                                            <a href="{{ Storage::url($document->pivot->path) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary mb-2">
                                                <i class="fas fa-eye"></i> Ver Archivo
                                            </a>
                                            
                                            <!-- Botones de acción -->
                                            <div class="d-grid gap-2">
                                                <button type="button" 
                                                        class="btn btn-sm btn-success approve-btn"
                                                        onclick="showApprovalModal({{ $document->id }}, '{{ $document->name }}', 'approve')">
                                                    <i class="fas fa-check"></i> Aprobar
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger reject-btn"
                                                        onclick="showApprovalModal({{ $document->id }}, '{{ $document->name }}', 'reject')">
                                                    <i class="fas fa-times"></i> Rechazar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay documentos subidos para este personal</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información lateral -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> Información del Personal</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nombre:</strong> {{ $staff->name }}</p>
                    <p><strong>DNI:</strong> {{ $staff->dni }}</p>
                    <p><strong>Empresa:</strong> {{ $staff->company->company_name }}</p>
                    <p><strong>Tipo:</strong> 
                        <span class="badge {{ $staff->type == 'professional' ? 'bg-primary' : 'bg-success' }}">
                            {{ $staff->type_name }}
                        </span>
                    </p>
                    <p><strong>Estado:</strong> 
                        <span class="badge bg-info">{{ $staff->status }}</span>
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Progreso de Aprobación</h6>
                </div>
                <div class="card-body">
                    @php
                        $total = $staff->documents->count();
                        $approved = $staff->documents->where('pivot.valid', true)->count();
                        $rejected = $staff->documents->where('pivot.valid', false)->count();
                        $pending = $total - $approved - $rejected;
                    @endphp
                    <div class="mb-2">
                        <small>Aprobados: {{ $approved }}/{{ $total }}</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: {{ $total > 0 ? ($approved/$total)*100 : 0 }}%">
                                {{ $approved }}
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small>Rechazados: {{ $rejected }}/{{ $total }}</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" style="width: {{ $total > 0 ? ($rejected/$total)*100 : 0 }}%">
                                {{ $rejected }}
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small>Pendientes: {{ $pending }}/{{ $total }}</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" style="width: {{ $total > 0 ? ($pending/$total)*100 : 0 }}%">
                                {{ $pending }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para aprobar/rechazar -->
    <div class="modal fade" id="approvalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalTitle">Aprobar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="approvalModalMessage">¿Estás seguro de aprobar este documento?</p>
                    <div id="commentsSection">
                        <label for="comments" class="form-label">Comentarios:</label>
                        <textarea class="form-control" id="comments" rows="3" 
                                  placeholder="Añade comentarios sobre la revisión..."></textarea>
                        <small class="text-muted">Los comentarios son opcionales para aprobación, obligatorios para rechazo.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmApprovalBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentDocumentId = null;
        let currentAction = null;

        function showApprovalModal(documentId, documentName, action) {
            currentDocumentId = documentId;
            currentAction = action;
            
            const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
            const title = document.getElementById('approvalModalTitle');
            const message = document.getElementById('approvalModalMessage');
            const confirmBtn = document.getElementById('confirmApprovalBtn');
            const commentsTextarea = document.getElementById('comments');
            
            if (action === 'approve') {
                title.textContent = 'Aprobar Documento';
                message.textContent = `¿Estás seguro de aprobar el documento "${documentName}"?`;
                confirmBtn.textContent = 'Aprobar';
                confirmBtn.className = 'btn btn-success';
                commentsTextarea.placeholder = 'Comentarios opcionales...';
            } else {
                title.textContent = 'Rechazar Documento';
                message.textContent = `¿Estás seguro de rechazar el documento "${documentName}"?`;
                confirmBtn.textContent = 'Rechazar';
                confirmBtn.className = 'btn btn-danger';
                commentsTextarea.placeholder = 'Explica por qué rechazas este documento (obligatorio)...';
            }
            
            commentsTextarea.value = '';
            modal.show();
        }

        document.getElementById('confirmApprovalBtn').addEventListener('click', function() {
            const comments = document.getElementById('comments').value;
            
            // Validar comentarios obligatorios para rechazo
            if (currentAction === 'reject' && !comments.trim()) {
                alert('Los comentarios son obligatorios para rechazar un documento.');
                return;
            }
            
            processApproval(currentDocumentId, currentAction, comments);
        });

        function processApproval(documentId, action, comments) {
            const url = `{{ route('admin.document-approval.staff.approve', ['staff' => $staff->id, 'document' => '__DOC_ID__']) }}`.replace('__DOC_ID__', documentId);
            const finalUrl = action === 'approve' ? url : url.replace('/approve', '/reject');
            
            fetch(finalUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    comments: comments
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('approvalModal')).hide();
                    
                    // Mostrar mensaje de éxito
                    alert(data.message);
                    
                    // Recargar la página para mostrar cambios
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'No se pudo procesar la solicitud'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }
    </script>
@endsection