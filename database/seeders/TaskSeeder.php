<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fully Reviewed Task with High Score & Clean Anti-Cheat Record
        Task::create([
            'trainee_name' => 'Alex Rivera',
            'trainee_email' => 'alex.rivera@example.com',
            'access_token' => Str::random(40),
            'title' => 'Build REST API with JWT Authentication and Role Middleware',
            'description' => 'Architect a robust API authentication system utilizing JWT tokens, role-based permission gates, and secure refresh token rotation.',
            'role' => 'Laravel',
            'level' => 'Advanced',
            'duration_hours' => 8,
            'test_instructions' => "1. Implement AuthController with register, login, refresh, and logout endpoints.\n2. Add RoleMiddleware checking for admin vs member roles.\n3. Implement unit tests for token expiration handling.",
            'status' => 'Reviewed',
            'score' => 92,
            'review_notes' => 'Exceptional code structure adhering to PSR-12. Flawless parameter validation and clean token refresh implementation.',
            'started_at' => now()->subHours(5),
            'completed_at' => now()->subHours(2),
            'submitted_at' => now()->subHours(2),
            'submission_type' => 'both',
            'submitted_code' => "<?php\n\nnamespace App\\Http\\Controllers;\n\nuse App\\Models\\User;\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Hash;\nuse Illuminate\\Support\\Facades\\DB;\n\nclass AuthController extends Controller\n{\n    public function login(Request \$request)\n    {\n        \$validated = \$request->validate([\n            'email' => 'required|email',\n            'password' => 'required|string|min:8'\n        ]);\n\n        \$user = User::where('email', \$validated['email'])->first();\n        if (!\$user || !Hash::check(\$validated['password'], \$user->password)) {\n            return response()->json(['message' => 'Invalid credentials'], 401);\n        }\n\n        \$token = \$user->createToken('api-token')->plainTextToken;\n        return response()->json(['token' => \$token, 'user' => \$user]);\n    }\n}",
            'github_repo_url' => 'https://github.com/alexrivera/laravel-jwt-api',
            'draft_code' => null,
            'tab_switch_count' => 1,
            'paste_count' => 1,
            'proctoring_log' => [
                [
                    'event' => 'TAB_SWITCH',
                    'details' => 'Brief tab blur detected (duration: 3s)',
                    'timestamp' => now()->subHours(4)->toIso8601String(),
                    'formatted_time' => now()->subHours(4)->format('d M Y, h:i:s A'),
                ],
                [
                    'event' => 'CLIPBOARD_PASTE',
                    'details' => 'Pasted code block of 42 characters',
                    'timestamp' => now()->subHours(3)->toIso8601String(),
                    'formatted_time' => now()->subHours(3)->format('d M Y, h:i:s A'),
                ]
            ],
            'ai_review_status' => 'Completed',
            'ai_rubric_scores' => [
                'architecture' => 94,
                'security' => 95,
                'database_orm' => 90,
                'clean_code' => 92,
                'problem_solving' => 88,
            ],
            'ai_security_flags' => [
                [
                    'severity' => 'Passed',
                    'title' => 'Zero High-Risk Vulnerabilities Detected',
                    'description' => 'Static code inspection found no unescaped queries, dangerous execution functions, or hardcoded secrets.'
                ]
            ],
            'ai_code_feedback' => "### AI Evaluation Summary\n\n**Candidate:** Alex Rivera | **Role:** Laravel (Advanced)\n**Calculated Overall Grade:** 92/100\n\n#### 🌟 Key Technical Strengths\n- Rigorous input validation implemented with Laravel validation rules.\n- Demonstrates fluent Eloquent ORM queries and safe password hashing.\n- Clean namespace conventions adhering to PSR-4 autoloading standards.\n\n#### 💡 Recommended Enhancements\n- Consider incorporating Redis cache layer for JWT revocation blacklists.\n- Add automated Pest/PHPUnit integration test suite.",
        ]);

        // 2. In Progress Task with Live Proctored Countdown & Telemetry
        Task::create([
            'trainee_name' => 'Sophia Chen',
            'trainee_email' => 'sophia.chen@example.com',
            'access_token' => Str::random(40),
            'title' => 'Create Real-time Chat Component with WebSockets and Vue.js',
            'description' => 'Build an interactive real-time messaging chat window featuring message history, online typing status, and responsive UI.',
            'role' => 'Frontend',
            'level' => 'Intermediate',
            'duration_hours' => 4,
            'test_instructions' => 'Create a reactive chat box with message send/receive state, auto-scrolling to bottom, and connection status indicator.',
            'status' => 'In Progress',
            'score' => null,
            'review_notes' => null,
            'started_at' => now()->subMinutes(45),
            'completed_at' => null,
            'submitted_at' => null,
            'submission_type' => 'code',
            'submitted_code' => null,
            'draft_code' => "const chatState = {\n    messages: [],\n    typingUser: null,\n    isConnected: true\n};\n\nfunction sendMessage(text) {\n    if (!text.trim()) return;\n    chatState.messages.push({\n        id: Date.now(),\n        text: text,\n        timestamp: new Date().toLocaleTimeString()\n    });\n}",
            'github_repo_url' => null,
            'tab_switch_count' => 2,
            'paste_count' => 1,
            'proctoring_log' => [
                [
                    'event' => 'TAB_SWITCH',
                    'details' => 'Candidate switched browser tab',
                    'timestamp' => now()->subMinutes(30)->toIso8601String(),
                    'formatted_time' => now()->subMinutes(30)->format('d M Y, h:i:s A'),
                ],
                [
                    'event' => 'WINDOW_BLUR',
                    'details' => 'Window lost focus for 12 seconds',
                    'timestamp' => now()->subMinutes(15)->toIso8601String(),
                    'formatted_time' => now()->subMinutes(15)->format('d M Y, h:i:s A'),
                ]
            ],
            'ai_review_status' => 'Pending',
            'ai_rubric_scores' => null,
            'ai_security_flags' => null,
            'ai_code_feedback' => null,
        ]);

        // 3. Completed Task Flagged for Excessive Tab Switches & Evaluated by AI
        Task::create([
            'trainee_name' => 'Marcus Vance',
            'trainee_email' => 'marcus.vance@example.com',
            'access_token' => Str::random(40),
            'title' => 'Implement CSV Data Import with Queue Processing',
            'description' => 'Create a streaming CSV processor that parses large files in chunks and dispatches jobs to Redis queue.',
            'role' => 'PHP',
            'level' => 'Intermediate',
            'duration_hours' => 4,
            'test_instructions' => 'Handle memory limits gracefully by processing CSV rows line-by-line using fgetcsv and generator functions.',
            'status' => 'Reviewed',
            'score' => 74,
            'review_notes' => 'Functional script but proctoring logged multiple tab switches (5 times). AI review noted lack of try/catch blocks around file handle.',
            'started_at' => now()->subHours(6),
            'completed_at' => now()->subHours(3),
            'submitted_at' => now()->subHours(3),
            'submission_type' => 'code',
            'submitted_code' => "<?php\n\nfunction processCsvFile(\$filePath) {\n    \$handle = fopen(\$filePath, 'r');\n    while ((\$data = fgetcsv(\$handle, 1000, ',')) !== FALSE) {\n        // Process row\n        echo \$data[0] . \"\\n\";\n    }\n    fclose(\$handle);\n}",
            'github_repo_url' => null,
            'draft_code' => null,
            'tab_switch_count' => 6,
            'paste_count' => 3,
            'proctoring_log' => [
                [
                    'event' => 'TAB_SWITCH',
                    'details' => 'Candidate left test tab (Switch #1)',
                    'timestamp' => now()->subHours(5)->toIso8601String(),
                    'formatted_time' => now()->subHours(5)->format('d M Y, h:i:s A'),
                ],
                [
                    'event' => 'CLIPBOARD_PASTE',
                    'details' => 'Pasted code block of 150 characters from external source',
                    'timestamp' => now()->subHours(4)->toIso8601String(),
                    'formatted_time' => now()->subHours(4)->format('d M Y, h:i:s A'),
                ],
                [
                    'event' => 'TAB_SWITCH',
                    'details' => 'Candidate left test tab (Switch #2)',
                    'timestamp' => now()->subHours(4)->toIso8601String(),
                    'formatted_time' => now()->subHours(4)->format('d M Y, h:i:s A'),
                ],
                [
                    'event' => 'TAB_SWITCH',
                    'details' => 'Candidate left test tab (Switch #3)',
                    'timestamp' => now()->subHours(3)->toIso8601String(),
                    'formatted_time' => now()->subHours(3)->format('d M Y, h:i:s A'),
                ]
            ],
            'ai_review_status' => 'Completed',
            'ai_rubric_scores' => [
                'architecture' => 72,
                'security' => 78,
                'database_orm' => 70,
                'clean_code' => 75,
                'problem_solving' => 62, // Penalized for 6 tab switches
            ],
            'ai_security_flags' => [
                [
                    'severity' => 'Medium',
                    'title' => 'Missing File Handle Verification',
                    'description' => 'fopen() call lacks defensive validation against unreadable file path or permissions error.'
                ]
            ],
            'ai_code_feedback' => "### AI Evaluation Summary\n\n**Candidate:** Marcus Vance | **Role:** PHP (Intermediate)\n**Calculated Overall Grade:** 74/100\n\n#### 🌟 Key Technical Strengths\n- Streamed CSV parsing with memory-efficient fgetcsv.\n\n#### 💡 Recommended Enhancements\n- Notice: Excessive browser window switches (6 times) flagged during proctored session.\n- Wrap I/O operations in structured try/catch/finally blocks to guarantee file handle closure.",
        ]);
    }
}
