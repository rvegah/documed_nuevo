<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Staff extends Model
{
    const TYPE_PROFESSIONAL = 'professional';
    const TYPE_CLINICAL = 'clinical';
    
    const STATUS_TRAMITACION = 'Tramitación';
    const STATUS_PRESENTADA = 'Presentada';
    const STATUS_APROBADA = 'Aprobada';
    const STATUS_RESUELTA = 'Resuelta';

    protected $fillable = [
        'company_id',
        'type',
        'name',
        'dni',
        'email',
        'phone',
        'status',
        'active',
        'user_id'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'staff_document')
            ->withPivot('path', 'original_file_name', 'valid', 'valid_date', 'valid_user_id', 'comments', 'user_id')
            ->withTimestamps();
    }

    // Accessor para el tipo en español
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            self::TYPE_PROFESSIONAL => 'Profesional',
            self::TYPE_CLINICAL => 'Personal Clínico',
            default => 'Desconocido'
        };
    }
}