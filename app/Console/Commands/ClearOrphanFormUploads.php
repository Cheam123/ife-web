<?php

namespace App\Console\Commands;

use App\Models\FormSubmission;
use App\Models\FormSubmissionDocumentUpload;
use App\Services\FormUploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Deletes form attachments that no submission refers to.
 *
 * Attachments upload the moment they are attached, before the form is
 * submitted — that is deliberate, so a failed photo can be retried without
 * losing everything the user typed. The cost is that abandoning a half-filled
 * form leaves the bytes on disk with nothing pointing at them.
 *
 * A file is only removed when BOTH hold:
 *   - it is older than the age window (a form still being filled is safe), and
 *   - no submission mentions it.
 */
class ClearOrphanFormUploads extends Command
{
    protected $signature = 'form:clear-orphan-uploads
                            {--days=7 : Leave files newer than this many days alone}
                            {--dry-run : List what would be deleted without deleting it}';

    protected $description = 'Delete form attachments that no submission references.';

    /** Where FormUploadService writes now, on the default disk. */
    private const ROOT = FormUploadService::ROOT;

    /**
     * Where it used to write, on the public disk. Those files are still
     * referenced by older answers, so they count as referenced here and are
     * never swept - only the current root is scanned for deletion.
     */
    private const LEGACY_ROOT = 'form-uploads';

    public function handle()
    {
        $days   = max(0, (int) $this->option('days'));
        $dryRun = (bool) $this->option('dry-run');
        $disk   = Storage::disk();
        $cutoff = now()->subDays($days)->getTimestamp();

        $referenced = $this->referencedFilenames();

        // Sanity gate. If extraction breaks, every file looks orphaned and this
        // command becomes "delete all attachments". Cross-check the parsed
        // result against a dumb LIKE that cannot be fooled by encoding: if rows
        // clearly mention attachments but nothing was parsed out, stop.
        $rowsWithAttachments = FormSubmissionDocumentUpload::exists()
            || FormSubmission::where('form_elements', 'like', '%' . self::LEGACY_ROOT . '%')
                ->orWhere('round_snapshots', 'like', '%' . self::LEGACY_ROOT . '%')
                ->exists();

        if (empty($referenced) && $rowsWithAttachments) {
            $this->error('Aborting: submissions reference attachments but none could be parsed.');
            $this->error('Refusing to delete anything — this would remove files that are in use.');

            return self::FAILURE;
        }

        $this->info('Referenced attachments: ' . count($referenced));

        $scanned = 0;
        $kept    = 0;
        $removed = 0;

        foreach ($disk->allFiles(self::ROOT) as $file) {
            $scanned++;

            // A form can sit half-filled for a while; only old files are
            // candidates, so an in-progress submission is never harmed.
            if ($disk->lastModified($file) > $cutoff) {
                $kept++;
                continue;
            }

            if (isset($referenced[basename($file)])) {
                $kept++;
                continue;
            }

            $removed++;
            $this->line(($dryRun ? '  would delete  ' : '  deleted       ') . $file);

            if (!$dryRun) {
                $disk->delete($file);
            }
        }

        $this->info(sprintf(
            '%sscanned %d, kept %d, %s %d.',
            $dryRun ? '[dry run] ' : '',
            $scanned,
            $kept,
            $dryRun ? 'would delete' : 'deleted',
            $removed
        ));

        return self::SUCCESS;
    }

    /**
     * Every attachment filename any submission still points at.
     *
     * Two columns matter, not one. When a loop round restarts,
     * FormApprovalService::startRound() archives the repeated range's answers
     * into `round_snapshots` and BLANKS them from `form_elements`. A photo from
     * an earlier round is then referenced only by the archive — scanning
     * `form_elements` alone would delete it and break the timeline.
     *
     * Filenames carry time()+uniqid() (see FormUploadService), so a basename is
     * a safe identity: no two uploads share one.
     *
     * @return array<string, true> filename => true, for O(1) lookup
     */
    private function referencedFilenames(): array
    {
        $referenced = [];

        // Attachments recorded against a submission are referenced by
        // definition, whatever the answer JSON happens to say.
        FormSubmissionDocumentUpload::select('id', 'filename')
            ->chunkById(1000, function ($rows) use (&$referenced) {
                foreach ($rows as $row) {
                    $referenced[$row->filename] = true;
                }
            });

        FormSubmission::select('id', 'form_elements', 'round_snapshots')
            ->chunkById(500, function ($rows) use (&$referenced) {
                foreach ($rows as $row) {
                    // Re-encode from the CAST arrays with unescaped slashes.
                    // Reading the raw column instead would hand back JSON that
                    // already escapes "/" as "\/", and the path would never
                    // match — every file would then look orphaned.
                    $haystack = json_encode(
                        [$row->form_elements, $row->round_snapshots],
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                    );

                    // Filenames are sanitised to word characters, dots and
                    // dashes, so an explicit class avoids escaping games.
                    $pattern = '#(?:' . self::ROOT . '|' . self::LEGACY_ROOT . ')/[A-Za-z0-9_./%()+,@=~-]+#';

                    if (!preg_match_all($pattern, $haystack, $matches)) {
                        continue;
                    }

                    foreach ($matches[0] as $path) {
                        // Drop any ?query a stored URL might carry.
                        $referenced[basename(strtok($path, '?'))] = true;
                    }
                }
            });

        return $referenced;
    }
}
