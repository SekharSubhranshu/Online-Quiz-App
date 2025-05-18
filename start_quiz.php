<?php
session_start();
include("includes/db.php");

// Only allow regular users
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: index.php");
    exit;
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    die("Quiz ID not provided.");
}

// Fetch quiz details
$quiz_stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$quiz_stmt->bind_param("i", $quiz_id);
$quiz_stmt->execute();
$quiz_stmt->bind_result($quiz_title);
$quiz_stmt->fetch();
$quiz_stmt->close();

// Fetch questions and options
$questions = [];
$question_stmt = $conn->prepare("SELECT id, question_text FROM questions WHERE quiz_id = ?");
$question_stmt->bind_param("i", $quiz_id);
$question_stmt->execute();
$question_result = $question_stmt->get_result();

while ($question = $question_result->fetch_assoc()) {
    $q_id = $question["id"];
    $question["options"] = [];

    $opt_stmt = $conn->prepare("SELECT id, option_text FROM options WHERE question_id = ?");
    $opt_stmt->bind_param("i", $q_id);
    $opt_stmt->execute();
    $opt_result = $opt_stmt->get_result();

    while ($opt = $opt_result->fetch_assoc()) {
        $question["options"][] = $opt;
    }

    $opt_stmt->close();
    $questions[] = $question;
}
$question_stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($quiz_title); ?> - Online Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2><?php echo htmlspecialchars($quiz_title); ?></h2>
    <form method="POST" action="submit_quiz.php">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

        <?php foreach ($questions as $index => $question): ?>
            <div>
                <p><strong>Q<?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($question['question_text']); ?></p>
                <?php foreach ($question['options'] as $option): ?>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]"
                               value="<?php echo $option['id']; ?>" required>
                        <?php echo htmlspecialchars($option['option_text']); ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
            <hr>
        <?php endforeach; ?>

        <button type="submit">Submit Quiz</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>