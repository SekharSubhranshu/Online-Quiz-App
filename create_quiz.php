<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $admin_id = $_SESSION["user_id"];

    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $description, $admin_id);
        if ($stmt->execute()) {
            $quiz_id = $stmt->insert_id;
            header("Location: add_question.php?quiz_id=$quiz_id");
            exit;
        } else {
            $error = "Error saving quiz: " . $stmt->error;
        }
    } else {
        $error = "Quiz title cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz - Online Quiz App</title>
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
            max-width: 500px;
        }

        h2 {
            color: #1e40af;
            margin-bottom: 25px;
            text-align: center;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 15px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 16px;
            background: #f8fafc;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
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

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3b82f6;
            font-weight: 600;
        }

        .error {
            color: red;
            margin-top: 15px;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2><i class="fa fa-pen"></i> Create New Quiz</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="title">Quiz Title:</label>
            <input type="text" name="title" required>

            <label for="description">Description:</label>
            <textarea name="description" placeholder="Brief description of the quiz..."></textarea>

            <button type="submit" class="btn"><i class="fa fa-plus-circle"></i> Create Quiz</button>
        </form>

        <a href="dashboard.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </div>

</body>
</html>