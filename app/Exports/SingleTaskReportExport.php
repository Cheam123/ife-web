<?php

namespace App\Exports;

use App\Models\TaskComment;
use App\Helpers\Helper;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SingleTaskReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $taskId;

    public function __construct($taskId)
    {
        $this->taskId = $taskId;
    }

    public function query()
    {
        $query = TaskComment::query()
            ->select('task_comment.*','tasks.*','leads.*')
            ->join('tasks', 'task_comment.task_id', '=', 'tasks.id')
            ->join('leads', 'tasks.lead_id', '=', 'leads.id')
            ->with(['task.lead', 'task.category', 'task.sub_category', 'submitBy', 'documentUploads'])
            ->where('tasks.id', $this->taskId);

        $query->where('task_comment.message', 'not like', '%<img%');

        $query->orderBy('task_comment.id', 'asc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'Lead Name',
            'Lead Business Name',
            'Lead Mobile',
            'Business Category',
            'Lead Source',
            'Task Title',
            'Task Invoice No',
            'Task Sales',
            'Category',
            'Sub Category',
            'Status',
            'Task Reference',
            'Submit Date',
            'Commented By',
            'Message',
            'Attachments',
        ];
    }

    public function map($comment): array
    {
        $task = $comment->task;
        $lead = $task->lead;

        $attachments = [];
        foreach ($comment->documentUploads as $doc) {
            $url = isset($doc->fullpath) ? $doc->fullpath : asset('storage/' . $doc->path);
            $attachments[] = $url;
        }
        $attachmentString = implode("\n", $attachments);

        $message = $comment->message;
        $message = str_replace(['<div>', '</div>', '&nbsp;'], ['', '', ' '], $message);
        $message = strip_tags($message);

        return [
            $lead->name ?? '',
            $lead->business_name ?? '',
            $lead->mobile ?? '',
            Helper::getBusinessCategory($lead->business_category ?? ''), 
            Helper::getLeadSource($lead->source ?? ''),
            $task->title ?? '',
            $task->invoice_no ?? '',
            $task->sales ?? '',
            $task->category->name ?? '',
            $task->sub_category->name ?? '',
            Helper::getStatus($task->status),
            $task->task_reference ?? '',
            $comment->submit_date,
            $comment->submitBy->name ?? '',
            $message,
            $attachmentString
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->getStyle('A1:P1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        
        $sheet->getStyle('O')->getAlignment()->setWrapText(true);
        $sheet->getStyle('P')->getAlignment()->setWrapText(true);
        
        $sheet->getStyle('A:P')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    }
}
