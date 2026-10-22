<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Portal & Sandbox fields
            $table->string('access_token', 64)->nullable()->unique()->after('trainee_email');
            $table->timestamp('started_at')->nullable()->after('completed_at');
            $table->timestamp('submitted_at')->nullable()->after('started_at');
            $table->string('submission_type', 32)->nullable()->default('code')->after('submitted_at');
            $table->longText('submitted_code')->nullable()->after('submission_type');
            $table->string('github_repo_url')->nullable()->after('submitted_code');
            $table->longText('draft_code')->nullable()->after('github_repo_url');

            // Proctoring & Anti-Cheat Telemetry
            $table->unsignedInteger('tab_switch_count')->default(0)->after('draft_code');
            $table->unsignedInteger('paste_count')->default(0)->after('tab_switch_count');
            $table->json('proctoring_log')->nullable()->after('paste_count');

            // AI Code Review & Rubric Scoring
            $table->string('ai_review_status', 32)->default('Pending')->after('proctoring_log');
            $table->json('ai_rubric_scores')->nullable()->after('ai_review_status');
            $table->json('ai_security_flags')->nullable()->after('ai_rubric_scores');
            $table->longText('ai_code_feedback')->nullable()->after('ai_security_flags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'access_token',
                'started_at',
                'submitted_at',
                'submission_type',
                'submitted_code',
                'github_repo_url',
                'draft_code',
                'tab_switch_count',
                'paste_count',
                'proctoring_log',
                'ai_review_status',
                'ai_rubric_scores',
                'ai_security_flags',
                'ai_code_feedback',
            ]);
        });
    }
};
