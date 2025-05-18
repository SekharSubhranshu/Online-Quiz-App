<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$quiz_id = $_GET['quiz_id'] ?? null;
$message = "";

if (!$quiz_id) {
    die("Invalid quiz ID.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_text = $_POST["question"];
    $options = $_POST["options"];
    $correct_option = $_POST["correct"];

    // Insert question
    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $stmt->bind_param("is", $quiz_id, $question_text);
    $stmt->execute();
    $question_id = $stmt->insert_id;
    $stmt->close();

    // Insert options
    foreach ($options as $index => $option_text) {
        $is_correct = ($index == $correct_option) ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $question_id, $option_text, $is_correct);
        $stmt->execute();
        $stmt->close();
    }

    $message = "Question added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question - Online Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4ff;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
            width: 90%;
            max-width: 600px;
        }

        h2 {
            color: #1e40af;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            font-weight: 600;
            display: block;
            margin: 15px 0 5px;
        }

        textarea, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 16px;
            background: #f8fafc;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            margin-top: 25px;
            width: 100%;
            background: #3b82f6;
            color: white;
            font-weight: 600;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #2563eb;
        }

        .success {
            margin-top: 15px;
            font-weight: 600;
            color: green;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #3b82f6;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><i class="fa fa-question-circle"></i> Add Question</h2>

        <?php if (!empty($message)): ?>
            <div class="success"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="question">Question:</label>
            <textarea name="question" placeholder="Enter question here..." required></textarea>

            <label>Options:</label>
            <input type="text" name="options[]" placeholder="Option 1" required>
            <input type="text" name="options[]" placeholder="Option 2" required>
            <input type="text" name="options[]" placeholder="Option 3" required>
            <input type="text" name="options[]" placeholder="Option 4" required>

            <label for="correct">Correct Option (0 to 3):</label>
            <input type="number" name="correct" min="0" max="3" required>

            <button type="submit" class="btn"><i class="fa fa-plus"></i> Add Question</button>
        </form>

        <a href="dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </div>

</body>
</html>