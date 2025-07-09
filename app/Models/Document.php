<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'order',
        'active',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class)
            ->withPivot('path', 'valid', 'valid_date', 'valid_user_id', 'comments')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }

}
