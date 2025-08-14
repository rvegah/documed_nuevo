@extends('layouts.documed')

@section('title', 'Configurar Documentos')

@section('content-documed')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-cogs text-primary"></i>
            Configurar Documentos Obligatorios
        </h1>
        <a href="{{ route('admin.document-approval.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Documentos Básicos --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-building"></i> Documentos Básicos de Empresa
            </h5>
        </div>
        <div class="card-body">
            @foreach($documents->where('category', 'basic') as $document)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>{{ $document->name }}</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge {{ $document->required ? 'bg-danger' : 'bg-secondary' }} me-3">
                            {{ $document->required ? 'Obligatorio' : 'Opcional' }}
                        </span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   data-document-id="{{ $document->id }}"
                                   {{ $document->required ? 'checked' : '' }}>
                            <label class="form-check-label">Obligatorio</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Documentos Profesionales --}}
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-md"></i> Documentos Profesionales
            </h5>
        </div>
        <div class="card-body">
            @foreach($documents->where('category', 'professional') as $document)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>{{ $document->name }}</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge {{ $document->required ? 'bg-danger' : 'bg-secondary' }} me-3">
                            {{ $document->required ? 'Obligatorio' : 'Opcional' }}
                        </span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   data-document-id="{{ $document->id }}"
                                   {{ $document->required ? 'checked' : '' }}>
                            <label class="form-check-label">Obligatorio</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Documentos Personal Clínico --}}
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-users"></i> Documentos Personal Clínico
            </h5>
        </div>
        <div class="card-body">
            @foreach($documents->where('category', 'clinical') as $document)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>{{ $document->name }}</strong>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge {{ $document->required ? 'bg-danger' : 'bg-secondary' }} me-3">
                            {{ $document->required ? 'Obligatorio' : 'Opcional' }}
                        </span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   data-document-id="{{ $document->id }}"
                                   {{ $document->required ? 'checked' : '' }}>
                            <label class="form-check-label">Obligatorio</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todos los switches
        const switches = document.querySelectorAll('.form-check-input');
        
        switches.forEach(switchElement => {
            switchElement.addEventListener('change', function() {
                const documentId = this.getAttribute('data-document-id');
                const isRequired = this.checked;
                
                // Hacer petición AJAX
                fetch(`/admin/document-config/${documentId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar el badge
                        const badge = this.closest('.d-flex').querySelector('.badge');
                        if (data.required) {
                            badge.className = 'badge bg-danger me-3';
                            badge.textContent = 'Obligatorio';
                        } else {
                            badge.className = 'badge bg-secondary me-3';
                            badge.textContent = 'Opcional';
                        }
                        
                        // Mostrar mensaje de éxito (opcional)
                        console.log(data.message);
                        
                        // Opcional: Mostrar toast o notificación
                        showToast(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revertir el switch si hay error
                    this.checked = !isRequired;
                    
                    // Mostrar mensaje de error
                    showToast('Error al actualizar el documento', 'error');
                });
            });
        });
        
        // Función para mostrar notificaciones (opcional)
        function showToast(message, type) {
            // Crear elemento de notificación
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Agregar al body
            document.body.appendChild(toast);
            
            // Auto-remover después de 3 segundos
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }
    });
    </script>

@endsection