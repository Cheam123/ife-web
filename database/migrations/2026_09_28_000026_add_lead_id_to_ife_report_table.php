<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a visit (IFE report) to the outlet it was made at, so an outlet's
     * visit history can be read back. Reports already attached to a task take
     * that task's lead.
     */
    public function up()
    {
        Schema::table('ife_report', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('task_id')->constrained('leads');
        });

        DB::table('ife_report')
            ->whereNull('lead_id')
            ->whereNotNull('task_id')
            ->orderBy('id')
            ->each(function ($report) {
                $leadId = DB::table('tasks')->where('id', $report->task_id)->value('lead_id');
                if ($leadId) {
                    DB::table('ife_report')->where('id', $report->id)->update(['lead_id' => $leadId]);
                }
            });
    }

    public function down()
    {
        Schema::table('ife_report', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
        });
    }
};
