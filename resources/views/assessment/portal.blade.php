<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $task->title }} - Candidate Assessment Portal</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Code Editor (Ace Editor CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.7/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.7/ext-language_tools.js"></script>
    
    <!-- Chart.js for Candidate Radar preview -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        #codeEditor {
            height: 480px;
            width: 100%;
            border-radius: 0.5rem;
            font-size: 14px;
            font-family: 'Fira Code', 'Courier New', monospace;
        }
        @keyframes pulseWarning {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }
        .pulse-alert {
            animation: pulseWarning 1.5s infinite ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col font-sans antialiased">

    {{-- Top Assessment Bar --}}
    <header class="bg-slate-800/90 border-b border-slate-700/80 sticky top-0 z-50 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col md:flex-row items-center justify-between gap-4">
            
            {{-- Branding & Candidate Info --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-purple-500/30">
                    <i class="fa-solid fa-code"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs uppercase tracking-wider font-semibold px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            Proctored Exam
                        </span>
                        <span class="text-xs text-slate-400">Task #{{ $task->id }}</span>
                    </div>
                    <h1 class="text-base font-bold text-white truncate max-w-md">{{ $task->title }}</h1>
                </div>
            </div>

            {{-- Center: Live Proctored Timer --}}
            <div class="flex items-center gap-3 bg-slate-950/80 px-4 py-2 rounded-xl border border-slate-700">
                <i class="fa-regular fa-clock text-amber-400 text-lg"></i>
                <div class="text-left">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Remaining Time</div>
                    <div id="countdownTimer" class="font-mono text-xl font-bold tracking-wider text-amber-300">
                        --:--:--
                    </div>
                </div>
                <div class="h-7 w-[1px] bg-slate-700 mx-1"></div>
                <div class="text-right">
                    <div class="text-[10px] uppercase font-bold text-slate-400">Total Allotted</div>
                    <div class="text-sm font-semibold text-slate-200">{{ $task->duration_hours }} Hours</div>
                </div>
            </div>

            {{-- Right: Telemetry & Actions --}}
            <div class="flex items-center gap-3">
                {{-- Anti-Cheat Radar Pill --}}
                <div id="radarPill" class="flex items-center gap-2 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700 text-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-slate-300 font-medium">Proctoring:</span>
                    <span id="tabSwitchCounter" class="font-bold text-emerald-400">0 Switches</span>
                </div>

                {{-- Draft auto-save indicator --}}
                <div id="saveStatus" class="hidden md:flex items-center gap-1.5 text-xs text-slate-400">
                    <i class="fa-solid fa-cloud-arrow-up text-emerald-400"></i>
                    <span id="saveStatusText">Draft Synced</span>
                </div>

                @if(!$task->submitted_at)
                <button type="button" onclick="openSubmitModal()" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Submit Solution
                </button>
                @else
                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-check-double"></i> Submitted & Evaluated
                </span>
                @endif
            </div>

        </div>
    </header>

    {{-- Anti-Cheat Tab Switch Warning Banner (Hidden by default) --}}
    <div id="warningBanner" class="hidden bg-amber-500/20 border-b border-amber-500/40 text-amber-200 px-4 py-2.5 text-sm flex items-center justify-between">
        <div class="max-w-7xl mx-auto w-full flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-lg pulse-alert"></i>
            <div>
                <strong class="font-semibold text-amber-100">Anti-Cheat Alert:</strong>
                <span>Tab switch / browser blur detected. This event has been permanently recorded in your proctoring audit log.</span>
            </div>
            <button onclick="document.getElementById('warningBanner').classList.add('hidden')" class="ml-auto text-amber-300 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    {{-- Main Assessment Canvas --}}
    <main class="max-w-7xl mx-auto px-4 py-6 w-full flex-1">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="bg-emerald-900/50 border border-emerald-600/50 text-emerald-200 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        {{-- If Task is already Evaluated, Show Results Ribbon --}}
        @if($task->status === 'Reviewed' && $task->ai_rubric_scores)
        <div class="bg-gradient-to-r from-purple-900/60 to-slate-900 border border-purple-500/40 rounded-2xl p-6 mb-6 shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-700/60">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-400 bg-purple-500/20 px-3 py-1 rounded-full border border-purple-500/30">
                        AI Automated Evaluation Result
                    </span>
                    <h2 class="text-2xl font-bold text-white mt-2">Evaluation Completed & Scored</h2>
                    <p class="text-sm text-slate-300 mt-1">Evaluated on {{ $task->completed_at?->format('d M Y, h:i A') }}</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="text-center px-6 py-3 bg-slate-950/80 rounded-xl border border-slate-700">
                        <div class="text-xs uppercase text-slate-400 font-bold">Overall Score</div>
                        <div class="text-4xl font-black text-emerald-400">{{ $task->score }}<span class="text-lg text-slate-400 font-normal">/100</span></div>
                    </div>
                    <a href="{{ route('tasks.report', $task) }}" target="_blank" class="bg-purple-600 hover:bg-purple-500 text-white font-semibold px-4 py-3 rounded-xl text-sm flex items-center gap-2 transition shadow-lg shadow-purple-600/30">
                        <i class="fa-solid fa-file-pdf"></i> View Official Report Card
                    </a>
                </div>
            </div>

            {{-- 5-Dimension Competency Radar & Security Highlights --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
                <div class="lg:col-span-5 bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                    <h3 class="text-sm font-bold text-slate-200 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-purple-400"></i> Competency Radar Breakdown
                    </h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="candidateRadarChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-7 bg-slate-950/60 p-4 rounded-xl border border-slate-800 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-200 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-400"></i> Security & Best Practice Audit
                        </h3>
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-2">
                            @foreach($task->ai_security_flags ?? [] as $flag)
                            <div class="p-2.5 rounded-lg text-xs {{ $flag['severity'] === 'Passed' ? 'bg-emerald-950/40 border border-emerald-800/60 text-emerald-200' : ($flag['severity'] === 'Critical' ? 'bg-red-950/40 border border-red-800/60 text-red-200' : 'bg-amber-950/40 border border-amber-800/60 text-amber-200') }}">
                                <div class="font-bold flex items-center gap-1.5">
                                    <span>{{ $flag['severity'] === 'Passed' ? '✅' : '⚠️' }}</span>
                                    <span>[{{ $flag['severity'] }}] {{ $flag['title'] }}</span>
                                </div>
                                <div class="mt-1 opacity-90">{{ $flag['description'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between">
                        <span>Anti-Cheat Status: <strong>{{ $task->getProctoringRisk()['level'] }} Risk</strong></span>
                        <span>Tab Switches: <strong>{{ $task->tab_switch_count }}</strong> | Pastes: <strong>{{ $task->paste_count }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- 2-Column Assessment Workspace --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Left Column: Problem Statement & Instructions (5 Cols) --}}
            <div class="lg:col-span-5 space-y-5">
                
                {{-- Trainee Profile Card --}}
                <div class="bg-slate-800/80 rounded-2xl p-5 border border-slate-700/80 shadow-lg">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-700/60">
                        <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center text-xl text-purple-300 font-bold">
                            {{ strtoupper(substr($task->trainee_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-white">{{ $task->trainee_name }}</div>
                            <div class="text-xs text-slate-400">{{ $task->trainee_email }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                        <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-700/50">
                            <div class="text-[10px] uppercase font-bold text-slate-400">Role</div>
                            <div class="text-sm font-bold text-indigo-400">{{ $task->role }}</div>
                        </div>
                        <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-700/50">
                            <div class="text-[10px] uppercase font-bold text-slate-400">Level</div>
                            <div class="text-sm font-bold text-purple-400">{{ $task->level }}</div>
                        </div>
                        <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-700/50">
                            <div class="text-[10px] uppercase font-bold text-slate-400">Status</div>
                            <div class="text-sm font-bold {{ $task->status === 'Reviewed' ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ $task->status }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Task Description & Specification --}}
                <div class="bg-slate-800/80 rounded-2xl p-5 border border-slate-700/80 shadow-lg space-y-4">
                    <div>
                        <h2 class="text-base font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-file-lines text-indigo-400"></i> Project Objective
                        </h2>
                        <p class="text-sm text-slate-300 mt-2 leading-relaxed bg-slate-900/50 p-3.5 rounded-xl border border-slate-700/50">
                            {{ $task->description }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-purple-400"></i> Test Instructions & Requirements
                        </h3>
                        <div class="text-xs text-slate-300 mt-2 bg-slate-900/50 p-3.5 rounded-xl border border-slate-700/50 space-y-1.5 leading-relaxed">
                            {{ $task->test_instructions }}
                        </div>
                    </div>

                    {{-- Proctoring Telemetry Panel --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-slate-400 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-satellite-dish text-teal-400"></i> Live Anti-Cheat Telemetry
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300">Active</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="bg-slate-900/80 p-3 rounded-xl border border-slate-700 flex items-center justify-between">
                                <span class="text-slate-400">Tab Switches:</span>
                                <span id="statTabSwitches" class="font-mono font-bold text-white">{{ $task->tab_switch_count ?? 0 }}</span>
                            </div>
                            <div class="bg-slate-900/80 p-3 rounded-xl border border-slate-700 flex items-center justify-between">
                                <span class="text-slate-400">Clipboard Pastes:</span>
                                <span id="statPastes" class="font-mono font-bold text-white">{{ $task->paste_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column: In-Browser Code Sandbox (7 Cols) --}}
            <div class="lg:col-span-7 space-y-5">
                
                <div class="bg-slate-800/80 rounded-2xl p-5 border border-slate-700/80 shadow-lg">
                    
                    {{-- Sandbox Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-700/60">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-terminal text-emerald-400"></i>
                            <h2 class="text-base font-bold text-white">In-Browser Code Sandbox</h2>
                        </div>

                        {{-- Controls: Language & Theme & Save --}}
                        <div class="flex items-center gap-2">
                            <select id="langSelector" onchange="changeEditorMode(this.value)" class="bg-slate-900 text-slate-200 text-xs px-3 py-1.5 rounded-lg border border-slate-700 focus:outline-none focus:border-purple-500">
                                <option value="php" {{ $task->role !== 'Frontend' ? 'selected' : '' }}>PHP / Laravel</option>
                                <option value="javascript" {{ $task->role === 'Frontend' ? 'selected' : '' }}>JavaScript (ES6)</option>
                                <option value="html">HTML5 / Blade</option>
                                <option value="sql">SQL Query</option>
                            </select>

                            <select id="themeSelector" onchange="changeEditorTheme(this.value)" class="bg-slate-900 text-slate-200 text-xs px-2.5 py-1.5 rounded-lg border border-slate-700 focus:outline-none">
                                <option value="monokai">Monokai (Dark)</option>
                                <option value="dracula">Dracula (Dark)</option>
                                <option value="github">GitHub (Light)</option>
                            </select>

                            <button type="button" onclick="manualSaveDraft()" class="bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1.5">
                                <i class="fa-solid fa-floppy-disk"></i> Save Draft
                            </button>
                        </div>
                    </div>

                    {{-- Editor Container --}}
                    <div class="relative rounded-xl overflow-hidden border border-slate-700 bg-slate-950">
                        <div id="codeEditor">{{ $task->submitted_code ?: ($task->draft_code ?: "// Write your solution for: {$task->title}\n\nnamespace App;\n\nclass Solution {\n    // Implement your technical solution here...\n}\n") }}</div>
                    </div>

                    {{-- GitHub Repository Alternative Input --}}
                    <div class="mt-4 pt-4 border-t border-slate-700/60">
                        <label class="block text-xs font-semibold text-slate-300 mb-1 flex items-center gap-2">
                            <i class="fa-brands fa-github text-base text-white"></i>
                            <span>GitHub Repository URL (Optional alternative / supplement)</span>
                        </label>
                        <input 
                            type="url" 
                            id="githubUrlInput" 
                            value="{{ $task->github_repo_url ?? '' }}" 
                            placeholder="https://github.com/your-username/assessment-repo" 
                            class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 transition"
                        >
                    </div>

                    {{-- Bottom Sandbox Footer --}}
                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Auto-save: active every 30 seconds</span>
                        </div>

                        @if(!$task->submitted_at)
                        <button type="button" onclick="openSubmitModal()" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Submit Solution For AI Review
                        </button>
                        @endif
                    </div>

                </div>

            </div>

        </div>

    </main>

    {{-- Final Submission Confirmation Modal --}}
    <div id="submitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div class="bg-slate-900 border border-slate-700 max-w-lg w-full rounded-2xl shadow-2xl overflow-hidden">
            
            <form method="POST" action="{{ route('assessment.submit', ['token' => $task->access_token]) }}" id="assessmentSubmitForm">
                @csrf
                <input type="hidden" name="submitted_code" id="modalSubmittedCode">
                <input type="hidden" name="github_repo_url" id="modalGithubUrl">

                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-xl mb-4">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <h3 class="text-xl font-bold text-white mb-2">Ready to Submit Your Assessment?</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Once submitted, your solution will be locked and automatically evaluated by the AI Code Reviewer for functionality, PSR cleanliness, and security integrity.
                    </p>

                    <div class="mt-5 space-y-3 bg-slate-950/60 p-4 rounded-xl border border-slate-800 text-xs">
                        <div class="font-semibold text-slate-200">Submission Mode:</div>
                        <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                            <input type="radio" name="submission_type" value="code" checked class="text-purple-600 focus:ring-purple-500">
                            <span>Submit In-Browser Sandbox Code</span>
                        </label>
                        <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                            <input type="radio" name="submission_type" value="github" class="text-purple-600 focus:ring-purple-500">
                            <span>Submit GitHub Repository Link</span>
                        </label>
                        <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                            <input type="radio" name="submission_type" value="both" class="text-purple-600 focus:ring-purple-500">
                            <span>Submit Both Sandbox Code & GitHub Link</span>
                        </label>
                    </div>
                </div>

                <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeSubmitModal()" class="px-4 py-2 rounded-lg text-sm text-slate-400 hover:text-white transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-5 py-2 rounded-lg text-sm transition shadow-lg shadow-emerald-600/30">
                        Confirm & Trigger AI Evaluation
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Scripts: Ace Editor, Timer, Telemetry & Auto-save --}}
    <script>
        // 1. Initialize Ace Editor
        const editor = ace.edit("codeEditor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("{{ $task->role === 'Frontend' ? 'ace/mode/javascript' : 'ace/mode/php' }}");
        editor.setOptions({
            enableBasicAutocompletion: true,
            enableLiveAutocompletion: true,
            enableSnippets: true,
            showLineNumbers: true,
            tabSize: 4,
            useSoftTabs: true,
            wrap: true
        });

        @if($task->submitted_at)
        editor.setReadOnly(true);
        @endif

        function changeEditorMode(mode) {
            editor.session.setMode("ace/mode/" + mode);
        }

        function changeEditorTheme(theme) {
            editor.setTheme("ace/theme/" + theme);
        }

        // 2. Live Proctored Countdown Timer
        let remainingSeconds = {{ $remainingSeconds }};
        const timerDisplay = document.getElementById("countdownTimer");

        function updateTimer() {
            if (remainingSeconds <= 0) {
                timerDisplay.textContent = "00:00:00 - EXPIRED";
                timerDisplay.classList.add("text-red-400");
                return;
            }

            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;

            timerDisplay.textContent = 
                String(hours).padStart(2, '0') + ":" +
                String(minutes).padStart(2, '0') + ":" +
                String(seconds).padStart(2, '0');

            remainingSeconds--;
        }

        updateTimer();
        setInterval(updateTimer, 1000);

        // 3. Anti-Cheat Radar Telemetry
        const token = "{{ $task->access_token }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let tabSwitches = {{ $task->tab_switch_count ?? 0 }};
        let pastes = {{ $task->paste_count ?? 0 }};

        function sendTelemetry(eventType, details) {
            fetch(`/assessment/${token}/telemetry`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    event: eventType,
                    details: details
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    tabSwitches = data.tab_switch_count;
                    pastes = data.paste_count;
                    document.getElementById('statTabSwitches').textContent = tabSwitches;
                    document.getElementById('statPastes').textContent = pastes;
                    document.getElementById('tabSwitchCounter').textContent = `${tabSwitches} Switches`;

                    if (tabSwitches >= 3) {
                        document.getElementById('radarPill').classList.remove('border-slate-700');
                        document.getElementById('radarPill').classList.add('border-amber-500', 'bg-amber-950/40');
                        document.getElementById('tabSwitchCounter').classList.remove('text-emerald-400');
                        document.getElementById('tabSwitchCounter').classList.add('text-amber-400');
                    }
                }
            })
            .catch(() => {});
        }

        // Detect Tab Switch / Window Blur
        window.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                sendTelemetry('TAB_SWITCH', 'Candidate switched browser tab or minimized window');
                document.getElementById('warningBanner').classList.remove('hidden');
            }
        });

        window.addEventListener('blur', () => {
            sendTelemetry('WINDOW_BLUR', 'Window lost focus');
        });

        // Detect Bulk Paste
        editor.on('paste', (e) => {
            const pasteLength = e.text ? e.text.length : 0;
            if (pasteLength > 30) {
                sendTelemetry('CLIPBOARD_PASTE', `Pasted code block of ${pasteLength} characters`);
            }
        });

        // 4. Draft Auto-Save
        function syncDraft() {
            if ("{{ $task->submitted_at }}") return;

            const code = editor.getValue();
            const statusEl = document.getElementById('saveStatusText');
            statusEl.textContent = 'Syncing...';

            fetch(`/assessment/${token}/draft`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    draft_code: code
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    statusEl.textContent = `Draft Saved (${data.saved_at})`;
                }
            })
            .catch(() => {
                statusEl.textContent = 'Auto-save retry...';
            });
        }

        setInterval(syncDraft, 30000); // sync every 30 seconds

        function manualSaveDraft() {
            syncDraft();
        }

        // 5. Modal Handling
        function openSubmitModal() {
            document.getElementById('modalSubmittedCode').value = editor.getValue();
            document.getElementById('modalGithubUrl').value = document.getElementById('githubUrlInput').value;
            document.getElementById('submitModal').classList.remove('hidden');
        }

        function closeSubmitModal() {
            document.getElementById('submitModal').classList.add('hidden');
        }

        // 6. Chart.js Radar Chart (if already evaluated)
        @if($task->status === 'Reviewed' && $task->ai_rubric_scores)
        const ctx = document.getElementById('candidateRadarChart').getContext('2d');
        const rubric = @json($task->ai_rubric_scores);

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Architecture', 'Security', 'Database / ORM', 'Clean Code', 'Problem Solving'],
                datasets: [{
                    label: 'Candidate Score',
                    data: [
                        rubric.architecture || 75,
                        rubric.security || 80,
                        rubric.database_orm || 75,
                        rubric.clean_code || 78,
                        rubric.problem_solving || 80
                    ],
                    backgroundColor: 'rgba(168, 85, 247, 0.25)',
                    borderColor: 'rgb(168, 85, 247)',
                    pointBackgroundColor: 'rgb(192, 132, 252)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(168, 85, 247)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        pointLabels: { color: '#cbd5e1', font: { size: 11, weight: 'bold' } },
                        ticks: { backdropColor: 'transparent', color: '#94a3b8', min: 0, max: 100, stepSize: 20 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        @endif
    </script>
</body>
</html>
