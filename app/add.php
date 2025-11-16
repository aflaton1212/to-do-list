<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_id"])) {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $user_id = $_SESSION["user_id"];

    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $description, $user_id);
        $stmt->execute();
    }
}
header("Location: index.php");
exit;
?>
