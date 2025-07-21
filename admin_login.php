<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // In a real application, validate against database
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول للمسؤول</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="admin_login.css">
         <link rel="icon" href="pic/logo.svg" type="image/x-icon">

</head>
<body>
  <div class="card login-card">
    <div class="card-header">
      <h4>تسجيل دخول المسؤول</h4>
    </div>
    <div class="card-body p-4">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">اسم المستخدم</label>
          <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
          <label class="form-label">كلمة المرور</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
      </form>
    </div>
  </div>
</body>
</html>
<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // In a real application, validate against database
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول للمسؤول</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --dark-blue: #1A4170;
      --green: #29DF64;
      --light-green: rgba(41, 223, 100, 0.1);
      --light-gray: #f8f9fa;
    }

    body {
      font-family: 'Tajawal', sans-serif;
      background-color: var(--light-gray);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      width: 100%;
      max-width: 400px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .card-header {
      background-color: var(--dark-blue);
      color: white;
      text-align: center;
      padding: 1.2rem;
    }

    .btn-primary {
      background-color: var(--green);
      border-color: var(--green);
    }

    .btn-primary:hover {
      background-color: #22c658;
      border-color: #22c658;
    }

    .form-control:focus {
      border-color: var(--green);
      box-shadow: 0 0 0 0.2rem var(--light-green);
    }

    .alert-danger {
      background-color: #ffefef;
      color: #c82333;
      border-color: #f5c6cb;
    }
  </style>
</head>
<body>
  <div class="card login-card">
    <div class="card-header">
      <h4>تسجيل دخول المسؤول</h4>
    </div>
    <div class="card-body p-4">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">اسم المستخدم</label>
          <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
          <label class="form-label">كلمة المرور</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
      </form>
    </div>
  </div>
</body>
</html>
