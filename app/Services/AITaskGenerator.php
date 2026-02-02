<?php
namespace App\Services;

class AITaskGenerator
{
    private $laravelTasks = [
        'Beginner' => [
            'Create a basic CRUD application',
            'Build user authentication system',
            'Create REST API endpoints'
        ],
        'Intermediate' => [
            'Implement file upload with validation',
            'Create real-time notifications',
            'Build payment integration'
        ],
        'Advanced' => [
            'Create microservices architecture',
            'Implement queue system',
            'Build API with JWT authentication'
        ]
    ];

    private $phpTasks = [
        'Beginner' => [
            'Create form validation script',
            'Build contact form with email',
            'Create file handling system'
        ],
        'Intermediate' => [
            'Build PDF generator',
            'Create CSV import/export',
            'Implement image processing'
        ],
        'Advanced' => [
            'Create custom framework',
            'Build WebSocket server',
            'Implement caching system'
        ]
    ];

    private $frontendTasks = [
        'Beginner' => [
            'Create responsive landing page',
            'Build todo app with JavaScript',
            'Create form with validation'
        ],
        'Intermediate' => [
            'Build e-commerce cart',
            'Create dashboard with charts',
            'Build real-time chat interface'
        ],
        'Advanced' => [
            'Create SPA with Vue.js/React',
            'Build PWA application',
            'Create animation library'
        ]
    ];

    public function generateTask($role, $level)
    {
        $tasks = $this->getTasksByRole($role);
        
        if (!isset($tasks[$level])) {
            $level = 'Beginner';
        }
        
        $taskList = $tasks[$level];
        $randomTask = $taskList[array_rand($taskList)];
        
        return [
            'title' => $randomTask . " - $role $level Task",
            'description' => $this->generateDescription($randomTask, $role, $level),
            'test_instructions' => $this->generateTestInstructions($randomTask, $role, $level),
            'duration_hours' => $this->getDuration($level)
        ];
    }

    private function getTasksByRole($role)
    {
        return match($role) {
            'Laravel' => $this->laravelTasks,
            'PHP' => $this->phpTasks,
            'Frontend' => $this->frontendTasks,
            default => $this->laravelTasks
        };
    }

    private function generateDescription($task, $role, $level)
    {
        return "This is a $level level $role task. You need to: $task. 
                This task will test your $role skills at $level level.";
    }

    private function generateTestInstructions($task, $role, $level)
    {
        $instructions = [
            'Laravel' => [
                'Beginner' => "Create a new Laravel project. Use migrations, controllers, and Blade templates.",
                'Intermediate' => "Use middleware, form requests, and API resources. Implement validation.",
                'Advanced' => "Use queues, events, and service providers. Implement security best practices."
            ],
            'PHP' => [
                'Beginner' => "Write clean PHP code with proper error handling.",
                'Intermediate' => "Use OOP principles, namespaces, and design patterns.",
                'Advanced' => "Implement performance optimization and security features."
            ],
            'Frontend' => [
                'Beginner' => "Use HTML, CSS, and vanilla JavaScript.",
                'Intermediate' => "Use framework (Vue/React) and responsive design.",
                'Advanced' => "Implement state management and performance optimization."
            ]
        ];

        return $instructions[$role][$level] ?? "Complete the task with best practices.";
    }

    private function getDuration($level)
    {
        return match($level) {
            'Beginner' => 4,
            'Intermediate' => 8,
            'Advanced' => 16,
            default => 4
        };
    }
}