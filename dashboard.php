<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$name = $_SESSION["name"];
$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - Online Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4ff;
            margin: 0;
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #1e293b;
        }
        h2 {
            color: #1e40af;
            margin-bottom: 5px;
        }
        p.role {
            font-size: 18px;
            margin-bottom: 40px;
            color: #334155;
        }
        .role span {
            font-weight: 600;
            color: #3b82f6;
        }
        .card {
            background: white;
            width: 320px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
            margin-bottom: 20px;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: #3b82f6;
            font-weight: 600;
            transition: box-shadow 0.3s ease, transform 0.2s ease;
        }
        .card:hover {
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.5);
            transform: translateY(-4px);
        }
        .card i {
            font-size: 24px;
        }
        .logout {
            background: #ef4444;
            color: white !important;
            justify-content: center;
            font-weight: 700;
            border-radius: 10px;
            margin-top: 40px;
            width: 320px;
            padding: 15px 0;
            box-shadow: 0 3px 10px rgba(239, 68, 68, 0.4);
            transition: background 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logout:hover {
            background: #dc2626;
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.6);
            transform: translateY(-4px);
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <p class="role">You are logged in as <span><?php echo htmlspecialchars($role); ?></span>.</p>

    <?php if ($role == "admin"): ?>
        <a href="create_quiz.php" class="card"><i class="fa fa-plus-circle"></i> Create a New Quiz</a>
        <a href="view_quizzes.php" class="card"><i class="fa fa-list"></i> View Your Quizzes</a>
    <?php else: ?>
        <a href="take_quiz_list.php" class="card"><i class="fa fa-play"></i> Take a Quiz</a>
    <?php endif; ?>

    <a href="results.php" class="card"><i class="fa fa-chart-bar"></i> View Result History</a>

    <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>

</body>
</html>