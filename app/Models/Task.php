<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_name',
        'trainee_email',
        'access_token',
        'title',
        'description',
        'role',
        'level',
        'duration_hours',
        'test_instructions',
        'status',
        'score',
        'review_notes',
        'completed_at',
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
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'proctoring_log' => 'array',
        'ai_rubric_scores' => 'array',
        'ai_security_flags' => 'array',
    ];

    /**
     * Boot model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->access_token)) {
                $task->access_token = Str::random(40);
            }
        });
    }

    /**
     * Get candidate assessment direct portal URL.
     */
    public function getAssessmentUrlAttribute(): string
    {
        return route('assessment.show', ['token' => $this->access_token]);
    }

    /**
     * Get remaining time in seconds for active proctored session.
     */
    public function getRemainingSeconds(): int
    {
        if (!$this->started_at) {
            return $this->duration_hours * 3600;
        }

        $totalAllowedSeconds = $this->duration_hours * 3600;
        $elapsedSeconds = now()->diffInSeconds($this->started_at);
        $remaining = $totalAllowedSeconds - $elapsedSeconds;

        return max(0, (int) $remaining);
    }

    /**
     * Check if test time has expired.
     */
    public function isExpired(): bool
    {
        if (!$this->started_at) {
            return false;
        }

        return $this->getRemainingSeconds() <= 0;
    }

    /**
     * Evaluate proctoring risk score based on telemetry alerts.
     */
    public function getProctoringRisk(): array
    {
        $switches = $this->tab_switch_count ?? 0;
        $pastes = $this->paste_count ?? 0;

        $riskScore = ($switches * 15) + ($pastes * 10);

        if ($riskScore >= 60 || $switches >= 5) {
            return ['level' => 'High', 'color' => 'red', 'score' => min(100, $riskScore)];
        } elseif ($riskScore >= 25 || $switches >= 2) {
            return ['level' => 'Moderate', 'color' => 'amber', 'score' => $riskScore];
        }

        return ['level' => 'Clean / Low', 'color' => 'green', 'score' => max(5, $riskScore)];
    }
}
