<?php

namespace App\Observers;

use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentUploadObserver
{
    /**
     * Handle the DocumentUpload "created" event.
     *
     * @param  \App\Models\DocumentUpload  $documentUpload
     * @return void
     */
    public function created(DocumentUpload $documentUpload)
    {
        //
    }

    /**
     * Handle the DocumentUpload "updated" event.
     *
     * @param  \App\Models\DocumentUpload  $documentUpload
     * @return void
     */
    public function updated(DocumentUpload $documentUpload)
    {
        //
    }

    /**
     * Handle the DocumentUpload "deleted" event.
     *
     * @param  \App\Models\DocumentUpload  $documentUpload
     * @return void
     */
    public function deleted(DocumentUpload $documentUpload)
    {
        Storage::delete($documentUpload->file_path);
    }

    /**
     * Handle the DocumentUpload "restored" event.
     *
     * @param  \App\Models\DocumentUpload  $documentUpload
     * @return void
     */
    public function restored(DocumentUpload $documentUpload)
    {
        //
    }

    /**
     * Handle the DocumentUpload "force deleted" event.
     *
     * @param  \App\Models\DocumentUpload  $documentUpload
     * @return void
     */
    public function forceDeleted(DocumentUpload $documentUpload)
    {
        //
    }
}
