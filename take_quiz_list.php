<?php
session_start();
include("includes/db.php");

// Only allow students
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Quizzes - Online Quiz App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f1f5f9;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #1d4ed8;
            margin-bottom: 30px;
        }

        .quiz-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 20px 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .quiz-details {
            max-width: 70%;
        }

        .quiz-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
        }

        .quiz-description {
            color: #475569;
            font-style: italic;
            margin-top: 4px;
        }

        .quiz-action a {
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .quiz-action a:hover {
            background-color: #1e40af;
        }

        .back-link {
            text-align: center;
            margin-top: 40px;
        }

        .back-link a {
            color: #2563eb;
            font-weight: 500;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h2>Available Quizzes</h2>

    <?php
    $result = $conn->query("SELECT * FROM quizzes");

    if ($result && $result->num_rows > 0):
        while ($quiz = $result->fetch_assoc()):
    ?>
        <div class="quiz-card">
            <div class="quiz-details">
                <div class="quiz-title"><?php echo htmlspecialchars($quiz['title']); ?></div>
                <?php if (!empty($quiz['description'])): ?>
                    <div class="quiz-description"><?php echo htmlspecialchars($quiz['description']); ?></div>
                <?php endif; ?>
            </div>
            <div class="quiz-action">
                <a href="take_quiz.php?quiz_id=<?php echo $quiz['id']; ?>">Take Quiz</a>
            </div>
        </div>
    <?php
        endwhile;
    else:
        echo "<p style='text-align:center;'>No quizzes available at the moment.</p>";
    endif;
    ?>

    <div class="back-link">
        <p><a href="dashboard.php">← Back to Dashboard</a></p>
    </div>

</body>
</html>