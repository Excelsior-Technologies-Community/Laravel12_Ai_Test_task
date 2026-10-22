<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AI Task Assignment</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 620px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: white;
            padding: 30px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
        }
        .header p {
            margin: 6px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px 25px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-purple { background: #ede9fe; color: #6d28d9; }
        .info-grid {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            text-align: center;
        }
        .info-grid .item-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600; }
        .info-grid .item-val { font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 4px; }
        .cta-box {
            text-align: center;
            margin: 28px 0;
            padding: 20px;
            background: #faf5ff;
            border: 1px dashed #c084fc;
            border-radius: 10px;
        }
        .btn-start {
            display: inline-block;
            background: #7c3aed;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }
        .requirements {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 18px;
            margin: 20px 0;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 18px;
            color: #6b7280;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🎯 AI Technical Assessment Assigned</h1>
        <p>Interactive Developer Evaluation Platform</p>
    </div>

    <div class="content">
        <p style="font-size: 16px;">Hello <strong>{{ $name }}</strong>,</p>
        <p>An interactive technical challenge has been customized and assigned to you by our AI training engine.</p>

        <div class="info-grid">
            <div>
                <div class="item-label">Target Role</div>
                <div class="item-val">{{ $role }}</div>
            </div>
            <div>
                <div class="item-label">Difficulty</div>
                <div class="item-val">{{ $task->level }}</div>
            </div>
            <div>
                <div class="item-label">Duration</div>
                <div class="item-val">{{ $task->duration_hours }}h</div>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #111827; font-size: 18px;">{{ $task->title }}</h3>
            <p style="color: #4b5563;">{{ $task->description }}</p>
            <h4 style="color: #374151; font-size: 14px; margin-bottom: 6px;">Instructions:</h4>
            <div style="background: #f9fafb; padding: 12px; border-radius: 6px; font-size: 13px; color: #4b5563;">
                {{ $task->test_instructions }}
            </div>
        </div>

        <div class="cta-box">
            <h4 style="margin: 0 0 8px; color: #581c87;">Ready to start your proctored assessment?</h4>
            <p style="margin: 0 0 16px; font-size: 13px; color: #6b7280;">You can write code directly in our in-browser sandbox or paste your solution.</p>
            <a href="{{ $task->assessment_url }}" class="btn-start">
                💻 Launch Live Assessment Portal &rarr;
            </a>
            <p style="margin: 12px 0 0; font-size: 11px; color: #9ca3af;">
                Direct link: {{ $task->assessment_url }}
            </p>
        </div>

        <div class="requirements">
            <h4 style="margin-top: 0; font-size: 14px;">Assessment Guidelines:</h4>
            <ul style="padding-left: 20px; font-size: 13px; color: #4b5563; margin-bottom: 0;">
                <li>The live countdown begins once you open your candidate test portal.</li>
                <li>Write modular, clean, PSR-standard code with proper validation.</li>
                <li>Anti-cheat telemetry monitors focus changes and bulk paste operations.</li>
                <li>Your submission will be immediately processed by the automated AI review scanner.</li>
            </ul>
        </div>

        <div class="footer">
            <p>Best of luck with your evaluation!<br>
            <strong>AI Developer Assessment System</strong></p>
        </div>
    </div>
</div>

</body>
</html>
