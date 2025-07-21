<?php
include('db.php');  // اتصال قاعدة البيانات

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // استلام البيانات من نموذج التسجيل
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['location']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // التحقق من تطابق كلمة المرور
    if ($password !== $confirm_password) {
        echo "كلمات المرور غير متطابقة!";
        exit;
    }

    // تحقق إذا كان البريد الإلكتروني موجوداً في قاعدة البيانات
    $check_email_sql = "SELECT * FROM customers WHERE cust_email = '$email'";
    $result = $conn->query($check_email_sql);

    if ($result->num_rows > 0) {
        echo "البريد الإلكتروني موجود بالفعل!";
    } else {
        // إدخال البيانات في قاعدة البيانات بدون تشفير كلمة المرور
        $sql = "INSERT INTO customers (cust_name, cust_email, cust_password, cust_phone, address)
                VALUES ('$name', '$email', '$password', '$phone', '$address')";

        if ($conn->query($sql) === TRUE) {
            echo "تم إنشاء الحساب بنجاح!";
            // إعادة التوجيه إلى صفحة تسجيل الدخول أو أي صفحة أخرى
            header("Location: auth.php?success=register");
            exit();
        } else {
            echo "خطأ: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}

session_start();  // بدء الجلسة

include('db.php');  // الاتصال بقاعدة البيانات

// التحقق من إرسال البيانات عبر POST من نموذج تسجيل الدخول
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // استلام البيانات من النموذج
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // استعلام للتحقق من وجود البريد الإلكتروني في قاعدة البيانات
    $sql = "SELECT * FROM customers WHERE cust_email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // جلب بيانات المستخدم
        $row = $result->fetch_assoc();

        // مقارنة كلمة المرور المدخلة مع كلمة المرور المخزنة في قاعدة البيانات
        if ($password === $row['cust_password']) {
            // كلمة المرور صحيحة، تخزين بيانات المستخدم في الجلسة
            $_SESSION['user_id'] = $row['cust_ID'];
            $_SESSION['user_name'] = $row['cust_name'];
            $_SESSION['user_email'] = $row['cust_email'];

            // التوجيه إلى صفحة الملف الشخصي (client.php)
            header("Location: client.php");
            exit();  // تأكد من إنهاء السكربت بعد التوجيه
        } else {
            echo "كلمة المرور غير صحيحة!";
        }
    } else {
        echo "البريد الإلكتروني غير موجود!";
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سهّالة | تسجيل الدخول والتسجيل</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style/register.css">
        <link rel="icon" href="pic/logo.svg" type="image/x-icon">

</head>
<body>
  <div class="auth-container">
    <!-- Logo -->
    <div class="logo">
      <a href="index.html">
       <img src="pic\logo.svg" width="60"></a>
      <h1>سهّالة</h1>
      <p>خدمات منزلية بكل سهولة</p>
    </div>
    
    <!-- Bootstrap Tabs -->
    <ul class="nav nav-tabs" id="authTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">
          تسجيل الدخول
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">
          إنشاء حساب
        </button>
      </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content" id="authTabsContent">
      <!-- Login Tab -->
      <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
        <form method="POST" action="auth.php">
          <div class="form-group">
            <label class="form-label">البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="ادخل بريدك الإلكتروني">
          </div>
          
          <div class="form-group">
            <label class="form-label">كلمة المرور</label>
            <div class="password-container">
              <input type="password" name="password" class="form-control" id="login-password" placeholder="ادخل كلمة المرور">
              <span class="password-toggle" onclick="togglePassword('login-password')">
                <i class="fas fa-eye"></i>
              </span>
            </div>
          </div>
          
          <button type="submit" name="login" class="btn-primary">تسجيل الدخول</button>
        </form>
      </div>
      
      <!-- Register Tab -->
      <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
        <form method="POST" action="auth.php">
            <div class="form-group">
                <label class="form-label">الاسم الكامل</label>
                <input type="text" name="name" class="form-control" placeholder="ادخل اسمك الكامل" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="ادخل بريدك الإلكتروني" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">رقم الجوال</label>
                <input type="tel" name="phone" class="form-control" placeholder="ادخل رقم جوالك" required>
            </div>

            <div class="form-group">
                <label class="form-label">الموقع</label>
                <input type="text" name="location" class="form-control" placeholder="ادخل موقعك هنا" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">كلمة المرور</label>
                <div class="password-container">
                    <input type="password" name="password" class="form-control" id="reg-password" placeholder="أنشئ كلمة مرور" required>
                    <span class="password-toggle" onclick="togglePassword('reg-password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">تأكيد كلمة المرور</label>
                <div class="password-container">
                    <input type="password" name="confirm_password" class="form-control" id="reg-confirm-password" placeholder="أعد إدخال كلمة المرور" required>
                    <span class="password-toggle" onclick="togglePassword('reg-confirm-password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <button type="submit" name="register" class="btn-primary">إنشاء حساب</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js.js"></script>
</body>
</html>
