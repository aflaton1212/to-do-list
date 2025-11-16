<?php

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "todo_app";

// ایجاد اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// بررسی اتصال
if ($conn->connect_error) {
    die("خطا در اتصال به دیتابیس: " . $conn->connect_error);
}
?>
