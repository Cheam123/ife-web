<?php

namespace App\Services;

use App\Models\FormSubmission;
use App\Models\FormSubmissionDocumentUpload;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Attachments for `file` form fields.
 *
 * Follows IFE and Task exactly: files ride along with the
 * submission as multipart, land on the DEFAULT disk under a per-parent folder
 * ("form/{id}", mirroring "ife/{id}"), and take one row each in an owned
 * table. URLs are built the same way too - see FormSubmissionDocumentUpload.
 *
 * The ordering matches IFE as well: the parent row is saved first, because the
 * folder is named after its id. The one thing forms need that IFE does not is
 * validation of a mandatory `file` field, which runs before the submission
 * exists - pendingAnswers() covers that gap, and applyToSnapshot() writes the
 * real values once the files are down.
 */
class FormUploadService
{
    /** Largest attachment accepted, in kilobytes. */
    public const MAX_KB = 10240;

    /** Request key carrying attachments: files[el_photo][] */
    public const REQUEST_KEY = 'files';

    /** Folder root on the default disk, mirroring "ife/{id}". */
    public const ROOT = 'form';

    /**
     * Attachments posted alongside a submission, keyed by element id.
     *
     * Both shapes accepted, so a single file need not be wrapped:
     *   files[el_photo][] = <file>, <file>
     *   files[el_photo]   = <file>
     *
     * @return array<string, array<int, UploadedFile>>
     */
    public function bundledFiles(Request $request): array
    {
        $bundle = $request->file(self::REQUEST_KEY);

        if (!is_array($bundle)) {
            return [];
        }

        $byElement = [];

        foreach ($bundle as $elementId => $files) {
            foreach (is_array($files) ? $files : [$files] as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $byElement[(string) $elementId][] = $file;
                }
            }
        }

        return $byElement;
    }

    /**
     * Stand-in answers so a mandatory `file` field passes validation.
     *
     * The real values cannot exist yet: the folder is named after the
     * submission id, which only exists after the insert. These are replaced
     * wholesale by applyToSnapshot() once the files are written.
     *
     * @param  array<string, array<int, UploadedFile>> $byElement
     * @return array<string, array<int, string>>
     */
    public function pendingAnswers(array $byElement): array
    {
        return collect($byElement)
            ->map(fn ($files) => array_map(fn ($f) => $f->getClientOriginalName(), $files))
            ->all();
    }

    /**
     * Write the files and record a row for each, exactly as IFE does.
     *
     * Rows for an element are cleared first: re-answering a field replaces its
     * attachments, the same as re-answering any other field.
     *
     * @param  array<string, array<int, UploadedFile>> $byElement
     * @return array<string, array> element id => answer value
     */
    public function storeFor(FormSubmission $submission, array $byElement, ?int $userId = null): array
    {
        $answers = [];

        foreach ($byElement as $elementId => $files) {
            FormSubmissionDocumentUpload::where('form_submission_id', $submission->id)
                ->where('element_id', $elementId)
                ->delete();

            $value = [];

            foreach (array_values($files) as $i => $file) {
                $originalName  = $file->getClientOriginalName();
                $sanitizedName = str_replace(' ', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $extension     = $file->getClientOriginalExtension();

                $filename = $sanitizedName . '_' . $submission->id . '-' . ($i + 1) . '-' . time() . '.' . $extension;
                $path     = self::ROOT . '/' . $submission->id;

                Storage::putFileAs($path, new File($file), $filename);

                $row = FormSubmissionDocumentUpload::create([
                    'form_submission_id' => $submission->id,
                    'element_id'         => $elementId,
                    'path'               => $path,
                    'filename'           => $filename,
                    'original_name'      => $originalName,
                    'mime_type'          => $file->getMimeType(),
                    'size'               => $file->getSize(),
                    'upload_by'          => $userId,
                ]);

                $value[] = $row->toAnswerValue();
            }

            $answers[$elementId] = $value;
        }

        return $answers;
    }

    /**
     * Write the real file answers into the stored snapshot, replacing the
     * placeholders validation ran against.
     *
     * @param array<string, array> $answers element id => answer value
     */
    public function applyToSnapshot(FormSubmission $submission, array $answers): void
    {
        if (empty($answers)) {
            return;
        }

        $snapshot = collect($submission->form_elements ?? [])
            ->map(function ($row) use ($answers) {
                $id = is_array($row) ? ($row['id'] ?? null) : null;

                if ($id !== null && array_key_exists($id, $answers)) {
                    $row['value'] = $answers[$id];
                }

                return $row;
            })
            ->values()
            ->all();

        $submission->update(['form_elements' => $snapshot]);
    }

    /**
     * Which elements the saved answers actually kept.
     *
     * A field hidden by `visible_when`, or owned by a later stage, has its
     * answer nulled during sanitising; storing attachments for it would leave
     * rows pointing at a field that renders nothing.
     *
     * @param  array<string, array<int, UploadedFile>> $byElement
     * @return array<string, array<int, UploadedFile>>
     */
    public function keptBySnapshot(array $byElement, array $snapshot): array
    {
        $values = collect($snapshot)
            ->filter(fn ($row) => is_array($row) && isset($row['id']))
            ->keyBy('id');

        return collect($byElement)
            ->filter(function ($files, $elementId) use ($values) {
                $value = $values->get($elementId)['value'] ?? null;

                return $value !== null && $value !== [] && $value !== '';
            })
            ->all();
    }

    /**
     * Normalise `form_data` to element id => value.
     *
     * It arrives either as [{id, value}, ...] or {el_id: value, ...}.
     *
     * @return array<string, mixed>
     */
    public function keyAnswers(array $formData): array
    {
        $keyed = [];

        foreach ($formData as $key => $entry) {
            if (is_array($entry) && array_key_exists('id', $entry)) {
                $keyed[$entry['id']] = $entry['value'] ?? null;
            } else {
                $keyed[$key] = $entry;
            }
        }

        return $keyed;
    }

    /**
     * Merge extra answers in before sanitising.
     *
     * Returns the [{id, value}, ...] LIST shape, because that is what
     * FormSchemaService::answersById() reads - it calls array_values() and
     * skips anything that is not an array carrying an `id`, so handing it a
     * keyed map silently discards every answer.
     *
     * @return array<int, array{id:string, value:mixed}>
     */
    public function mergeIntoFormData(array $formData, array $extra): array
    {
        $merged = array_merge($this->keyAnswers($formData), $extra);

        return collect($merged)
            ->map(fn ($value, $id) => ['id' => (string) $id, 'value' => $value])
            ->values()
            ->all();
    }
}
