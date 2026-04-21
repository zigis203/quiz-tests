# Quiz System - Project Documentation

## Overview
A PHP-based Quiz System web application with user authentication, role-based access (admin/user), dynamic quizzes, progress tracking, and result management.

## Technology Stack
- **Backend**: PHP 8.0+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Architecture**: MVC-inspired with models and controllers in views

## Project Structure

```
quiz-tests/
├── config/                          # Configuration & Database
│   ├── config.php                   # Global config, session init, autoloader
│   ├── Database.php                 # PDO connection & schema initialization
│   ├── setup.php                    # Initial admin setup
│   └── auth_middleware.php          # Authentication helper functions
│
├── app/
│   └── models/                      # Business logic classes
│       ├── User.php                 # User auth, registration, queries
│       ├── Topic.php                # Topic management
│       ├── Question.php             # Question management
│       ├── QuizResult.php           # Result tracking & scores
│       └── Seeder.php               # Database seeding with sample data
│
├── views/                           # Public-facing PHP files
│   ├── index.php                    # Home/landing page
│   ├── login.php                    # Login form & authentication
│   ├── register.php                 # Registration form
│   ├── logout.php                   # Session termination
│   │
│   ├── topics.php                   # List all available quiz topics (User)
│   ├── quiz.php                     # Main quiz interface (User)
│   ├── quiz-submit.php              # Process quiz submission (User)
│   ├── results.php                  # Display quiz results (User)
│   ├── history.php                  # User's quiz history & high scores
│   │
│   ├── admin.php                    # Admin dashboard (Admin only)
│   ├── manage_topics.php            # CRUD topics (Admin only)
│   ├── add_topic.php                # Add new topic (Admin only)
│   ├── edit_topic.php               # Edit topic (Admin only)
│   ├── delete_topic.php             # Delete topic (Admin only)
│   │
│   ├── manage_questions.php         # CRUD questions (Admin only)
│   ├── add_question.php             # Add new question (Admin only)
│   ├── edit_question.php            # Edit question (Admin only)
│   ├── delete_question.php          # Delete question (Admin only)
│   │
│   ├── manage_answers.php           # CRUD answers (Admin only)
│   ├── edit_answer.php              # Edit answer (Admin only)
│   ├── delete_answer.php            # Delete answer (Admin only)
│   │
│   ├── setup-admin.php              # Initial admin user creation
│   ├── login.html                   # Static login template
│   ├── index.html                   # Static home template
│   ├── quiz.html                    # Static quiz template
│   ├── results.html                 # Static results template
│   ├── topics.html                  # Static topics template
│   └── skats.html / rezultats.skats.html  # Additional templates
│
├── public/                          # Static assets (accessible by browsers)
│   ├── css/
│   │   ├── style.css                # Main stylesheet
│   │   └── quiz.css                 # Quiz-specific styles
│   ├── js/
│   │   ├── app.js                   # Global app logic
│   │   ├── quiz.js                  # Quiz functionality & interactivity
│   │   ├── results.js               # Results visualization
│   │   ├── topics.js                # Topics page logic
│   │   └── validation.js            # Form validation helpers
│   └── images/                      # Images & assets
│
├── make-admin.php                   # CLI utility to create admin user
├── DATABASE_SCHEMA.sql              # Complete database schema
├── README.md                        # Project README
└── PROJECT_STRUCTURE.md             # This file
```

## Database Schema

### Users Table
- `id` - Primary key
- `username` - Unique username (80 chars)
- `email` - Unique email address
- `password_hash` - bcrypt hashed password
- `role` - ENUM: 'user' or 'admin'
- `created_at` - Registration timestamp

### Topics Table
- `id` - Primary key
- `name` - Topic/subject name (must be unique)
- `description` - Optional topic description
- `created_at` - Creation timestamp

### Questions Table
- `id` - Primary key
- `topic_id` - FK to topics
- `question_text` - The quiz question
- `created_at` - Creation timestamp

### Answers Table
- `id` - Primary key
- `question_id` - FK to questions
- `answer_text` - Answer option text
- `is_correct` - Boolean (1 = correct, 0 = incorrect)
- `created_at` - Creation timestamp

### Quiz_Results Table (High Scores & History)
- `id` - Primary key
- `user_id` - FK to users
- `topic_id` - FK to topics
- `score` - Number of correct answers
- `total` - Total questions in quiz
- `created_at` - Quiz completion timestamp

## Setup Instructions

### 1. Database Configuration
Edit `config/config.php` with your database credentials:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'quiz_app');
define('DB_USER', 'quiz_user');
define('DB_PASS', 'quiz_pass');
```

### 2. Initialize Database
The `Database.php` class automatically:
- Creates all tables on first connection
- Seeds initial data (5 topics with 15 questions each)
- Sets up an admin user (admin/admin123)

### 3. First Login
- **Admin User**: username: `admin`, password: `admin123`
- Navigate to `admin.php` to manage topics and questions
- Create regular users via the registration form

## Authentication Flow

### Login Process
1. User enters username and password on `login.php`
2. PHP validates credentials against hashed password in DB
3. On success: User data stored in `$_SESSION`
4. Redirect based on role:
   - Admin → `admin.php`
   - User → `topics.php`

### Role-Based Access Control
Use the `AuthMiddleware` class in protected pages:

```php
<?php
require_once __DIR__ . '/../config/auth_middleware.php';

// For regular users only
AuthMiddleware::requireLogin();

// For admin only
AuthMiddleware::requireAdmin();

// For logged out users (login/register pages)
AuthMiddleware::requireLogout();
```

## Key Models & Methods

### User Model
```php
$user->register($username, $email, $password, $role = 'user');
$user->authenticate($username, $password);
$user->findByUsername($username);
$user->findByEmail($email);
$user->findById($id);
User::login($userData);
User::logout();
User::getCurrentUser($db);
```

### Topic Model
```php
$topic->addTopic($name, $description);
$topic->getAll();
$topic->getById($id);
$topic->updateTopic($id, $name, $description);
$topic->deleteTopic($id);
```

### Question Model
```php
$question->addQuestionWithAnswers($topicId, $questionText, $answers);
$question->getByTopic($topicId, $limit = 15);  // Get random questions
$question->getById($id);
$question->updateQuestion($id, $text);
$question->deleteQuestion($id);
```

### QuizResult Model
```php
$result->saveResult($userId, $topicId, $score, $total);
$result->getUserResults($userId);
$result->getHighScores($topicId, $limit = 10);
$result->getTopicResults($topicId);
```

## Features

### User Features
- ✅ Register with email and password validation
- ✅ Login with session management
- ✅ View available quiz topics
- ✅ Take quizzes with 15 random questions per topic
- ✅ Answer shuffling (PHP-based)
- ✅ Real-time progress bar
- ✅ View detailed results with score
- ✅ View quiz history and high scores

### Admin Features
- ✅ Manage topics (Create, Read, Update, Delete)
- ✅ Manage questions per topic (CRUD)
- ✅ Manage answer options (CRUD)
- ✅ View user statistics and high scores
- ✅ Seed initial data with sample quizzes

## Security Notes

1. **Password Hashing**: Using PHP's `password_hash()` with PASSWORD_DEFAULT (bcrypt)
2. **SQL Injection**: All queries use prepared statements with PDO
3. **Session Security**: Session starts automatically in config.php
4. **CSRF Protection**: Add tokens in production (not implemented in MVP)
5. **Authorization**: Role-based access control via AuthMiddleware

## API Endpoints (via POST)

### Authentication
- `POST /login.php` - User login
- `POST /register.php` - User registration
- `GET /logout.php` - User logout

### User Endpoints
- `GET /topics.php` - List all topics
- `GET /quiz.php?topic_id=X` - Start quiz
- `POST /quiz-submit.php` - Submit quiz answers
- `GET /results.php?result_id=X` - View result details
- `GET /history.php` - User's quiz history

### Admin Endpoints
- `GET /admin.php` - Admin dashboard
- `POST /add_topic.php` - Create topic
- `POST /edit_topic.php` - Update topic
- `POST /delete_topic.php` - Delete topic
- `POST /add_question.php` - Create question
- `POST /edit_question.php` - Update question
- `POST /delete_question.php` - Delete question
- `POST /edit_answer.php` - Update answer
- `POST /delete_answer.php` - Delete answer

## Frontend Features

### Progress Bar (quiz.js)
- Shows current question number vs total
- Visual percentage indicator
- Updates on each question

### Answer Shuffling (quiz.js)
```javascript
// Answers are shuffled client-side using Fisher-Yates algorithm
const shuffledAnswers = shuffleArray(answers);
```

### Result Display
- Score and percentage
- Correct vs incorrect breakdown
- Comparison with high scores
- Option to retake quiz or choose different topic

## File Explanations

### config/config.php
- Starts session
- Defines database credentials
- Sets BASE_URL
- Registers PSR-4 autoloader for models

### config/Database.php
- PDO singleton pattern
- Auto-creates database schema
- Calls Seeder on first run
- Provides getConnection() method

### config/auth_middleware.php
- `requireLogin()` - Protect user pages
- `requireAdmin()` - Protect admin pages
- `isLoggedIn()` - Check login status
- `logout()` - Terminate session

### app/models/*.php
- Pure business logic
- Database queries via PDO
- Validation rules
- No HTML output

### views/*.php
- Mix of PHP logic and HTML
- Calls models for data
- Renders forms and results
- Checks permissions via AuthMiddleware

### public/js/*.js
- Client-side interactivity
- Answer shuffling
- Form validation
- Progress tracking
- Result visualization

## Troubleshooting

### Database Connection Fails
1. Check MySQL is running
2. Verify credentials in config/config.php
3. Ensure database user exists and has proper permissions
4. Check MySQL port (default 3306)

### Password Reset Required
- Use `make-admin.php` to create new admin
- Or manually reset in database:
  ```sql
  UPDATE users SET password_hash = PASSWORD('newpass') WHERE username = 'admin';
  ```

### Session Issues
- Ensure `session_start()` is called in config.php (it is)
- Check PHP session.save_path is writable
- Clear browser cookies if issues persist

### Questions Not Showing in Quiz
- Verify questions exist in DB for selected topic
- Check foreign key relationships
- Ensure at least 1 answer is marked as correct per question

## Future Enhancements

- [ ] CSRF token protection
- [ ] Rate limiting for quiz submission
- [ ] Email verification for registration
- [ ] Password reset functionality
- [ ] Quiz difficulty levels
- [ ] Timer for quizzes
- [ ] Leaderboard
- [ ] Question categories/tags
- [ ] Quiz statistics/analytics
- [ ] Dark mode theme
- [ ] API endpoint documentation (OpenAPI/Swagger)

## Deployment Checklist

- [ ] Update DB credentials for production
- [ ] Set appropriate file permissions (755 for dirs, 644 for files)
- [ ] Enable HTTPS
- [ ] Add CSRF tokens
- [ ] Set secure session cookies
- [ ] Configure error logging (not error display)
- [ ] Regular database backups
- [ ] Monitor failed login attempts
- [ ] Use environment variables for secrets

---

**Last Updated**: 2026-04-21
**PHP Version**: 8.0+
**MySQL Version**: 5.7+
