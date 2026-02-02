# PHP_Laravel12_AI_Training_System

AI Training System is a Laravel 12 based project that automatically generates technical tasks for trainees based on **Role** and **Experience Level**, stores them in the database, and sends full task details via **Email Notification**. This project is ideal for training institutes, internships, and developer evaluation systems.

---

## Project Overview

This system helps trainers or companies automatically assign development tasks using a rule‑based AI style generator.

**Core Concepts**

* Role‑based task generation (Laravel, PHP, Frontend)
* Level‑based difficulty (Beginner, Intermediate, Advanced)
* Email delivery of task instructions
* Task history storage
* Simple UI with TailwindCSS

---

## Tech Stack

* Laravel 12
* PHP 8.2+
* MySQL
* Blade Templates
* TailwindCSS (CDN)
* SMTP Email (Gmail or any SMTP)

---

## Features

* Automatic AI‑style task generation
* Email notifications with full task details
* Role‑based tasks
* Difficulty‑based tasks
* Task duration calculation
* Task history listing
* Simple and clean UI
* No login required

---

## Installation Guide

### Step 1: Setup Project

```bash
composer create-project laravel/laravel AI-Training-System
cd AI-Training-System
```

---

### Step 2: Update Environment File

Update `.env` configuration:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_training
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="tasks@aitraining.com"
MAIL_FROM_NAME="AI Task System"
```

---

### Step 3: Create Migration

```bash
php artisan make:migration create_tasks_table
```

Run migration:

```bash
php artisan migrate
```

---

### Step 4: Create Model

File: `app/Models/Task.php`

This model stores generated tasks with fields such as title, description, role, level, duration, and instructions.

---

### Step 5: Create AI Task Generator Service

File: `app/Services/AITaskGenerator.php`

Responsibilities:

* Maintain predefined task sets
* Select random tasks
* Generate descriptions
* Generate instructions
* Assign duration based on level

---

### Step 6: Create Email Service

File: `app/Services/TaskEmailService.php`

Responsibilities:

* Prepare email subject
* Pass task data to blade template
* Send task email using SMTP

---

### Step 7: Create Controller

```bash
php artisan make:controller TaskController
```

Controller Responsibilities:

* Show task list
* Show task creation form
* Generate AI task
* Save task in database
* Send task email

---

### Step 8: Create Email Template

Path: `resources/views/emails/task_assigned.blade.php`

Email contains:

* Trainee name
* Role and level
* Task description
* Duration
* Test instructions
* Requirements checklist

---

### Step 9: Create Views

**Layouts**

* `resources/views/layouts/app.blade.php`

**Pages**

* Task List Page
* Task Generate Form Page

UI built with TailwindCSS CDN for quick styling.

---

### Step 10: Create Routes

File: `routes/web.php`

Routes include:

* `/` – Task list
* `/create` – Generate form
* `/generate-task` – POST action

---

### Step 11: Run Application

```bash
php artisan serve
```

Open in browser:

```
http://localhost:8000
```
<img width="1759" height="956" alt="image" src="https://github.com/user-attachments/assets/b81e8421-5ad1-4226-8718-8581fd1766e9" />

---

## How to Test

Visit:

```
http://localhost:8000/create
```
<img width="1783" height="887" alt="image" src="https://github.com/user-attachments/assets/1ea98326-82a4-421e-9b87-be0a0c2d2fa1" />
<img width="1919" height="972" alt="image" src="https://github.com/user-attachments/assets/7a73daf1-e892-46e9-9f5a-8721087e25fc" />


Fill the form with:

* Name
* Email
* Role
* Level

Click **Generate & Send AI Task**.

System will:

1. Generate task
2. Save to database
3. Send email
4. Display task in history list

---

## Complete Feature List

* AI task generation logic
* Email notification system
* Role‑based task selection
* Level‑based complexity
* Database task storage
* Simple UI interface
* Task duration estimation
* No authentication required

---

## System Workflow

1. User selects role and level
2. AI generator selects task
3. Task description and instructions created
4. Task stored in database
5. Email sent to trainee
6. Task appears in task history

---

## Use Cases

* Internship evaluation systems
* Training institutes
* Developer skill testing
* Company assignment automation
* MCA / BCA final year projects

---

## Optional Enhancements

* Admin login system
* Real AI API integration
* File submission upload
* Automatic evaluation scoring
* Dashboard analytics
* Queue based email sending

---

## Learning Outcomes

After completing this project, you will understand:

* Laravel MVC architecture
* Service layer usage
* Email integration
* Form validation
* Database migrations
* Blade templating
* TailwindCSS basics

---

## License

This project is open‑source and free for educational and development purposes.
