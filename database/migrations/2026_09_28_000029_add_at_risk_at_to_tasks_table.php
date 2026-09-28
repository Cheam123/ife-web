<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Set by tasks:check-risk when a task is first flagged at risk, so managers
     * are alerted once; cleared again if the task stops being at risk.
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dateTime('at_risk_at')->nullable()->after('due_notify')->index();
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['at_risk_at']);
            $table->dropColumn('at_risk_at');
        });
    }
};
