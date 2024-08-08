<?php
session_start();
include 'db.php'; // Ensure 'db.php' is in the same directory as 'task.php'

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<a href="login.php">Login</a> | <a href="register.php">Register</a>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Fetch total number of users
$total_users = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total_users = $row['total'];
}
$stmt->close();

// Fetch pending tasks
$pending_tasks = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE completed = 0");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $pending_tasks = $row['total'];
}
$stmt->close();

// Fetch resolved tasks (assuming completed tasks are considered resolved issues)
$resolved_issues = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE completed = 1");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $resolved_issues = $row['total'];
}
$stmt->close();

// Task Creation and Assignment (Only for supervisors)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $intern_id = $_POST['intern_id'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, priority, due_date, supervisor_id, intern_id, completed) VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssssii", $title, $description, $priority, $due_date, $user_id, $intern_id);

    if ($stmt->execute()) {
        echo "Task created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Daily Logs (Only for interns)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['log_activity'])) {
    $task_id = $_POST['task_id'];
    $progress = $_POST['progress'];
    $log_date = date('Y-m-d');

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO logs (intern_id, task_id, log_date, progress) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $task_id, $log_date, $progress);

    if ($stmt->execute()) {
        echo "Activity logged successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Mark task as completed (Only for interns)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_completed'])) {
    $task_id = $_POST['task_id'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE tasks SET completed = 1 WHERE id = ? AND intern_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        echo "Task marked as completed";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System</title>
    <link rel="stylesheet" href="style.css"> <!-- Include the CSS file here -->
</head>
<body>
    <header>
        <h1>Task Management System</h1>
    </header>
    <nav>
        <a href="#">Dashboard</a>
        <a href="#">User Management</a>
        <a href="#">Customer Queries</a>
        <a href="#">Reports</a>
        <a href="#">Settings</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <div class="stats">
            <div>Total Users<br><span><?php echo $total_users; ?></span></div>
            <div>Pending Tasks<br><span><?php echo $pending_tasks; ?></span></div>
            <div>Resolved Issues<br><span><?php echo $resolved_issues; ?></span></div>
        </div>

        <?php if ($user_role == 'supervisor'): ?>
            <h2>Create and Assign Task</h2>
            <form method="POST" action="task.php">
                <input type="hidden" name="create_task" value="1">
                Title: <input type="text" name="title" required><br>
                Description: <textarea name="description"></textarea><br>
                Priority: 
                <select name="priority">
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select><br>
                Due Date: <input type="date" name="due_date" required><br>
                Assign to Intern: 
                <select name="intern_id">
                    <?php
                    // Fetch interns for assignment
                    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role='intern'");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['username']}</option>";
                    }
                    $stmt->close();
                    ?>
                </select><br>
                <input type="submit" value="Create Task">
            </form>

            <h2>View Assigned Tasks</h2>
            <?php
            // Fetch tasks assigned by the supervisor
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE supervisor_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "Title: " . htmlspecialchars($row['title']) . "<br>";
                echo "Description: " . htmlspecialchars($row['description']) . "<br>";
                echo "Priority: " . htmlspecialchars($row['priority']) . "<br>";
                echo "Due Date: " . htmlspecialchars($row['due_date']) . "<br>";
                echo "Completed: " . ($row['completed'] ? 'Yes' : 'No') . "<br><br>";
            }
            $stmt->close();
            ?>
        <?php endif; ?>

        <?php if ($user_role == 'intern'): ?>
            <h2>Log Daily Activity</h2>
            <form method="POST" action="task.php">
                <input type="hidden" name="log_activity" value="1">
                Task: 
                <select name="task_id">
                    <?php
                    // Fetch tasks assigned to the intern
                    $stmt = $conn->prepare("SELECT id, title FROM tasks WHERE intern_id=? AND completed=0");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['title']}</option>";
                    }
                    $stmt->close();
                    ?>
                </select><br>
                Progress: <textarea name="progress" required></textarea><br>
                <input type="submit" value="Log Activity">
            </form>

            <h2>Mark Task as Completed</h2>
            <form method="POST" action="task.php">
                <input type="hidden" name="mark_completed" value="1">
                Task: 
                <select name="task_id">
                    <?php
                    // Fetch tasks assigned to the intern
                    $stmt = $conn->prepare("SELECT id, title FROM tasks WHERE intern_id=? AND completed=0");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['title']}</option>";
                    }
                    $stmt->close();
                    ?>
                </select><br>
                <input type="submit" value="Mark as Completed">
            </form>

            <div class="recent-activities">
                <h2>Recent Activities</h2>
                <?php
                // Fetch recent activities (logs) for the intern
                $stmt = $conn->prepare("SELECT * FROM logs WHERE intern_id = ? ORDER BY log_date DESC LIMIT 25");
                $stmt->bind_param("i", $user_id); // Bind the actual intern ID ($user_id)
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "Date: " . htmlspecialchars($row['log_date']) . "<br>";
                    echo "Progress: " . htmlspecialchars($row['progress']) . "<br><br>";
                }
                $stmt->close();
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
