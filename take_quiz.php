<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: index.php");
    exit;
}

if (!isset($_GET["quiz_id"])) {
    echo "Quiz ID is missing in the URL.";
    exit;
}

$quiz_id = $_GET["quiz_id"];
$user_id = $_SESSION["user_id"];

$quiz_stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
$quiz_stmt->bind_param("i", $quiz_id);
$quiz_stmt->execute();
$quiz_result = $quiz_stmt->get_result();

if ($quiz_result->num_rows != 1) {
    echo "Quiz not found.";
    exit;
}

$quiz = $quiz_result->fetch_assoc();

$questions_stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$questions_stmt->bind_param("i", $quiz_id);
$questions_stmt->execute();
$questions_result = $questions_stmt->get_result();

$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}
$total_questions = count($questions);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($quiz['title']); ?> - Take Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f3f4f6;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #1d4ed8;
            margin-bottom: 30px;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        }

        .question-block {
            margin-bottom: 30px;
        }

        .question-title {
            font-weight: bold;
            margin-bottom: 12px;
            color: #111827;
        }

        .option-label {
            display: block;
            margin-bottom: 8px;
            background: #f9fafb;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .option-label:hover {
            background-color: #e0f2fe;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        input[type="submit"] {
            background-color: #2563eb;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #1e40af;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .progress-container {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 8px;
            margin-bottom: 25px;
            height: 18px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: #3b82f6;
            width: 0%;
            transition: width 0.3s ease;
        }

        .progress-text {
            text-align: right;
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

    <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>

    <form action="submit_quiz.php" method="post" id="quizForm">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

        <div class="progress-text">
            Progress: <span id="progress-count">0</span> / <?php echo $total_questions; ?>
        </div>
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>

        <?php
        $question_number = 1;
        foreach ($questions as $question):
            $question_id = $question['id'];

            $options_stmt = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
            $options_stmt->bind_param("i", $question_id);
            $options_stmt->execute();
            $options_result = $options_stmt->get_result();
        ?>
            <div class="question-block">
                <div class="question-title">
                    <?php echo $question_number . '. ' . htmlspecialchars($question['question_text']); ?>
                </div>

                <?php while ($option = $options_result->fetch_assoc()): ?>
                    <label class="option-label">
                        <input type="radio" name="answers[<?php echo $question_id; ?>]" value="<?php echo $option['id']; ?>" required>
                        <?php echo htmlspecialchars($option['option_text']); ?>
                    </label>
                <?php endwhile; ?>
            </div>
        <?php
        $question_number++;
        endforeach;
        ?>

        <input type="submit" value="Submit Quiz">
    </form>

    <div class="back-link">
        <p><a href="dashboard.php">← Back to Dashboard</a></p>
    </div>

    <script>
        const totalQuestions = <?php echo $total_questions; ?>;
        const progressBar = document.getElementById('progress-bar');
        const progressText = document.getElementById('progress-count');

        const updateProgress = () => {
            const answered = document.querySelectorAll('input[type=radio]:checked').length;
            const percentage = (answered / totalQuestions) * 100;
            progressBar.style.width = percentage + "%";
            progressText.textContent = answered;
        };

        document.querySelectorAll('input[type=radio]').forEach(input => {
            input.addEventListener('change', updateProgress);
        });
    </script>

</body>
</html>