<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .task-card { background: white; border: 1px solid #ddd; padding: 20px; margin: 20px 0; }
        .btn { display: inline-block; background: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 AI Task Assignment</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $name }}!</h2>
            <p>Our AI system has generated a task for your <strong>{{ $role }}</strong> role.</p>
            
            <div class="task-card">
                <h3>{{ $task->title }}</h3>
                <p><strong>Level:</strong> {{ $task->level }}</p>
                <p><strong>Duration:</strong> {{ $task->duration_hours }} hours</p>
                
                <h4>Task Description:</h4>
                <p>{{ $task->description }}</p>
                
                <h4>Test Instructions:</h4>
                <p>{{ $task->test_instructions }}</p>
                
                <h4>Requirements:</h4>
                <ul>
                    <li>Complete the task within {{ $task->duration_hours }} hours</li>
                    <li>Follow best practices for {{ $role }} development</li>
                    <li>Include proper error handling</li>
                    <li>Write clean and documented code</li>
                </ul>
            </div>
            
            <p>Good luck with your task! The AI will evaluate your submission.</p>
            
            <p>Best regards,<br>
            AI Training System</p>
        </div>
    </div>
</body>
</html>