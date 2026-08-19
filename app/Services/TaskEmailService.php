<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Mail;

class TaskEmailService
{
    public function sendTaskEmail(
        $email,
        $name,
        Task $task,
        $role
    ) {
        $subject = "🎯 {$role} Task Assigned: {$task->title}";

        $data = [
            'name' => $name,
            'task' => $task,
            'role' => $role,
        ];

        Mail::send(
            'emails.task_assigned',
            $data,
            function ($message) use ($email, $subject) {
                $message
                    ->to($email)
                    ->subject($subject);
            }
        );

        return true;
    }
}