<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A named container for forms, used to keep the form list navigable as the
 * number of forms grows. Purely organisational — access to a form is still
 * decided by that form's own "Who can submit" setting.
 */
class FormGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position'];

    /** Forms in this group, in the order an admin arranged them. */
    public function forms()
    {
        return $this->hasMany(Form::class)
            ->orderBy('position')
            ->orderBy('name');
    }

    /** Groups in display order; ties broken by name so the order is stable. */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('name');
    }
}
