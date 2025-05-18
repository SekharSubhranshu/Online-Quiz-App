<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit;
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    echo "Quiz ID is missing.";
    exit;
}

// Fetch quiz title
$quiz_stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$quiz_stmt->bind_param("i", $quiz_id);
$quiz_stmt->execute();
$quiz_result = $quiz_stmt->get_result();
$quiz = $quiz_result->fetch_assoc();

if (!$quiz) {
    echo "Quiz not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Quiz - Online Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f4f6fc;
            color: #1e293b;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #1e40af;
            margin-bottom: 20px;
        }

        .question {
            margin-top: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .question p {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .option {
            padding: 10px 15px;
            border-radius: 6px;
            background: #f1f5f9;
            margin: 5px 0;
        }

        .correct {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: bold;
            border: 1px solid #10b981;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            color: #3b82f6;
            font-weight: 600;
        }

        hr {
            border: none;
            border-top: 1px solid #cbd5e1;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fa fa-eye"></i> Quiz: <?php echo htmlspecialchars($quiz['title']); ?></h2>

        <?php
        $question_stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
        $question_stmt->bind_param("i", $quiz_id);
        $question_stmt->execute();
        $question_result = $question_stmt->get_result();

        $question_number = 1;
        while ($question = $question_result->fetch_assoc()):
        ?>
            <div class="question">
                <p>Q<?php echo $question_number++; ?>: <?php echo htmlspecialchars($question['question_text']); ?></p>

                <?php
                $q_id = $question['id'];
                $option_stmt = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
                $option_stmt->bind_param("i", $q_id);
                $option_stmt->execute();
                $option_result = $option_stmt->get_result();

                while ($option = $option_result->fetch_assoc()):
                    $is_correct = $option['is_correct'] == 1;
                ?>
                    <div class="option <?php echo $is_correct ? 'correct' : ''; ?>">
                        <?php echo htmlspecialchars($option['option_text']); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endwhile; ?>

        <a href="dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </div>

</body>
</html>