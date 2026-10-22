@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">
                📊 AI Assessment & Training Dashboard
            </h1>
            <p class="text-gray-500 mt-1">
                Monitor live proctoring telemetry, AI code evaluations, and 5-dimension skill radar analytics.
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('tasks.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Task History
            </a>
            <a href="{{ route('tasks.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                + Generate Task
            </a>
        </div>
    </div>

    {{-- Top Metrics Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-bold uppercase text-gray-400">Total Tasks</p>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $totalTasks }}</p>
            <p class="text-[11px] text-gray-500 mt-1">All generated tasks</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-bold uppercase text-amber-500">In Progress</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $inProgressTasks }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Currently taking exam</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-bold uppercase text-blue-500">Completed</p>
            <p class="text-2xl font-black text-blue-600 mt-1">{{ $completedTasks }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Ready for review</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-bold uppercase text-emerald-500">Reviewed</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $reviewedTasks }}</p>
            <p class="text-[11px] text-gray-500 mt-1">AI & manual scored</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-bold uppercase text-purple-500">Avg Score</p>
            <p class="text-2xl font-black text-purple-600 mt-1">{{ $averageScore }}</p>
            <p class="text-[11px] text-gray-500 mt-1">Out of 100 points</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-bold uppercase text-indigo-500">Completion</p>
            <p class="text-2xl font-black text-indigo-600 mt-1">{{ $completionRate }}%</p>
            <p class="text-[11px] text-gray-500 mt-1">Pass-through rate</p>
        </div>
    </div>

    {{-- Proctoring & Anti-Cheat Telemetry Highlights --}}
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl p-6 shadow-md border border-slate-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
            <div>
                <span class="text-xs uppercase tracking-wider font-bold text-teal-400 bg-teal-500/20 px-2.5 py-0.5 rounded-full border border-teal-500/30">
                    Proctoring Telemetry Radar
                </span>
                <h2 class="text-xl font-bold text-white mt-1">Candidate Integrity & Anti-Cheat Radar</h2>
            </div>
            <div class="text-xs text-slate-400">
                Continuous browser focus and clipboard telemetry monitoring
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
            <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700/60">
                <div class="text-xs uppercase font-bold text-slate-400">Total Window/Tab Switches</div>
                <div class="text-3xl font-black text-amber-400 mt-1">{{ $totalTabSwitches }}</div>
                <div class="text-xs text-slate-400 mt-1">Recorded via browser blur events</div>
            </div>

            <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700/60">
                <div class="text-xs uppercase font-bold text-slate-400">Bulk Paste Interceptions</div>
                <div class="text-3xl font-black text-cyan-400 mt-1">{{ $totalPasteEvents }}</div>
                <div class="text-xs text-slate-400 mt-1">Blocks exceeding 30 characters</div>
            </div>

            <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700/60">
                <div class="text-xs uppercase font-bold text-slate-400">Flagged Sessions</div>
                <div class="text-3xl font-black text-red-400 mt-1">{{ $flaggedCandidatesCount }}</div>
                <div class="text-xs text-slate-400 mt-1">Candidates with high risk score</div>
            </div>
        </div>
    </div>

    {{-- Competency Radar & Role Distribution Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Radar Chart (7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        🎯 Cohort Skill Radar Benchmark
                    </h2>
                    <p class="text-xs text-gray-500">Average competency distribution across 5 core developer pillars.</p>
                </div>
            </div>

            <div class="h-80 flex items-center justify-center">
                <canvas id="dashboardRadarChart"></canvas>
            </div>
        </div>

        {{-- Role & Level Breakdown (5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">
            {{-- Roles --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-gray-900 mb-3">Tasks by Role</h3>
                <div class="space-y-3">
                    @forelse($roleStats as $stat)
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span>{{ $stat->role }}</span>
                            <span class="text-gray-500">{{ $stat->total }} tasks</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $totalTasks > 0 ? ($stat->total / $totalTasks) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400">No role data available.</p>
                    @endforelse
                </div>
            </div>

            {{-- Levels --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-gray-900 mb-3">Tasks by Difficulty Level</h3>
                <div class="grid grid-cols-3 gap-2 text-center">
                    @foreach($levelStats as $lvl)
                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <div class="text-[11px] font-bold uppercase text-gray-500">{{ $lvl->level }}</div>
                        <div class="text-xl font-bold text-purple-700 mt-1">{{ $lvl->total }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- Top Performing Trainees & Recent Reviewed Assessments --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Top Trainees Leaderboard (5 Cols) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b">
                🏆 Top Performing Candidates
            </h3>

            <div class="space-y-3">
                @forelse($topTrainees as $index => $trainee)
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 hover:bg-purple-50/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs">
                            #{{ $index + 1 }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-800">{{ $trainee->trainee_name }}</div>
                            <div class="text-xs text-gray-400">{{ $trainee->reviewed_tasks }} reviewed tasks</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-base font-black text-emerald-600">{{ round($trainee->average_score, 1) }}</div>
                        <div class="text-[10px] text-gray-400 uppercase">Avg Score</div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400">No candidates scored yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Reviewed Tasks (7 Cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <h3 class="text-base font-bold text-gray-900">
                    ⚡ Recent Assessments & Reports
                </h3>
                <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-purple-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentReviewedTasks as $item)
                <div class="py-3 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-800 truncate">{{ $item->title }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-semibold">
                                {{ $item->role }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $item->trainee_name }} • Completed {{ $item->completed_at?->diffForHumans() ?? 'recently' }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($item->score !== null)
                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            {{ $item->score }}/100
                        </span>
                        @endif
                        <a href="{{ route('tasks.report', $item) }}" target="_blank" class="text-xs font-semibold text-purple-600 hover:text-purple-800 bg-purple-50 hover:bg-purple-100 px-3 py-1.5 rounded-lg transition">
                            Report
                        </a>
                        <a href="{{ route('tasks.show', $item) }}" class="text-xs font-semibold text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition">
                            View
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-3">No completed tasks yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

{{-- Chart.js for Cohort Radar --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxRadar = document.getElementById('dashboardRadarChart').getContext('2d');
    const radarData = @json($radarCohort);

    new Chart(ctxRadar, {
        type: 'radar',
        data: {
            labels: ['Architecture', 'Security', 'Database / ORM', 'Clean Code', 'Problem Solving'],
            datasets: [{
                label: 'Cohort Average Score',
                data: [
                    radarData.architecture || 74,
                    radarData.security || 78,
                    radarData.database_orm || 72,
                    radarData.clean_code || 79,
                    radarData.problem_solving || 76
                ],
                backgroundColor: 'rgba(124, 58, 237, 0.25)',
                borderColor: '#7c3aed',
                pointBackgroundColor: '#7c3aed',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#7c3aed',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: '#e5e7eb' },
                    grid: { color: '#e5e7eb' },
                    pointLabels: { color: '#374151', font: { size: 12, weight: 'bold' } },
                    ticks: { min: 0, max: 100, stepSize: 20, display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

@endsection
