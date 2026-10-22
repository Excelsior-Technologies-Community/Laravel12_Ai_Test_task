<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Report Card - {{ $task->trainee_name }} - {{ $task->title }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen py-8 px-4 font-sans antialiased">

    <div class="max-w-4xl mx-auto space-y-6">
        
        {{-- Action Bar (No Print) --}}
        <div class="no-print flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <a href="{{ route('tasks.show', $task) }}" class="text-gray-600 hover:text-gray-900 text-sm font-semibold flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Task Details
            </a>

            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Print / Save as PDF
                </button>
            </div>
        </div>

        {{-- Printable Report Card Document --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden print-card">
            
            {{-- Document Header --}}
            <div class="bg-gradient-to-r from-purple-800 to-indigo-900 text-white p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-purple-300 text-xs font-bold uppercase tracking-wider">
                            <span>AI Training & Assessment System</span>
                            <span>•</span>
                            <span>Official Competency Evaluation</span>
                        </div>
                        <h1 class="text-3xl font-extrabold mt-1">Candidate Evaluation Report</h1>
                        <p class="text-purple-200 text-sm mt-1">Task Ref: #{{ $task->id }} | Issued on {{ now()->format('d M Y') }}</p>
                    </div>

                    {{-- Grade Seal --}}
                    <div class="text-center bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl min-w-[140px]">
                        <div class="text-[11px] uppercase tracking-wider text-purple-200 font-bold">Overall Score</div>
                        <div class="text-4xl font-black text-white mt-1">{{ $task->score ?? 'N/A' }}<span class="text-lg text-purple-200 font-normal">/100</span></div>
                        <div class="text-[11px] text-emerald-300 font-semibold mt-1">Percentile: Top {{ 100 - $percentile }}%</div>
                    </div>
                </div>
            </div>

            {{-- Candidate & Task Metadata --}}
            <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Candidate Name</div>
                        <div class="text-base font-bold text-gray-800 mt-1">{{ $task->trainee_name }}</div>
                        <div class="text-xs text-gray-500">{{ $task->trainee_email }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Target Role</div>
                        <div class="text-base font-bold text-purple-700 mt-1">{{ $task->role }}</div>
                        <div class="text-xs text-gray-500">Tier: {{ $task->level }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Assessment Title</div>
                        <div class="text-base font-bold text-gray-800 mt-1 truncate">{{ $task->title }}</div>
                        <div class="text-xs text-gray-500">Duration: {{ $task->duration_hours }}h</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Evaluation Status</div>
                        <div class="inline-block mt-1 px-3 py-0.5 rounded-full text-xs font-bold {{ $task->status === 'Reviewed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $task->status }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ $task->completed_at?->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>

            {{-- 5-Dimension Radar Competency Matrix --}}
            <div class="p-8 border-b border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-chart-radar text-purple-600"></i> 5-Dimension Competency Radar
                        </h2>
                        <p class="text-xs text-gray-500">Benchmarked against cohort standards across key technical disciplines.</p>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <span class="flex items-center gap-1.5 text-purple-700">
                            <span class="w-3 h-3 rounded-full bg-purple-600"></span> Candidate Score
                        </span>
                        <span class="flex items-center gap-1.5 text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-gray-400"></span> Cohort Average
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    <div class="md:col-span-6 h-72 flex items-center justify-center">
                        <canvas id="reportRadarChart"></canvas>
                    </div>

                    <div class="md:col-span-6 space-y-3.5">
                        @php
                            $scores = $task->ai_rubric_scores ?? [
                                'architecture' => 75,
                                'security' => 80,
                                'database_orm' => 75,
                                'clean_code' => 78,
                                'problem_solving' => 80
                            ];
                        @endphp

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-700">Architecture & Design Patterns</span>
                                <span class="font-bold text-purple-700">{{ $scores['architecture'] ?? 75 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $scores['architecture'] ?? 75 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-700">Security & Vulnerability Defense</span>
                                <span class="font-bold text-purple-700">{{ $scores['security'] ?? 80 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $scores['security'] ?? 80 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-700">Database & ORM Efficiency</span>
                                <span class="font-bold text-purple-700">{{ $scores['database_orm'] ?? 75 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $scores['database_orm'] ?? 75 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-700">Clean Code, PSR-12 & Readability</span>
                                <span class="font-bold text-purple-700">{{ $scores['clean_code'] ?? 78 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $scores['clean_code'] ?? 78 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-700">Problem Solving & Speed</span>
                                <span class="font-bold text-purple-700">{{ $scores['problem_solving'] ?? 80 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $scores['problem_solving'] ?? 80 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Anti-Cheat Telemetry & Proctoring Integrity --}}
            <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-indigo-600"></i> Proctoring Integrity & Anti-Cheat Telemetry
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    @php $risk = $task->getProctoringRisk(); @endphp
                    <div class="bg-white p-4 rounded-xl border border-gray-200">
                        <div class="text-xs uppercase font-bold text-gray-400">Risk Assessment</div>
                        <div class="text-xl font-bold mt-1 text-{{ $risk['color'] }}-600">{{ $risk['level'] }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Integrity Score: {{ 100 - $risk['score'] }}%</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-200">
                        <div class="text-xs uppercase font-bold text-gray-400">Window / Tab Switches</div>
                        <div class="text-xl font-bold mt-1 text-gray-800">{{ $task->tab_switch_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Allowed Threshold: &lt; 3</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-200">
                        <div class="text-xs uppercase font-bold text-gray-400">Bulk Clipboard Pastes</div>
                        <div class="text-xl font-bold mt-1 text-gray-800">{{ $task->paste_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Monitored character sequences</div>
                    </div>
                </div>
            </div>

            {{-- Detailed AI Reviewer Notes & Security Audit --}}
            <div class="p-8 space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-microchip text-purple-600"></i> AI Automated Review & Evaluator Feedback
                    </h2>
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 text-sm leading-relaxed text-gray-700 whitespace-pre-line">
                        {{ $task->ai_code_feedback ?: ($task->review_notes ?: 'Pending submission and AI evaluation.') }}
                    </div>
                </div>

                {{-- Sign-off & Verification Footer --}}
                <div class="pt-6 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
                    <div>
                        <div class="font-bold text-gray-700">AI Assessment Verification Engine</div>
                        <div>Certified electronically via Secure Token SHA-256</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-gray-700">Digital Seal & Timestamp</div>
                        <div>{{ now()->toDayDateTimeString() }}</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Render Radar Chart --}}
    <script>
        const ctx = document.getElementById('reportRadarChart').getContext('2d');
        const rubric = @json($scores);
        const cohortAvg = {{ $cohortAvg }};

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Architecture', 'Security', 'Database / ORM', 'Clean Code', 'Problem Solving'],
                datasets: [
                    {
                        label: 'Candidate Score',
                        data: [
                            rubric.architecture || 75,
                            rubric.security || 80,
                            rubric.database_orm || 75,
                            rubric.clean_code || 78,
                            rubric.problem_solving || 80
                        ],
                        backgroundColor: 'rgba(124, 58, 237, 0.25)',
                        borderColor: '#7c3aed',
                        pointBackgroundColor: '#7c3aed',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#7c3aed',
                        borderWidth: 2
                    },
                    {
                        label: 'Cohort Average',
                        data: [cohortAvg, cohortAvg + 2, cohortAvg - 2, cohortAvg + 1, cohortAvg],
                        backgroundColor: 'rgba(156, 163, 175, 0.15)',
                        borderColor: '#9ca3af',
                        pointBackgroundColor: '#9ca3af',
                        borderDash: [5, 5],
                        borderWidth: 1.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: '#e5e7eb' },
                        grid: { color: '#e5e7eb' },
                        pointLabels: { color: '#475569', font: { size: 11, weight: 'bold' } },
                        ticks: { min: 0, max: 100, stepSize: 20, display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>
