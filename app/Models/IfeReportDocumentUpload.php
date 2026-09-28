<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfeReportDocumentUpload extends Model
{
    use HasFactory;

    protected $table = 'ife_report_document_uploads';

    protected $fillable = [
        'ife_report_id',
        'filename',
        'mime_type',
        'size',
        'upload_by',
        'created_at',
        'updated_at',
    ];

    public function ife_report(){
        return $this->belongsTo(IfeReport::class, 'ife_report_id');
    }

    public function getFilePathAttribute()
    {
        return "ife/{$this->ife_report_id}";
    }

    public function getFileFullPathAttribute()
    {
        $fpath = "ife/{$this->ife_report_id}/{$this->filename}";
        return config('app.aws_s3_path') . '/' . $fpath;
    }
}
