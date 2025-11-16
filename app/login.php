<?php
include 'config.php';
session_start();

// preserve username for redisplay on error
$username_value = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"] ?? '');
  $password = trim($_POST["password"] ?? '');

  // safe value for form
  $username_value = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

  if ($username === '' || $password === '') {
    $error = "لطفاً همه فیلدها را پر کنید.";
  } else {
    // prepare case-insensitive lookup
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE LOWER(username) = LOWER(?)");
    if ($stmt) {
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
          $_SESSION["user_id"] = $row["id"];
          $_SESSION["username"] = $username;
          $stmt->close();
          header("Location: index.php");
          exit;
        } else {
          $error = "رمز عبور اشتباه است!";
          $stmt->close();
        }
      } else {
        $error = "نام کاربری یافت نشد!";
        $stmt->close();
      }
    } else {
      $error = "خطا در اتصال به پایگاه‌داده.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <title>ورود به حساب</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="auth-box">
    <h1>ورود به سیستم</h1>

    <?php if (!empty($error ?? null)) : ?>
      <p class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="username" placeholder="نام کاربری" value="<?php echo $username_value; ?>" required>
      <input type="password" name="password" placeholder="رمز عبور" required>
      <button type="submit">ورود</button>
    </form>

    <p>هنوز حسابی ندارید؟ <a href="register.php">ثبت‌نام کنید</a></p>
  </div>
</body>
</html>
