<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    const STATUS_TRAMITACION = 'Tramitación';
    const STATUS_PRESENTADA = 'Presentada';
    const STATUS_APROBADA = 'Aprobada';
    const STATUS_RESUELTA = 'Resuelta';
    const STATUS_RECHAZADA = 'Rechazada';

    const TYPE_CLINIC = 'clinic';
    const TYPE_PROFESSIONAL = 'professional';
    const TYPE_BOTH = 'both';

    protected $fillable = [
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'province',
        'legal_representative_dni',
        'rn_owner',
        'has_center_staff',
        'company_type',
        'notes',
        'user_id',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'has_center_staff' => 'boolean',
        'deleted' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class)
            ->withPivot('path', 'original_file_name', 'valid', 'valid_date', 'valid_user_id', 'comments', 'user_id', 'file_index')
            ->withTimestamps();
    }

    // 🚀 NUEVAS RELACIONES PARA STAFF
    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function professionals(): HasMany
    {
        return $this->hasMany(Staff::class)->where('type', Staff::TYPE_PROFESSIONAL);
    }

    public function clinicalStaff(): HasMany
    {
        return $this->hasMany(Staff::class)->where('type', Staff::TYPE_CLINICAL);
    }

    public function getUrlDocumentsAttribute(): string
    {
        return "company_documents/{$this->id}";
    }

    /**
     * Obtener todos los archivos de un documento específico (para documentos múltiples)
     */
    public function getDocumentFiles($documentId)
    {
        return $this->documents()
            ->where('document_id', $documentId)
            ->get()
            ->map(function ($document) {
                return (object) [
                    'file_index' => $document->pivot->file_index,
                    'path' => $document->pivot->path,
                    'original_file_name' => $document->pivot->original_file_name,
                    'created_at' => $document->pivot->created_at,
                    'pivot' => $document->pivot
                ];
            })
            ->sortBy('file_index');
    }

    /**
     * Verificar si un documento permite múltiples archivos
     */
    public function isMultipleFileDocument($documentId)
    {
        // Solo "Contratos de Mantenimiento" (ID: 45) permite múltiples archivos
        return $documentId == 45;
    }
}