# Quiz System - Quick Start Guide

## Prerequisites
- PHP 8.0+ with PDO MySQL extension
- MySQL 5.7+ server
- A local server (Laragon, XAMPP, WAMP, etc.)
- Web browser

## Installation Steps

### Step 1: Clone/Download Project
```bash
# If you're using this folder structure:
# C:\laragon\www\zhigis\quiz-tests
# The project is already in place
```

### Step 2: Create MySQL Database
Open MySQL client and run:
```sql
CREATE DATABASE quiz_app;
CREATE USER 'quiz_user'@'localhost' IDENTIFIED BY 'quiz_pass';
GRANT ALL PRIVILEGES ON quiz_app.* TO 'quiz_user'@'localhost';
FLUSH PRIVILEGES;
```

**For Laragon with default root user:**
```sql
CREATE DATABASE quiz_app;
```
Then edit `config/config.php`:
```php
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty password
```

### Step 3: Verify Database Connection
Edit `config/config.php` and confirm:
```php
define('DB_HOST', '127.0.0.1');  // or 'localhost'
define('DB_NAME', 'quiz_app');
define('DB_USER', 'quiz_user');   // or 'root'
define('DB_PASS', 'quiz_pass');   // or ''
define('BASE_URL', '/zhigis/quiz-tests/views');
```

### Step 4: Access the Application
1. Start Apache and MySQL servers
2. Navigate to: `http://localhost/zhigis/quiz-tests/views/index.php`
3. Database tables auto-create on first access

### Step 5: First Login
- **Username**: `admin`
- **Password**: `admin123`

## Project Structure Quick Reference

| Folder | Purpose |
|--------|---------|
| `config/` | Database connection & auth configuration |
| `app/models/` | Database logic & business rules |
| `views/` | Web pages & user interface |
| `public/css/` | Stylesheets |
| `public/js/` | Client-side JavaScript |

## Key Files

| File | Purpose |
|------|---------|
| `config/config.php` | Database credentials & session setup |
| `config/Database.php` | MySQL connection (auto-creates schema) |
| `config/auth_middleware.php` | Login/permission checking |
| `app/models/User.php` | User registration & authentication |
| `app/models/Topic.php` | Quiz topics management |
| `app/models/Question.php` | Quiz questions management |
| `app/models/QuizResult.php` | Score tracking & history |
| `views/login.php` | Login page |
| `views/register.php` | Registration page |
| `views/topics.php` | Available quizzes list |
| `views/quiz.php` | Main quiz interface |
| `views/admin.php` | Admin dashboard |

## Common Tasks

### Create a New User
1. Click "Reģistrējies" (Register) on login page
2. Fill in username, email, password
3. Click register
4. Auto-login and redirected to topics

### Create a New Topic (Admin)
1. Login with admin account
2. Go to Admin Panel → Manage Topics
3. Click "Add Topic"
4. Enter topic name and description
5. Save

### Add Questions to Topic (Admin)
1. Admin Panel → Manage Questions
2. Select topic from dropdown
3. Click "Add Question"
4. Enter question text
5. Add 4 answer options (mark one as correct)
6. Save

### Take a Quiz (User)
1. Login with regular user account
2. Select topic from "Topics" list
3. Answer 15 random questions
4. Click "Submit Quiz"
5. View results and score

### View Quiz History (User)
1. Click "History" from main menu
2. See all previous quiz attempts
3. View high scores per topic

## Testing the Quiz Flow

### Quick Test Scenario
1. **Register User**: Use credentials `testuser / test@test.com / TestPass123`
2. **Login**: Use credentials above
3. **Take Quiz**: Select "Sports" topic
4. **Answer Questions**: Select random answers or correct ones
5. **Submit**: Click "Submit Quiz" button
6. **View Result**: See score and percentage
7. **Check History**: Click "History" to see all attempts

## Default Seeded Data

The database auto-seeds with:
- **5 Topics**: Sports, Music, Movies, Science, History
- **15 Questions per Topic** = 75 total questions
- **4 Answers per Question** = 300 total answers
- **Admin User**: `admin` / `admin123`

## Troubleshooting

### "Access denied for user 'quiz_user'@'localhost'"
1. Verify MySQL user was created
2. Check credentials in `config/config.php`
3. Try using `root` user if user doesn't exist

### "Database doesn't exist"
1. Create database: `CREATE DATABASE quiz_app;`
2. Or change DB_NAME to existing database

### "Tables not created"
1. Access `views/login.php` to trigger `Database::getInstance()`
2. Tables auto-create on first access
3. Check MySQL error logs if issues persist

### "403 Access Denied"
1. You're logged in as regular user trying to access admin page
2. Login with admin account to manage topics/questions

### "Session issues / Not staying logged in"
1. Check cookies are enabled in browser
2. Clear browser cache and cookies
3. Verify `session_start()` is first line in `config/config.php`

## Admin Functions

| Function | Location | Access |
|----------|----------|--------|
| Manage Topics | `admin.php` → Topics | Admin only |
| Add Topic | `add_topic.php` | Admin only |
| Edit Topic | `edit_topic.php?id=X` | Admin only |
| Delete Topic | `delete_topic.php?id=X` | Admin only |
| Manage Questions | `admin.php` → Questions | Admin only |
| Add Question | `add_question.php?topic_id=X` | Admin only |
| Edit Question | `edit_question.php?id=X` | Admin only |
| Delete Question | `delete_question.php?id=X` | Admin only |
| Manage Answers | `manage_answers.php?question_id=X` | Admin only |
| Edit Answer | `edit_answer.php?id=X` | Admin only |
| Delete Answer | `delete_answer.php?id=X` | Admin only |

## User Functions

| Function | Location | Access |
|----------|----------|--------|
| View Topics | `topics.php` | Logged in users |
| Take Quiz | `quiz.php?topic_id=X` | Logged in users |
| Submit Quiz | `quiz-submit.php` | Logged in users |
| View Results | `results.php?result_id=X` | Logged in users |
| View History | `history.php` | Logged in users |

## Database Schema Overview

```
users (id, username, email, password_hash, role, created_at)
  ├── topics (id, name, description, created_at)
  │   └── questions (id, topic_id, question_text, created_at)
  │       └── answers (id, question_id, answer_text, is_correct, created_at)
  └── quiz_results (id, user_id, topic_id, score, total, created_at)
```

## Security Notes

✅ **Implemented:**
- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- Session-based authentication
- Role-based access control
- Email validation

⚠️ **Not Implemented (Add in Production):**
- CSRF token protection
- HTTPS enforcement
- Rate limiting
- Email verification
- Password reset

## Next Steps

1. **Customize Topics**: Replace/add quiz topics for your needs
2. **Style**: Modify `public/css/style.css` for branding
3. **Add More Questions**: Use admin panel to add content
4. **Security**: Add CSRF tokens and HTTPS before production
5. **Analytics**: Track which topics are most popular

## Support Resources

- **PHP**: https://www.php.net/manual/
- **MySQL**: https://dev.mysql.com/doc/
- **PDO**: https://www.php.net/manual/en/book.pdo.php
- **Session**: https://www.php.net/manual/en/book.session.php

---

**Ready to start?** 
1. Ensure MySQL is running
2. Open browser to `http://localhost/zhigis/quiz-tests/views/index.php`
3. Register or login with `admin/admin123`
4. Enjoy! 🎓
