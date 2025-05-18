<?php
session_start();
include("includes/db.php");

// Ensure user is logged in and is a student
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $quiz_id = $_POST["quiz_id"];
    $answers = $_POST["answers"];

    $score = 0;
    $total_questions = count($answers);

    foreach ($answers as $question_id => $selected_option_id) {
        // Check if correct
        $stmt = $conn->prepare("SELECT is_correct FROM options WHERE id = ?");
        $stmt->bind_param("i", $selected_option_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row["is_correct"]) {
                $score++;
            }
        }

        // Store each answer
        $insert = $conn->prepare("INSERT INTO user_answers (user_id, quiz_id, question_id, selected_option_id) VALUES (?, ?, ?, ?)");
        $insert->bind_param("iiii", $user_id, $quiz_id, $question_id, $selected_option_id);
        $insert->execute();
    }

    // Store final result
    $insert_result = $conn->prepare("INSERT INTO results (user_id, quiz_id, score, total_questions) VALUES (?, ?, ?, ?)");
    $insert_result->bind_param("iiii", $user_id, $quiz_id, $score, $total_questions);
    $insert_result->execute();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quiz Submitted</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 100px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #28a745;
        }
        p {
            font-size: 18px;
            margin: 15px 0;
        }
        .thank-you {
            margin-top: 30px;
            color: #555;
            font-style: italic;
        }
        a {
            color: #007bff;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quiz Submitted Successfully!</h2>
        <p><strong>Your Score:</strong> <?php echo $score; ?> / <?php echo $total_questions; ?></p>
        <p class="thank-you">Thank you for appearing in the test!</p>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
<?php
} else {
    echo "Invalid access.";
}
?>