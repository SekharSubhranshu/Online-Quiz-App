<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];
$result_id = $_GET['result_id'] ?? null;

if (!$result_id) {
    die("Result ID is required.");
}

// Verify ownership
if ($role === "user") {
    $stmt = $conn->prepare("SELECT user_id FROM results WHERE id = ?");
    $stmt->bind_param("i", $result_id);
    $stmt->execute();
    $stmt->bind_result($owner_id);
    $stmt->fetch();
    $stmt->close();

    if ($owner_id != $user_id) {
        die("You do not have permission to view this result.");
    }
}

// Get quiz info
$stmt = $conn->prepare("
    SELECT q.id, q.title
    FROM results r
    JOIN quizzes q ON r.quiz_id = q.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $result_id);
$stmt->execute();
$stmt->bind_result($quiz_id, $quiz_title);
$stmt->fetch();
$stmt->close();

// Get quiz taker's user_id
$stmt = $conn->prepare("SELECT user_id FROM results WHERE id = ?");
$stmt->bind_param("i", $result_id);
$stmt->execute();
$stmt->bind_result($quiz_user_id);
$stmt->fetch();
$stmt->close();

// Get user answers and correct answers
$query = "
    SELECT qs.question_text, ua.selected_option_id, op.option_text AS selected_text,
           (SELECT option_text FROM options WHERE question_id = qs.id AND is_correct = 1) AS correct_text,
           (SELECT id FROM options WHERE question_id = qs.id AND is_correct = 1) AS correct_option_id
    FROM user_answers ua
    JOIN questions qs ON ua.question_id = qs.id
    JOIN options op ON ua.selected_option_id = op.id
    WHERE ua.user_id = ? AND ua.quiz_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $quiz_user_id, $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
$correct_count = 0;

while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
    if ($row['selected_option_id'] == $row['correct_option_id']) {
        $correct_count++;
    }
}

$total_questions = count($questions);
$score_percent = $total_questions > 0 ? round(($correct_count / $total_questions) * 100) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review Result - Online Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f4f6fc;
            padding: 30px;
            color: #1e293b;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #1d4ed8;
        }

        .summary {
            background-color: #e0f2fe;
            padding: 15px;
            border-left: 6px solid #0ea5e9;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .summary p {
            margin: 5px 0;
            font-weight: 500;
        }

        .question-block {
            margin-top: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #d1d5db;
        }

        .correct {
            background-color: #d1fae5;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            color: #065f46;
            font-weight: bold;
            margin-top: 5px;
        }

        .wrong {
            background-color: #fee2e2;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            color: #991b1b;
            font-weight: bold;
            margin-top: 5px;
        }

        a {
            text-decoration: none;
            color: #2563eb;
            font-weight: 600;
        }

        .links {
            text-align: center;
            margin-top: 30px;
        }

        .links a {
            margin: 0 10px;
        }

        .label {
            font-weight: 600;
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fa fa-file-alt"></i> Review: <?php echo htmlspecialchars($quiz_title); ?></h2>

        <div class="summary">
            <p>Total Questions: <?php echo $total_questions; ?></p>
            <p>Correct Answers: <?php echo $correct_count; ?></p>
            <p>Score: <?php echo $score_percent; ?>%</p>
        </div>

        <?php if ($total_questions > 0): ?>
            <?php foreach ($questions as $row): ?>
                <div class="question-block">
                    <p class="label">Question:</p>
                    <p><?php echo htmlspecialchars($row['question_text']); ?></p>

                    <p class="label">Your Answer:</p>
                    <div class="<?php echo ($row['selected_option_id'] == $row['correct_option_id']) ? 'correct' : 'wrong'; ?>">
                        <?php echo htmlspecialchars($row['selected_text']); ?>
                    </div>

                    <?php if ($row['selected_option_id'] != $row['correct_option_id']): ?>
                        <p class="label">Correct Answer:</p>
                        <div class="correct"><?php echo htmlspecialchars($row['correct_text']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No answers found for this quiz result.</p>
        <?php endif; ?>

        <div class="links">
            <a href="results.php"><i class="fa fa-history"></i> Result History</a>
            <a href="dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
        </div>
    </div>

</body>
</html>