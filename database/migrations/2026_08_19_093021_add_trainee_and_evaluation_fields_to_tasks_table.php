<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('trainee_name')->after('id');
            $table->string('trainee_email')->after('trainee_name');

            $table->string('status')
                ->default('Pending')
                ->after('test_instructions');

            $table->unsignedTinyInteger('score')
                ->nullable()
                ->after('status');

            $table->text('review_notes')
                ->nullable()
                ->after('score');

            $table->timestamp('completed_at')
                ->nullable()
                ->after('review_notes');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'trainee_name',
                'trainee_email',
                'status',
                'score',
                'review_notes',
                'completed_at',
            ]);
        });
    }
};