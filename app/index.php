
<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
include 'config.php';
$user_id = $_SESSION["user_id"];
// prepare and execute statement
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY id DESC");
if ($stmt) {
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $tasks = $stmt->get_result();
} else {
  // fallback to empty result
  $tasks = null;
}

?>


<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <title>لیست کارهای من</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <div class="container">
    <header>
      <h1>لیست کارهای من</h1>
      <a href="logout.php">خروج</a>
    </header>

    <!-- فرم افزودن کار -->
    <form method="POST" action="add.php" class="add-task">
      <input type="text" name="title" placeholder="عنوان کار" required>
      <input type="text" name="description" placeholder="توضیح (اختیاری)">
      <input type="date" name="deadline">
      <button type="submit">+ افزودن کار</button>
    </form>

    <!-- جستجو -->
    <div class="search-box">
      <input type="text" placeholder="جستجوی کارها..." id="searchInput">
    </div>

    <!-- فیلتر -->
    <div class="filters">
      <button class="active">همه</button>
      <button>در انتظار</button>
      <button>انجام‌شده</button>
    </div>

    <!-- لیست کارها -->
    <ul class="task-list">
      <?php if ($tasks && $tasks->num_rows > 0) : ?>
        <?php while ($row = $tasks->fetch_assoc()) :
          $title = htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8');
          $description = htmlspecialchars($row['description'] ?? '', ENT_QUOTES, 'UTF-8');
          $deadline = htmlspecialchars($row['deadline'] ?? '', ENT_QUOTES, 'UTF-8');
          $completed = !empty($row['completed']);
        ?>
        <li class="task<?php echo $completed ? ' completed' : ''; ?>">
          <span class="title"><?php echo $title; ?></span>
          <div class="actions">
            <a href="update.php?id=<?php echo (int) $row['id']; ?>">تغییر وضعیت</a>
            <a href="delete.php?id=<?php echo (int) $row['id']; ?>">حذف</a>
          </div>
          <?php if ($description !== '') : ?>
            <div class="desc"><?php echo $description; ?></div>
          <?php endif; ?>
          <?php if ($deadline !== '') : ?>
            <div class="deadline">مهلت: <?php echo $deadline; ?></div>
          <?php endif; ?>
        </li>
        <?php endwhile; ?>
        <?php if (isset($stmt)) $stmt->close(); ?>
      <?php else : ?>
        <li class="task">
          <span class="title">هنوز کاری ثبت نشده است.</span>
        </li>
      <?php endif; ?>
    </ul>
  </div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchInput");
  const filterButtons = document.querySelectorAll(".filters button");
  const tasks = document.querySelectorAll(".task-list li");

  //  جستجو
  searchInput.addEventListener("input", () => {
    const term = searchInput.value.toLowerCase();
    tasks.forEach(task => {
      const title = task.querySelector(".title").textContent.toLowerCase();
      task.style.display = title.includes(term) ? "flex" : "none";
    });
  });

  //  فیلتر کارها
  filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      filterButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const filter = btn.textContent.trim();
      tasks.forEach(task => {
        if (filter === "همه") {
          task.style.display = "flex";
        } else if (filter === "انجام‌شده") {
          task.style.display = task.classList.contains("completed") ? "flex" : "none";
        } else if (filter === "در انتظار") {
          task.style.display = !task.classList.contains("completed") ? "flex" : "none";
        }
      });
    });
  });
});
</script>

</body>
</html>
