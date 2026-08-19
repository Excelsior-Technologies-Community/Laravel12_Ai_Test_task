<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <title>AI Task Assignment</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
        }

        .header {
            background: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #f9f9f9;
            padding: 25px;
        }

        .task-card {
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .info {
            background: #eef2ff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <h1>
            🤖 AI Task Assignment
        </h1>

        <p>
            AI Training System
        </p>

    </div>


    <div class="content">

        <h2>
            Hello {{ $name }}!
        </h2>

        <p>
            Our AI training system has generated a new technical
            assignment based on your selected role and experience level.
        </p>


        <div class="info">

            <p>
                <strong>Role:</strong>
                {{ $role }}
            </p>

            <p>
                <strong>Level:</strong>
                {{ $task->level }}
            </p>

            <p>
                <strong>Estimated Duration:</strong>
                {{ $task->duration_hours }} hours
            </p>

        </div>


        <div class="task-card">

            <h3>
                {{ $task->title }}
            </h3>


            <h4>
                Task Description
            </h4>

            <p>
                {{ $task->description }}
            </p>


            <h4>
                Test Instructions
            </h4>

            <p>
                {{ $task->test_instructions }}
            </p>


            <h4>
                Requirements
            </h4>

            <ul>

                <li>
                    Complete the task within
                    {{ $task->duration_hours }} hours.
                </li>

                <li>
                    Follow best practices for
                    {{ $role }} development.
                </li>

                <li>
                    Include proper error handling.
                </li>

                <li>
                    Write clean and documented code.
                </li>

                <li>
                    Test your implementation before submission.
                </li>

            </ul>

        </div>


        <p>
            Once you complete the assignment, your trainer can
            update its status and evaluate your work.
        </p>


        <div class="footer">

            <p>
                Good luck with your task!
            </p>

            <p>
                Best regards,<br>
                <strong>AI Training System</strong>
            </p>

        </div>

    </div>

</div>

</body>
</html>