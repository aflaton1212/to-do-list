<?php
include 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $task_id = (int) $_GET['id'];
    $user_id = $_SESSION["user_id"];

    // وضعیت فعلی را بخوان
    $stmt = $conn->prepare("SELECT completed FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
        $newStatus = $task['completed'] ? 0 : 1;

        // تغییر وضعیت
        $update = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("iii", $newStatus, $task_id, $user_id);
        $update->execute();
    }
}
header("Location: index.php");
exit;
