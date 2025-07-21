<?php
session_start();  // بدء الجلسة

include('db.php');  // الاتصال بقاعدة البيانات

// التحقق من وجود المستخدم في الجلسة
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // إعادة التوجيه إلى صفحة تسجيل الدخول إذا لم يكن المستخدم مسجلاً دخوله
    exit();
}

$user_id = $_SESSION['user_id'];  // الحصول على الـ user_id من الجلسة

// استعلام لجلب بيانات العميل
$user_sql = "SELECT * FROM customers WHERE cust_ID = '$user_id'";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// استعلام لجلب الطلبات السابقة
$orders_sql = "SELECT r.req_ID, r.service_type_ID, r.pro_ID, r.price, r.description, r.req_statu_ID, r.created_at, r.image_path, 
               s.service_name, ps.pro_name, rs.status_name
               FROM requests r
               JOIN service_types s ON r.service_type_ID = s.service_type_ID
               JOIN service_providers ps ON r.pro_ID = ps.pro_ID
               JOIN req_statu rs ON r.req_statu_ID = rs.req_statu_ID
               WHERE r.cust_ID = '$user_id'
               ORDER BY r.created_at DESC";
$orders_result = $conn->query($orders_sql);

// تحقق من وجود خطأ في الاستعلام
if ($orders_result === false) {
    echo "حدث خطأ في الاستعلام: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سهّالة | ملف العميل</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style/client.css">
        <link rel="icon" href="pic/logo.svg" type="image/x-icon">

</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <a href="index.html" class="logo">
          <img src="pic\logo.svg" width="47">
          <span>سهّالة</span>
        </a>
        <div>
          <a href="logout.php" class="btn btn-logout">
            <i class="fas fa-sign-out-alt"></i> تسجيل خروج
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="container profile-container">
    <!-- Profile Card -->
    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-pic">
          <i class="fas fa-user"></i>
        </div>
        <h3><?php echo $user['cust_name']; ?></h3>
        <p>عميل مميز منذ <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
      </div>
      
      <div class="profile-info">
        <div class="info-grid">
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-user"></i>
            </div>
            <div class="info-content">
              <h4>الاسم الكامل</h4>
              <p><?php echo $user['cust_name']; ?></p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
              <h4>البريد الإلكتروني</h4>
              <p><?php echo $user['cust_email']; ?></p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
              <h4>الموقع</h4>
              <p><?php echo $user['address']; ?></p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="info-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
              <h4>رقم الجوال</h4>
              <p><?php echo $user['cust_phone']; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Orders Card -->
    <div class="orders-card">
      <h3 class="card-title">الطلبات السابقة</h3>
      
      <div class="order-list">
        <?php if ($orders_result->num_rows > 0): ?>
          <?php while ($order = $orders_result->fetch_assoc()): ?>
            <div class="order-item">
              <div class="order-header">
                <div class="order-icon">
                  <i class="fas fa-bolt"></i>
                </div>
                <div>
                  <div class="order-title"><?php echo $order['service_name']; ?></div>
                  <div class="order-id">#<?php echo $order['req_ID']; ?></div>
                </div>
              </div>
              <p><?php echo $order['description']; ?></p>
              <span class="status <?php echo ($order['status_name'] == 'مكتمل') ? 'status-completed' : 'status-pending'; ?>">
                <?php echo ucfirst($order['status_name']); ?>
              </span>
              
              <div class="order-details">
                <div class="order-date"><?php echo date('d F Y', strtotime($order['created_at'])); ?></div>
                <div class="order-price"><?php echo $order['price']; ?> ريال</div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>لا توجد طلبات سابقة.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p>© 2023 سهّالة. جميع الحقوق محفوظة</p>
      <p>صمم بكل ❤️ لتسهيل حياتك</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
