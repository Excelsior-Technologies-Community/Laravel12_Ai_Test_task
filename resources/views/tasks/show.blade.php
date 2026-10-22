@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Success / Status Alert --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
        <span class="text-xl">✅</span>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Header & Action Buttons --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-700">
                    Task #{{ $task->id }}
                </span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $task->status === 'Reviewed' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $task->status }}
                </span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 mt-1">
                {{ $task->title }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Assigned to <strong class="text-gray-800">{{ $task->trainee_name }}</strong> ({{ $task->trainee_email }})
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('tasks.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold px-3.5 py-2 rounded-lg transition">
                ← Back
            </a>

            {{-- Open Candidate Portal Link --}}
            <a href="{{ $task->assessment_url }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-sm">
                💻 Candidate Portal
            </a>

            {{-- 1-Click AI Code Evaluation --}}
            <form method="POST" action="{{ route('tasks.ai_evaluate', $task) }}" class="inline">
                @csrf
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                    ⚡ AI Evaluate Code
                </button>
            </form>

            {{-- Printable Report Card --}}
            <a href="{{ route('tasks.report', $task) }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                📊 Report Card
            </a>

            {{-- Duplicate --}}
            <form method="POST" action="{{ route('tasks.duplicate', $task) }}" class="inline">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-3 py-2 rounded-lg transition">
                    📋 Duplicate
                </button>
            </form>
        </div>
    </div>

    {{-- Assessment & Scoring Overview Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs uppercase font-bold text-gray-400">Overall Score</div>
            <div class="text-3xl font-black mt-1 {{ $task->score !== null ? 'text-green-600' : 'text-gray-400' }}">
                {{ $task->score !== null ? $task->score . '/100' : 'Pending' }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Status: {{ $task->status }}</div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs uppercase font-bold text-gray-400">Duration & Timeline</div>
            <div class="text-2xl font-bold mt-1 text-gray-800">{{ $task->duration_hours }} Hours</div>
            <div class="text-xs text-gray-500 mt-1">
                Started: {{ $task->started_at ? $task->started_at->format('d M, h:i A') : 'Not started' }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            @php $risk = $task->getProctoringRisk(); @endphp
            <div class="text-xs uppercase font-bold text-gray-400">Proctoring Risk</div>
            <div class="text-2xl font-bold mt-1 text-{{ $risk['color'] }}-600 flex items-center gap-1.5">
                <span>{{ $risk['level'] }}</span>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                {{ $task->tab_switch_count ?? 0 }} switches • {{ $task->paste_count ?? 0 }} pastes
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs uppercase font-bold text-gray-400">AI Review Status</div>
            <div class="text-2xl font-bold mt-1 text-purple-600">
                {{ $task->ai_review_status ?? 'Pending' }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Completed: {{ $task->completed_at ? $task->completed_at->format('d M, h:i A') : 'Incomplete' }}
            </div>
        </div>
    </div>

    {{-- 5-Dimension Competency Radar & Security Scanner --}}
    @if($task->ai_rubric_scores)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    🎯 5-Dimension Competency Radar Matrix
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Automated algorithmic analysis benchmarked against cohort statistics.</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            {{-- Spider Radar Chart --}}
            <div class="lg:col-span-6 h-80 flex items-center justify-center">
                <canvas id="taskDetailRadarChart"></canvas>
            </div>

            {{-- Competency Breakdown & Security Scanner Findings --}}
            <div class="lg:col-span-6 space-y-4">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-purple-50 p-3 rounded-xl border border-purple-200">
                        <div class="text-purple-600 font-bold uppercase">Architecture</div>
                        <div class="text-xl font-black text-purple-900 mt-1">{{ $task->ai_rubric_scores['architecture'] ?? 75 }}/100</div>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-200">
                        <div class="text-indigo-600 font-bold uppercase">Security & Defense</div>
                        <div class="text-xl font-black text-indigo-900 mt-1">{{ $task->ai_rubric_scores['security'] ?? 80 }}/100</div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-xl border border-blue-200">
                        <div class="text-blue-600 font-bold uppercase">Database / ORM</div>
                        <div class="text-xl font-black text-blue-900 mt-1">{{ $task->ai_rubric_scores['database_orm'] ?? 75 }}/100</div>
                    </div>
                    <div class="bg-teal-50 p-3 rounded-xl border border-teal-200">
                        <div class="text-teal-600 font-bold uppercase">Clean Code / PSR</div>
                        <div class="text-xl font-black text-teal-900 mt-1">{{ $task->ai_rubric_scores['clean_code'] ?? 78 }}/100</div>
                    </div>
                </div>

                {{-- Security Findings List --}}
                <div class="mt-4">
                    <h3 class="text-xs uppercase font-bold text-gray-500 mb-2">Automated Security Vulnerability Scanner:</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($task->ai_security_flags ?? [] as $flag)
                        <div class="p-3 rounded-xl text-xs {{ $flag['severity'] === 'Passed' ? 'bg-green-50 border border-green-200 text-green-900' : ($flag['severity'] === 'Critical' ? 'bg-red-50 border border-red-200 text-red-900' : 'bg-amber-50 border border-amber-200 text-amber-900') }}">
                            <div class="font-bold flex items-center gap-1.5">
                                <span>{{ $flag['severity'] === 'Passed' ? '✅' : '⚠️' }}</span>
                                <span>[{{ $flag['severity'] }}] {{ $flag['title'] }}</span>
                            </div>
                            <div class="mt-1 opacity-90">{{ $flag['description'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Candidate Solution Code & Submission Inspection --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 pb-3 border-b">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    💻 Trainee Code Submission
                </h2>
                <p class="text-xs text-gray-500">Live code captured from the in-browser assessment sandbox.</p>
            </div>

            @if($task->github_repo_url)
            <a href="{{ $task->github_repo_url }}" target="_blank" class="text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 px-3.5 py-1.5 rounded-lg flex items-center gap-2 transition">
                <span>View GitHub Repository &rarr;</span>
            </a>
            @endif
        </div>

        @if($task->submitted_code || $task->draft_code)
        <div class="bg-slate-900 text-slate-100 p-4 rounded-xl font-mono text-xs overflow-x-auto max-h-96 border border-slate-800">
            <pre><code>{{ $task->submitted_code ?: $task->draft_code }}</code></pre>
        </div>
        @else
        <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300">
            <p class="text-gray-500 text-sm">No code submitted yet. Candidate has access to the test via their portal link:</p>
            <a href="{{ $task->assessment_url }}" target="_blank" class="mt-2 inline-block font-mono text-xs text-purple-600 underline">
                {{ $task->assessment_url }}
            </a>
        </div>
        @endif
    </div>

    {{-- Anti-Cheat Proctoring Telemetry Audit Log --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    🛡️ Proctoring & Anti-Cheat Audit Trail
                </h2>
                <p class="text-xs text-gray-500">Chronological telemetry captured during candidate's testing session.</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full {{ $task->tab_switch_count > 3 ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                {{ $task->tab_switch_count > 3 ? 'Flagged for Review' : 'Clean Session' }}
            </span>
        </div>

        @if(!empty($task->proctoring_log))
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b">
                        <th class="p-3 font-semibold">Timestamp</th>
                        <th class="p-3 font-semibold">Event Type</th>
                        <th class="p-3 font-semibold">Telemetry Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($task->proctoring_log as $entry)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-3 font-mono text-gray-500">{{ $entry['formatted_time'] ?? $entry['timestamp'] }}</td>
                        <td class="p-3 font-bold {{ $entry['event'] === 'TAB_SWITCH' ? 'text-amber-600' : ($entry['event'] === 'CLIPBOARD_PASTE' ? 'text-blue-600' : 'text-gray-700') }}">
                            {{ $entry['event'] }}
                        </td>
                        <td class="p-3 text-gray-700">{{ $entry['details'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-xs text-gray-500 py-3">No suspicious telemetry flags logged. Candidate remained inside test session.</p>
        @endif
    </div>

    {{-- Manual Instructor Evaluation Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b">
            ✍️ Instructor Evaluation & Manual Override
        </h2>

        <form method="POST" action="{{ route('tasks.evaluation.update', $task) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm">
                        <option value="Pending" {{ $task->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Reviewed" {{ $task->status === 'Reviewed' ? 'selected' : '' }}>Reviewed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Score (0-100)</label>
                    <input type="number" min="0" max="100" name="score" value="{{ $task->score }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm" placeholder="e.g. 85">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Evaluator Notes / Review</label>
                <textarea name="review_notes" rows="4" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm" placeholder="Enter evaluation comments...">{{ $task->review_notes }}</textarea>
            </div>

            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-6 py-2.5 rounded-lg text-sm transition">
                Save Evaluation
            </button>
        </form>
    </div>

</div>

{{-- Render Radar Chart on Task Show --}}
@if($task->ai_rubric_scores)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxDetail = document.getElementById('taskDetailRadarChart').getContext('2d');
    const rubric = @json($task->ai_rubric_scores);
    const cohort = @json($cohortAverages);

    new Chart(ctxDetail, {
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
                    backgroundColor: 'rgba(147, 51, 234, 0.25)',
                    borderColor: '#9333ea',
                    pointBackgroundColor: '#9333ea',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#9333ea',
                    borderWidth: 2
                },
                {
                    label: 'Cohort Average',
                    data: [
                        cohort.architecture || 72,
                        cohort.security || 75,
                        cohort.database_orm || 72,
                        cohort.clean_code || 76,
                        cohort.problem_solving || 74
                    ],
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
                    pointLabels: { color: '#374151', font: { size: 11, weight: 'bold' } },
                    ticks: { min: 0, max: 100, stepSize: 20, display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endif

@endsection
