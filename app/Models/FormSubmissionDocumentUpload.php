<?php

namespace App\Models;

use App\Services\FormUploadService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * One attachment on a form answer. Mirrors IfeReportDocumentUpload, with an
 * `element_id` because a form can have more than one `file` field.
 */
class FormSubmissionDocumentUpload extends Model
{
    use HasFactory;

    protected $table = 'form_submission_document_uploads';

    protected $fillable = [
        'form_submission_id',
        'element_id',
        'path',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'upload_by',
    ];

    public function submission()
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'upload_by');
    }

    /** Folder these files live in, mirroring "ife/{id}". */
    public function getFilePathAttribute()
    {
        return FormUploadService::ROOT . "/{$this->form_submission_id}";
    }

    /**
     * Full path, built the same way IFE and Task build theirs: the S3 base
     * plus the object key. Empty AWS_URL therefore yields a relative path,
     * exactly as it does for chat attachments.
     */
    public function getFileFullPathAttribute()
    {
        $fpath = FormUploadService::ROOT . "/{$this->form_submission_id}/{$this->filename}";

        return config('app.aws_s3_path') . '/' . $fpath;
    }

    /**
     * The shape an answer stores. Keys match Helper::media_documents_array()
     * so a client can render a form attachment with the same code it uses for
     * an IFE or chat one.
     */
    public function toAnswerValue(): array
    {
        return [
            'id'        => $this->id,
            'file_name' => $this->original_name ?: $this->filename,
            'file_path' => $this->file_full_path,
            'file_type' => strtolower(pathinfo($this->filename, PATHINFO_EXTENSION)),
            'size'      => $this->size,
            'mime_type' => $this->mime_type,
        ];
    }
}
