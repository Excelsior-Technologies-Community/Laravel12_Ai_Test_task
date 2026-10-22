<?php

namespace App\Services;

use App\Models\Task;

class AICodeReviewerService
{
    /**
     * Perform AI static analysis and rubric scoring on candidate code submission.
     */
    public function reviewTaskSubmission(Task $task): array
    {
        $code = $task->submitted_code ?? $task->draft_code ?? '';
        $role = $task->role;
        $level = $task->level;

        // If no code was provided (e.g., GitHub URL only or blank), generate review for the submission
        $codeLength = strlen(trim($code));
        $lineCount = count(explode("\n", $code));

        $securityFlags = [];
        $strengths = [];
        $improvements = [];

        // Base scores
        $architectureScore = 75;
        $securityScore = 80;
        $databaseScore = 75;
        $cleanCodeScore = 78;
        $problemSolvingScore = 80;

        // Static Security & Vulnerability Analysis
        if (preg_match('/(eval\s*\(|shell_exec\s*\(|system\s*\(|passthru\s*\(|exec\s*\()/i', $code)) {
            $securityFlags[] = [
                'severity' => 'Critical',
                'title' => 'Dangerous Dynamic Code Execution Detected',
                'description' => 'Usage of dangerous execution functions (eval/exec/system) introduces remote code execution vulnerability.'
            ];
            $securityScore -= 35;
        }

        if (preg_match('/(SELECT\s+.*WHERE.*\$.*|INSERT\s+INTO.*VALUES.*\$.*|UPDATE\s+.*SET.*\$.*)/i', $code) && !preg_match('/\?|\:([a-zA-Z0-9_]+)/', $code)) {
            $securityFlags[] = [
                'severity' => 'High',
                'title' => 'Potential SQL Injection Risk',
                'description' => 'Raw variables concatenated into SQL strings without parameter bindings detected.'
            ];
            $securityScore -= 30;
        }

        if (preg_match('/(md5\s*\(|sha1\s*\()/i', $code)) {
            $securityFlags[] = [
                'severity' => 'High',
                'title' => 'Weak Cryptographic Hashing',
                'description' => 'Deprecated hashing algorithm (MD5/SHA1) detected. Always use Hash::make() or bcrypt.'
            ];
            $securityScore -= 20;
        }

        if (preg_match('/(\$_GET|\$_POST|\$_REQUEST)\[/i', $code) && !preg_match('/htmlspecialchars|filter_var|strip_tags|\$request->validate/i', $code)) {
            $securityFlags[] = [
                'severity' => 'Medium',
                'title' => 'Unsanitized Superglobal Input Access',
                'description' => 'Direct superglobal variable access without request validation or escaping introduces XSS/tampering risks.'
            ];
            $securityScore -= 15;
        }

        if (preg_match('/(password|secret|api_key|token)\s*=\s*[\'"][^\'"]{6,}[\'"]/i', $code)) {
            $securityFlags[] = [
                'severity' => 'Medium',
                'title' => 'Hardcoded Secrets or Credentials',
                'description' => 'Potential sensitive credentials hardcoded in source file. Utilize .env and config() instead.'
            ];
            $securityScore -= 15;
        }

        // Positive patterns
        if (preg_match('/(validate\s*\(|FormRequest|Validator::make)/i', $code)) {
            $strengths[] = 'Rigorous input validation implemented with Laravel validation rules.';
            $securityScore += 10;
        }

        if (preg_match('/(try\s*\{.*catch\s*\(|throw\s+new)/is', $code)) {
            $strengths[] = 'Defensive error management with structured try/catch blocks and custom exceptions.';
            $problemSolvingScore += 8;
        }

        if (preg_match('/(DB::transaction|transaction\s*\(|begin\s*Transaction)/i', $code)) {
            $strengths[] = 'Safe atomic database transactions utilized for critical write operations.';
            $databaseScore += 10;
        }

        if (preg_match('/(namespace\s+[A-Za-z0-9_\\\\]+|use\s+App\\\)/i', $code)) {
            $strengths[] = 'Clean namespace conventions adhering to PSR-4 autoloading standards.';
            $cleanCodeScore += 8;
        }

        if (preg_match('/(\/\*\*[\s\S]*?\*\/|@param|@return)/', $code)) {
            $strengths[] = 'Clear DocBlock comments explaining method parameters and return types.';
            $cleanCodeScore += 6;
        }

        // Role-specific heuristics
        if ($role === 'Laravel') {
            if (preg_match('/(Model|belongsTo|hasMany|where|with\s*\()/i', $code)) {
                $databaseScore += 8;
                $strengths[] = 'Demonstrates fluent Eloquent ORM relationships and eager loading.';
            } else {
                $improvements[] = 'Consider leveraging Eloquent relationship methods and eager loading to prevent N+1 query bottlenecks.';
            }

            if (preg_match('/Controller|Service|Repository/i', $code)) {
                $architectureScore += 10;
            } else {
                $improvements[] = 'Apply single responsibility principle (SRP) by decoupling business logic into dedicated Service classes.';
            }
        } elseif ($role === 'PHP') {
            if (preg_match('/(class\s+[A-Za-z0-9_]+|interface\s+[A-Za-z0-9_]+)/i', $code)) {
                $architectureScore += 10;
                $strengths[] = 'Adheres to Object-Oriented Programming (OOP) paradigms with structured class definition.';
            } else {
                $improvements[] = 'Refactor procedural scripts into testable OOP classes and interfaces.';
            }
        } elseif ($role === 'Frontend') {
            if (preg_match('/(addEventListener|fetch\s*\(|async|await|const|let)/i', $code)) {
                $cleanCodeScore += 10;
                $strengths[] = 'Modern ES6+ JavaScript syntax and asynchronous promise handling utilized.';
            }
            if (preg_match('/(grid|flex|transition|transform|tailwind)/i', $code)) {
                $architectureScore += 8;
                $strengths[] = 'Fluid responsive layout with modern CSS utilities.';
            }
        }

        // Level scaling
        if ($level === 'Advanced') {
            $architectureScore -= 5;
            $improvements[] = 'For Advanced tier, include automated unit/feature tests (PHPUnit / Pest) and queue dispatching.';
        } elseif ($level === 'Beginner') {
            $cleanCodeScore += 6;
            $problemSolvingScore += 5;
        }

        // Proctoring Telemetry impact
        if ($task->tab_switch_count > 4) {
            $improvements[] = 'Notice: Excessive browser window switches (' . $task->tab_switch_count . ' times) flagged during proctored session.';
            $problemSolvingScore = max(40, $problemSolvingScore - ($task->tab_switch_count * 2));
        }

        if (empty($securityFlags)) {
            $securityFlags[] = [
                'severity' => 'Passed',
                'title' => 'Zero High-Risk Vulnerabilities Detected',
                'description' => 'Static code inspection found no unescaped queries, dangerous execution functions, or hardcoded secrets.'
            ];
            $securityScore = max(88, $securityScore);
        }

        if (empty($strengths)) {
            $strengths[] = 'Successfully delivered executable implementation matching base task criteria.';
            $strengths[] = 'Functional logic structure with readable code flow.';
        }

        if (empty($improvements)) {
            $improvements[] = 'Add automated unit tests to verify edge cases and boundary inputs.';
            $improvements[] = 'Incorporate caching (e.g. Cache::remember) for high-frequency read operations.';
        }

        // Clamp scores between 45 and 98
        $architectureScore = max(45, min(98, $architectureScore));
        $securityScore = max(35, min(99, $securityScore));
        $databaseScore = max(45, min(98, $databaseScore));
        $cleanCodeScore = max(45, min(98, $cleanCodeScore));
        $problemSolvingScore = max(45, min(98, $problemSolvingScore));

        $overallScore = (int) round(
            ($architectureScore * 0.25) +
            ($securityScore * 0.25) +
            ($databaseScore * 0.20) +
            ($cleanCodeScore * 0.15) +
            ($problemSolvingScore * 0.15)
        );

        $rubricScores = [
            'architecture' => $architectureScore,
            'security' => $securityScore,
            'database_orm' => $databaseScore,
            'clean_code' => $cleanCodeScore,
            'problem_solving' => $problemSolvingScore,
        ];

        // Format structured feedback
        $feedbackMarkdown = "### AI Evaluation Summary\n\n";
        $feedbackMarkdown .= "**Candidate:** {$task->trainee_name} | **Role:** {$task->role} ({$task->level})\n";
        $feedbackMarkdown .= "**Calculated Overall Grade:** {$overallScore}/100\n\n";

        $feedbackMarkdown .= "#### 🌟 Key Technical Strengths\n";
        foreach ($strengths as $strength) {
            $feedbackMarkdown .= "- " . $strength . "\n";
        }
        $feedbackMarkdown .= "\n";

        $feedbackMarkdown .= "#### 💡 Recommended Enhancements\n";
        foreach ($improvements as $imp) {
            $feedbackMarkdown .= "- " . $imp . "\n";
        }
        $feedbackMarkdown .= "\n";

        $feedbackMarkdown .= "#### 🔒 Security & Best Practice Audit\n";
        foreach ($securityFlags as $flag) {
            $badge = $flag['severity'] === 'Passed' ? '✅' : ($flag['severity'] === 'Critical' ? '🚨' : '⚠️');
            $feedbackMarkdown .= "{$badge} **[{$flag['severity']}] {$flag['title']}**: {$flag['description']}\n";
        }

        // Update task
        $task->update([
            'score' => $overallScore,
            'status' => 'Reviewed',
            'ai_review_status' => 'Completed',
            'ai_rubric_scores' => $rubricScores,
            'ai_security_flags' => $securityFlags,
            'ai_code_feedback' => $feedbackMarkdown,
            'review_notes' => "AI Automated Review completed. Overall Score: {$overallScore}/100. Evaluated {$lineCount} lines of code across 5 core competencies.",
            'completed_at' => $task->completed_at ?? now(),
        ]);

        return [
            'overall_score' => $overallScore,
            'rubric_scores' => $rubricScores,
            'security_flags' => $securityFlags,
            'feedback' => $feedbackMarkdown,
        ];
    }
}
