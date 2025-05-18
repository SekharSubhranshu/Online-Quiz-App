<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit;
}

$admin_id = $_SESSION["user_id"];
$result = $conn->prepare("SELECT * FROM quizzes WHERE created_by = ?");
$result->bind_param("i", $admin_id);
$result->execute();
$quizzes = $result->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Quizzes - Online Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4ff;
            margin: 0;
            padding: 40px 20px;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            color: #1e40af;
            margin-bottom: 30px;
        }
        .quiz-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            max-width: 600px;
        }
        .quiz-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
            text-decoration: none;
            color: #1e3a8a;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: box-shadow 0.3s ease, transform 0.2s ease;
        }
        .quiz-card:hover {
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.5);
            transform: translateY(-4px);
        }
        .quiz-card i {
            font-size: 22px;
            color: #3b82f6;
        }
        .back-btn {
            margin-top: 40px;
            background: #3b82f6;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
            transition: background 0.3s ease;
        }
        .back-btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <h2>Quizzes You Created</h2>

    <div class="quiz-list">
        <?php if ($quizzes->num_rows > 0): ?>
            <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                <a href="view_quiz.php?quiz_id=<?= $quiz['id']; ?>" class="quiz-card">
                    <i class="fa fa-clipboard-list"></i>
                    <?= htmlspecialchars($quiz['title']); ?>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No quizzes found.</p>
        <?php endif; ?>
    </div>

    <a href="dashboard.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
</body>
</html>