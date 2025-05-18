<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

if ($role === "user") {
    $stmt = $conn->prepare("
        SELECT r.id, r.score, q.title
        FROM results r
        JOIN quizzes q ON r.quiz_id = q.id
        WHERE r.user_id = ?
        ORDER BY r.id DESC
    ");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $conn->prepare("
        SELECT r.id, r.score, q.title, u.name
        FROM results r
        JOIN quizzes q ON r.quiz_id = q.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.id DESC
    ");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result History - Online Quiz App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4ff;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
            text-align: center;
        }

        h2 {
            color: #1e40af;
            margin-bottom: 30px;
        }

        .results-table {
            margin: auto;
            border-collapse: collapse;
            width: 90%;
            max-width: 900px;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .results-table th {
            background-color: #3b82f6;
            color: white;
            padding: 12px;
            text-transform: uppercase;
            font-size: 14px;
        }

        .results-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .results-table tr:hover {
            background-color: #f1f5ff;
        }

        .btn-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        .back-link {
            margin-top: 30px;
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
        }

        .back-link:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>

    <h2>Result History</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="results-table">
            <tr>
                <?php if ($role === "admin"): ?>
                    <th>User</th>
                <?php endif; ?>
                <th>Quiz Title</th>
                <th>Score</th>
                <th>Action</th>
            </tr>

            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <?php if ($role === "admin"): ?>
                        <td><?php echo htmlspecialchars($row["name"]); ?></td>
                    <?php endif; ?>
                    <td><?php echo htmlspecialchars($row["title"]); ?></td>
                    <td><?php echo $row["score"]; ?></td>
                    <td><a class="btn-link" href="review_result.php?result_id=<?php echo $row["id"]; ?>"><i class="fa fa-eye"></i> Review</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No results found.</p>
    <?php endif; ?>

    <a class="back-link" href="dashboard.php"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>

</body>
</html>