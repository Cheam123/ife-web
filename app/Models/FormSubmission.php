<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * One CASE — a single submission of a form, with its own reference, process
 * and open/closed status.
 *
 * A case may follow up on ONE earlier case of the same form
 * (`parent_submission_id`), which is how an emergency callout attaches to the
 * service job it came from. The link is one level deep: a case that already
 * has a parent cannot itself be a parent, so chains cannot form and a case can
 * never become its own ancestor.
 *
 * A closed or completed parent still accepts new children — that is the whole
 * point, and the reason this replaced the old multi-entry record.
 */
class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'parent_submission_id',
        'record_title',
        'record_status',
        'record_closed_remark',
        'record_closed_by',
        'record_closed_at',
        'submitted_by',
        'form_elements',
        'process_snapshot',
        'round_snapshots',
        'status',
        'rejected_remark',
        'rejected_by',
    ];

    protected $casts = [
        'form_elements'    => 'array',
        'process_snapshot' => 'array',
        'round_snapshots'  => 'array',
        'record_closed_at' => 'datetime',
    ];

    /**
     * Get the form that this submission belongs to.
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    /** The earlier case this one follows up on, if any. */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_submission_id');
    }

    /** Cases opened as follow-ups to this one, oldest first. */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_submission_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    /**
     * Whether this case may be chosen as somebody's parent. Only top-level
     * cases may: the link is one level deep, so a follow-up cannot itself be
     * followed up on.
     */
    public function canBeParent(): bool
    {
        return $this->parent_submission_id === null;
    }

    /** Cases are open until an admin closes them. */
    public function isRecordOpen(): bool
    {
        return ($this->record_status ?? 'open') !== 'closed';
    }

    /**
     * Attachments answering this entry's `file` fields, in the same shape the
     * other modules use (ife_report_document_uploads, document_uploads).
     */
    public function documentUploads()
    {
        return $this->hasMany(FormSubmissionDocumentUpload::class, 'form_submission_id');
    }

    /** The admin who closed this record, when one has. */
    public function recordClosedBy()
    {
        return $this->belongsTo(User::class, 'record_closed_by');
    }

    /**
     * Reference shown to users, e.g. REC-0042. Built from the id, which is what
     * the old root pointer already held for every row — existing references are
     * unchanged.
     */
    public function recordReference(): string
    {
        return 'REC-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user who submitted the form.
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Resolved per-stage approval rows (snapshot of the process chain).
     */
    public function approvals()
    {
        return $this->hasMany(FormSubmissionApproval::class)->orderBy('sequence');
    }

    /**
     * The stage currently awaiting action — the lowest-sequence pending
     * blocking row (approval or fill phase).
     */
    public function currentStage(): ?FormSubmissionApproval
    {
        return $this->approvals
            ->first(fn ($row) => $row->isBlockingNode() && $row->status === 'pending');
    }

    /**
     * True while no approver/fill assignee has acted yet (submitter may still edit).
     */
    public function isUntouched(): bool
    {
        return $this->approvals
            ->filter(fn ($row) => $row->isBlockingNode())
            ->every(fn ($row) => $row->status === 'pending' && empty($row->actions));
    }

    /**
     * Get the user who rejected the submission.
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
