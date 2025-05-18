# Online Quiz Application

An online quiz system built with **PHP** and **MySQL** that supports student quiz-taking, admin management, results viewing, and more.

## Features

- **Student Panel:**
  - Register and log in
  - Take available quizzes
  - View results and review answers

- **Admin Panel:**
  - Add/edit/delete quizzes, questions, and options
  - View all student results
  - Manage quiz content

- **Security & Roles:**
  - Role-based access (admin/student)
  - Passwords securely hashed
  - Session handling and protection

## Technologies Used

- PHP
- MySQL
- HTML/CSS (Minimal styling)
- Bootstrap (for UI components)
- JavaScript (basic interactivity)

## Folder Structure

├── includes/             # DB connection and shared code
├── take_quiz_test.php    # List of available quizzes 
├── take_quiz.php         # Quiz-taking page 
├── submit_quiz.php       # Handles submissions 
├── results.php           # Shows past quiz results 
├── review_result.php     # View answers of a specific attempt 
├── register.php          # User registration 
├── index.php             # Login page 
├── dashboard.php         # Role-based dashboard 
├── logout.php            # Session logout 
└── README.md             # This file

## Setup Instructions

1. **Upload the project folder** to your web hosting or XAMPP `htdocs` directory.

2. **Import the Database:**
   - Open `phpMyAdmin`
   - Create a new database (e.g., `quiz_app`)
   - Import the provided `quiz_app.sql` file available

3. **Configure Database Connection:**
   - Open `/includes/db.php`
   - Update your database credentials:
     ```php
     $conn = new mysqli("localhost", "root", "", "quiz_app");
     ```

4. **Run the Application:**
   - Open your browser
   - Go to `http://localhost/your-folder-name/`

## Default Admin Credentials (if any)

Email: admin@example.com Password: admin123

## License

This project is for educational purposes and open to modification for learning or internal use.

---

**Thank you for using the Online Quiz App!**


---
