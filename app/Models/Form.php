<?php

namespace App\Models;

use App\Services\FormProcessService;
use App\Services\FormSchemaService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'form_group_id',
        'position',
        'form_elements',
        'settings',
        'process_definition',
        'description',
        'is_enabled',
    ];

    protected $casts = [
        'form_elements'      => 'array',
        'settings'           => 'array',
        'process_definition' => 'array',
    ];

    /**
     * Get the submissions for this form.
     */
    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    /** The group this form is filed under, if any. */
    public function group()
    {
        return $this->belongsTo(FormGroup::class, 'form_group_id');
    }

    /** Forms in the order an admin arranged them. */
    public function scopeArranged($query)
    {
        return $query->orderBy('position')->orderBy('name');
    }

    /**
     * Normalized v2 field schema (legacy rows upgraded on read).
     */
    public function getSchemaAttribute(): array
    {
        return app(FormSchemaService::class)->normalize($this->form_elements);
    }

    /**
     * Normalized process definition.
     */
    public function getProcessAttribute(): array
    {
        return app(FormProcessService::class)->normalize($this->process_definition);
    }
}
