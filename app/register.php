<?php
include 'config.php';
session_start();

// initialize preserved username value for form
$username_value = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"] ?? '');
  $password = trim($_POST["password"] ?? '');

  // preserve safe version for redisplay
  $username_value = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

  if ($username === '' || $password === '') {
    $error = "لطفاً همه فیلدها را پر کنید.";
  } else {
    // basic username checks (length and no whitespace)
    if (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
      $error = "نام کاربری باید حداقل 3 و حداکثر 50 نویسه باشد.";
    } elseif (preg_match('/\s/u', $username)) {
      $error = "نام کاربری نباید فاصله داشته باشد.";
    } elseif (mb_strlen($password) < 6) {
      $error = "رمز عبور باید حداقل 6 نویسه باشد.";
    } else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // بررسی یکتایی نام کاربری به‌صورت بدون حساسیت به حروف بزرگ/کوچک
      $check = $conn->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(?)");
      if ($check) {
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result && $result->num_rows > 0) {
          $error = "❌ نام کاربری قبلاً وجود دارد!";
          $check->close();
        } else {
          // ثبت کاربر جدید
          $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
          if ($stmt) {
            $stmt->bind_param("ss", $username, $hashedPassword);
            if ($stmt->execute()) {
              $_SESSION["user_id"] = $conn->insert_id;
              $_SESSION["username"] = $username;
              $stmt->close();
              $check->close();
              header("Location: index.php");
              exit;
            } else {
              // generic message so DB internals are not leaked
              $error = "خطا در ثبت‌نام. دوباره تلاش کنید.";
              $stmt->close();
              $check->close();
            }
          } else {
            $error = "خطا در آماده‌سازی پرس‌وجو. لطفاً بعداً تلاش کنید.";
            $check->close();
          }
        }
      } else {
        $error = "خطا در اتصال به پایگاه‌داده.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <title>ثبت‌نام</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="auth-box">
  <h1>ثبت‌نام کاربر جدید</h1>
  <?php if (!empty(
$error ?? null
)) : ?>
    <p class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="نام کاربری" value="<?php echo $username_value; ?>" required>
    <input type="password" name="password" placeholder="رمز عبور" required>
    <button type="submit">ثبت‌نام</button>
  </form>

    <p>حساب دارید؟ <a href="login.php">ورود</a></p>
  </div>
</body>
</html>
