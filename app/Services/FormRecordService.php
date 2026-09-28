<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\User;

/**
 * Cases — the single object every form produces.
 *
 * One submission IS one case. A case may point at ONE earlier case of the same
 * form (`parent_submission_id`): the emergency callout that came out of a
 * finished service job. The link is one level deep and never changes what a
 * case is — each keeps its own reference, process, status and permissions.
 */
class FormRecordService
{
    private FormSchemaService $schema;
    private FormApprovalService $approvals;

    public function __construct(?FormSchemaService $schema = null, ?FormApprovalService $approvals = null)
    {
        $this->schema    = $schema ?? new FormSchemaService();
        $this->approvals = $approvals ?? new FormApprovalService();
    }

    /** Display title: what the opener typed, else "{form} #{id}". */
    public function titleFor(FormSubmission $case): string
    {
        $title = trim((string) ($case->record_title ?? ''));
        if ($title !== '') {
            return $title;
        }

        return (optional($case->form)->name ?? 'Record') . ' #' . $case->id;
    }

    /**
     * Who may read a case: form admins, plus whoever submitted it or took part
     * in it. A parent link grants nothing — the two cases are separate work,
     * and seeing one must not reveal the other.
     */
    public function canView(FormSubmission $case, User $user): bool
    {
        if ($user->can('form_admin')) {
            return true;
        }

        return (int) $case->submitted_by === (int) $user->id
            || $this->approvals->isParticipant($case, $user);
    }

    /**
     * Who may close or reopen a case: the person who opened it, or a form
     * admin. Closing ends the work and cancels whatever is in flight, so it
     * belongs to the owner — but an admin has to be able to reach a case whose
     * opener has left, which a submitter-only rule would strand forever.
     *
     * A handler part-way through the case is neither, and cannot.
     */
    public function canClose(FormSubmission $case, User $user): bool
    {
        return (int) $case->submitted_by === (int) $user->id
            || $user->can('form_admin');
    }

    /**
     * Cases this user may see. Admins see everything; everyone else sees what
     * they submitted or took part in.
     */
    public function visibleFor(?User $user = null)
    {
        $query = FormSubmission::query();

        if (!$user || $user->can('form_admin')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('submitted_by', $user->id)
              ->orWhereExists(function ($inner) use ($user) {
                  $inner->selectRaw('1')
                        ->from('form_submission_approvals')
                        ->whereColumn('form_submission_approvals.form_submission_id', 'form_submissions.id')
                        ->whereJsonContains('approver_ids', (int) $user->id);
              });
        });
    }

    /**
     * The cases a new submission of this form may be attached to: top-level
     * cases of the SAME form that the user may see, newest first.
     *
     * Deliberately includes completed and closed cases — attaching an
     * emergency to a finished job is the reason this exists.
     *
     * The web renders the whole list into a select and passes neither argument.
     * The app cannot: it pages a picker over a phone, so it narrows by $search
     * and caps with $limit. Both are optional and the rule about WHICH cases
     * qualify stays here, so the two pickers cannot drift apart.
     *
     * @param  string|null  $search  Matches the case title, or a reference —
     *                               "REC-0042", "0042" and "42" all mean id 42.
     * @param  int          $limit   0 for no cap.
     */
    public function parentOptionsFor(Form $form, User $user, ?string $search = null, int $limit = 0)
    {
        $query = $this->visibleFor($user)
            ->where('form_id', $form->id)
            ->whereNull('parent_submission_id')
            ->with('form')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $term = trim((string) $search);

        if ($term !== '') {
            // An untitled case displays as "{form} #{id}", so there is nothing
            // in record_title to match it by — the id branch is what finds it.
            $digits = ltrim(preg_replace('/\D/', '', $term), '0');

            $query->where(function ($q) use ($term, $digits) {
                $q->where('record_title', 'like', '%' . $term . '%');

                if ($digits !== '') {
                    $q->orWhere('id', (int) $digits);
                }
            });
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Whether $parent may be the parent of a new case on $form, for $user.
     * Same rules the submit paths enforce, in one place so the picker and the
     * server cannot disagree.
     *
     * @return string|null  null when allowed, else why not
     */
    public function parentRefusal(Form $form, FormSubmission $parent, User $user): ?string
    {
        if ((int) $parent->form_id !== (int) $form->id) {
            return 'A case can only follow up on another case of the same form.';
        }

        if (!$parent->canBeParent()) {
            return 'That case is already a follow-up. Attach this one to the original case instead.';
        }

        if (!$this->canView($parent, $user)) {
            return 'You do not have access to that case.';
        }

        return null;
    }

    /**
     * List row for the Records screens: title, reference, status and the stage
     * the case is waiting on.
     */
    public function summarize(FormSubmission $case): array
    {
        // A cancelled or rejected case leaves its approval rows pending, so
        // currentStage() keeps naming a stage nobody owes anything on. A case
        // is only waiting on someone while it is still live.
        $stage = $case->status === 'pending' && $case->isRecordOpen()
            ? $case->currentStage()
            : null;

        return [
            'root'        => $case,
            'title'       => $this->titleFor($case),
            'reference'   => $case->recordReference(),
            'latest'      => $case,
            // A closed case reads as Closed whatever its outcome was: the
            // admin's decision about the case outranks the entry outcome.
            'status'      => $case->isRecordOpen() ? $case->status : 'closed',
            'entry_status' => $case->status,
            'stage_name'  => optional($stage)->name,
            'waiting_on'  => $stage
                             ? User::whereIn('id', $this->approvals->waitingOn($case))->pluck('name')->all()
                             : [],
            'updated_at'  => $case->updated_at ?? $case->created_at,
        ];
    }
}
