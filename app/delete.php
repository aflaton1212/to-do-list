<?php
include 'config.php';

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: index.php");
exit;
?>
