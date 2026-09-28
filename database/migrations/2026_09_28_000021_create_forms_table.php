<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Form definitions built in the form builder.
     *
     * form_elements      — the field schema (FormSchemaService)
     * settings           — {"access": {"submit_scope", "user_ids", "user_types"}}
     * process_definition — the approval / handler flow (FormProcessService)
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            // Deleting a group never deletes forms: they fall back into the ungrouped bucket.
            $table->foreignId('form_group_id')->nullable()->constrained('form_groups')->nullOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('form_elements');
            $table->json('settings')->nullable();
            $table->json('process_definition')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            $table->index(['form_group_id', 'position']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forms');
    }
};
