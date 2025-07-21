<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Database connection
$servername = "sql309.infinityfree.com";
$username   = "if0_39494302";
$password   = "TOxVuLkZez";
$dbname     = "if0_39494302_sahalh";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle customer operations
    if (isset($_POST['add_customer'])) {
        $stmt = $pdo->prepare("INSERT INTO customers (cust_name, cust_email, cust_password, cust_phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['cust_name'],
            $_POST['cust_email'],
            password_hash($_POST['cust_password'], PASSWORD_DEFAULT),
            $_POST['cust_phone'],
            $_POST['address']
        ]);
    }
    elseif (isset($_POST['update_customer'])) {
        $stmt = $pdo->prepare("UPDATE customers SET cust_name=?, cust_email=?, cust_phone=?, address=? WHERE cust_ID=?");
        $stmt->execute([
            $_POST['cust_name'],
            $_POST['cust_email'],
            $_POST['cust_phone'],
            $_POST['address'],
            $_POST['cust_id']
        ]);
    }
    elseif (isset($_POST['delete_customer'])) {
        $stmt = $pdo->prepare("DELETE FROM customers WHERE cust_ID=?");
        $stmt->execute([$_POST['cust_id']]);
    }
    
    // Handle provider operations
    elseif (isset($_POST['add_provider'])) {
        $stmt = $pdo->prepare("INSERT INTO service_providers (pro_name, pro_phone, service, price, statu) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['pro_name'],
            $_POST['pro_phone'],
            $_POST['service'],
            $_POST['price'],
            $_POST['statu']
        ]);
    }
    elseif (isset($_POST['update_provider'])) {
        $stmt = $pdo->prepare("UPDATE service_providers SET pro_name=?, pro_phone=?, service=?, price=?, statu=? WHERE pro_ID=?");
        $stmt->execute([
            $_POST['pro_name'],
            $_POST['pro_phone'],
            $_POST['service'],
            $_POST['price'],
            $_POST['statu'],
            $_POST['pro_id']
        ]);
    }
    elseif (isset($_POST['delete_provider'])) {
        $stmt = $pdo->prepare("DELETE FROM service_providers WHERE pro_ID=?");
        $stmt->execute([$_POST['pro_id']]);
    }
    
    // Handle request operations
    elseif (isset($_POST['update_request'])) {
        $stmt = $pdo->prepare("UPDATE requests SET req_statu_ID=? WHERE req_ID=?");
        $stmt->execute([
            $_POST['status'],
            $_POST['req_id']
        ]);
    }
    elseif (isset($_POST['delete_request'])) {
        $stmt = $pdo->prepare("DELETE FROM requests WHERE req_ID=?");
        $stmt->execute([$_POST['req_id']]);
    }
}

// Fetch data for dashboard
$total_money = $pdo->query("SELECT SUM(price) FROM requests")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$total_providers = $pdo->query("SELECT COUNT(*) FROM service_providers")->fetchColumn();
$total_requests = $pdo->query("SELECT COUNT(*) FROM requests")->fetchColumn();

// Fetch data for tables
$customers = $pdo->query("SELECT * FROM customers")->fetchAll();
$providers = $pdo->query("SELECT * FROM service_providers")->fetchAll();
$requests = $pdo->query("SELECT r.*, s.status_name 
                         FROM requests r 
                         JOIN req_statu s ON r.req_statu_ID = s.req_statu_ID")->fetchAll();
$service_types = $pdo->query("SELECT * FROM service_types")->fetchAll();
$statuses = $pdo->query("SELECT * FROM req_statu")->fetchAll();
?>



<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم سهّـاله - الإدارة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet"/>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
      <link rel="icon" href="pic/logo.svg" type="image/x-icon">

    <link rel="stylesheet" href="style/admin.css">
    <style>
    
    </style>
</head>
<body>
    <!-- Fixed Navigation Bar -->
    <nav class="admin-navbar">
        <div class="logo-container">
            <div class="logo"><img src="pic/logo.svg" width="45"></div>
            <div class="logo-text">
                <h2>سهّـاله</h2>
            </div>
        </div>
        
        <div class="nav-tabs">
            <div class="nav-tab active" data-tab="dashboard">
                <i class="fas fa-tachometer-alt"></i> لوحة التحكم
            </div>
            <div class="nav-tab" data-tab="customers">
                <i class="fas fa-users"></i> العملاء
            </div>
            <div class="nav-tab" data-tab="providers">
                <i class="fas fa-user-tie"></i> مقدمي الخدمات
            </div>
            <div class="nav-tab" data-tab="requests">
                <i class="fas fa-tasks"></i> الطلبات
            </div>
        </div>
        
        <div class="admin-actions">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="بحث...">
            </div>
            
            <!-- <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=المسؤول&background=1A4170&color=fff" alt="صورة المستخدم">
                <div class="user-info">
                    <span class="name">Admin</span>
                </div>
            </div> -->
            <div class="logout">
            <a href="admin_logout.php">            
            <button class="logout-btn">
                <!-- <i class="bi bi-box-arrow-left" style="color: #f5f5f5;" ></i> -->
            <i class="fa-solid fa-right-from-bracket fa-xl" style="color: #f5f5f5;"></i>
            </button></a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="admin-container">
        <!-- Dashboard Tab -->
        <div class="content-tab active" id="dashboard-tab">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="stat-content">
                        <h3><?= number_format($total_money, 2) ?> ر.س</h3>
                        <p>إجمالي الأرباح</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-content">
                        <h3><?= $total_customers ?></h3>
                        <p>إجمالي العملاء</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                    <div class="stat-content">
                        <h3><?= $total_providers ?></h3>
                        <p>مقدمو الخدمات</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                    <div class="stat-content">
                        <h3><?= $total_requests ?></h3>
                        <p>الطلبات الجديدة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Tab -->
        <div class="content-tab" id="customers-tab">
            <div class="section-title">
                <h2>إدارة العملاء</h2>
                <button class="btn btn-primary" id="add-customer-btn">
                    <i class="fas fa-plus"></i> عميل جديد
                </button>
            </div>
            
            <div class="data-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>العنوان</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td>#<?= $customer['cust_ID'] ?></td>
                            <td><?= $customer['cust_name'] ?></td>
                            <td><?= $customer['cust_email'] ?></td>
                            <td><?= $customer['cust_phone'] ?></td>
                            <td><?= $customer['address'] ?></td>
                            <td><?= date('Y-m-d', strtotime($customer['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" data-id="<?= $customer['cust_ID'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="cust_id" value="<?= $customer['cust_ID'] ?>">
                                        <button type="submit" name="delete_customer" class="action-btn delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Service Providers Tab -->
        <div class="content-tab" id="providers-tab">
            <div class="section-title">
                <h2>إدارة مقدمي الخدمات</h2>
                <button class="btn btn-primary" id="add-provider-btn">
                    <i class="fas fa-plus"></i> مقدم جديد
                </button>
            </div>
            
            <div class="data-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>الخدمة</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($providers as $provider): ?>
                        <tr>
                            <td>#<?= $provider['pro_ID'] ?></td>
                            <td><?= $provider['pro_name'] ?></td>
                            <td><?= $provider['pro_phone'] ?></td>
                            <td><?= $provider['service'] ?></td>
                            <td><?= $provider['price'] ?> ر.س</td>
                            <td>
                                <span class="status <?= $provider['statu'] === 'متاح' ? 'active' : 'inactive' ?>">
                                    <?= $provider['statu'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" data-id="<?= $provider['pro_ID'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="pro_id" value="<?= $provider['pro_ID'] ?>">
                                        <button type="submit" name="delete_provider" class="action-btn delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Requests Tab -->
        <div class="content-tab" id="requests-tab">
            <div class="section-title">
                <h2>إدارة الطلبات</h2>
                <button class="btn btn-primary">
                    <i class="fas fa-filter"></i> تصفية النتائج
                </button>
            </div>
            
            <div class="data-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>الخدمة</th>
                            <th>العميل</th>
                            <th>مقدم الخدمة</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): 
                            $status_class = '';
                            switch ($request['status_name']) {
                                case 'مكتمل': $status_class = 'active'; break;
                                case 'قيد المعالجة': $status_class = 'pending'; break;
                                case 'ملغي': $status_class = 'inactive'; break;
                            }
                        ?>
                        <tr>
                            <td>#<?= $request['req_ID'] ?></td>
                            <td>
                                <?php 
                                $service_type = array_filter($service_types, function($st) use ($request) {
                                    return $st['service_type_ID'] == $request['service_type_ID'];
                                });
                                echo !empty($service_type) ? reset($service_type)['service_name'] : 'غير محدد';
                                ?>
                            </td>
                            <td><?= $request['cust_name'] ?></td>
                            <td>
                                <?php 
                                $provider = array_filter($providers, function($p) use ($request) {
                                    return $p['pro_ID'] == $request['pro_ID'];
                                });
                                echo !empty($provider) ? reset($provider)['pro_name'] : 'غير محدد';
                                ?>
                            </td>
                            <td><?= $request['price'] ?> ر.س</td>
                            <td>
                                <span class="status <?= $status_class ?>"><?= $request['status_name'] ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" data-id="<?= $request['req_ID'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="req_id" value="<?= $request['req_ID'] ?>">
                                        <button type="submit" name="delete_request" class="action-btn delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div class="modal" id="customer-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">إدارة العميل</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form method="post" id="customer-form">
                <div class="modal-body">
                    <input type="hidden" name="cust_id" id="cust_id">
                    <div class="form-group">
                        <label class="form-label">الاسم الكامل</label>
                        <input type="text" class="form-control" name="cust_name" id="cust_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" name="cust_email" id="cust_email" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" name="cust_password" id="cust_password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" name="cust_phone" id="cust_phone" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">العنوان</label>
                        <input type="text" class="form-control" name="address" id="address" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close-modal">إلغاء</button>
                    <button type="submit" class="btn btn-success" name="update_customer" id="customer-submit-btn">حفظ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Provider Modal -->
    <div class="modal" id="provider-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">إدارة مقدم الخدمة</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form method="post" id="provider-form">
                <div class="modal-body">
                    <input type="hidden" name="pro_id" id="pro_id">
                    <div class="form-group">
                        <label class="form-label">الاسم</label>
                        <input type="text" class="form-control" name="pro_name" id="pro_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">الهاتف</label>
                        <input type="text" class="form-control" name="pro_phone" id="pro_phone" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">الخدمة</label>
                        <input type="text" class="form-control" name="service" id="service" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">السعر (ر.س)</label>
                        <input type="number" class="form-control" name="price" id="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">الحالة</label>
                        <select class="form-control" name="statu" id="statu" required>
                            <option value="متاح">متاح</option>
                            <option value="غير متاح">غير متاح</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close-modal">إلغاء</button>
                    <button type="submit" class="btn btn-success" name="update_provider" id="provider-submit-btn">حفظ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Request Status Modal -->
    <div class="modal" id="request-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">تغيير حالة الطلب</h3>
                <button class="close-modal">&times;</button>
            </div>
            <form method="post" id="request-form">
                <div class="modal-body">
                    <input type="hidden" name="req_id" id="req_id">
                    <div class="form-group">
                        <label class="form-label">حالة الطلب</label>
                        <select class="form-control" name="status" id="request-status" required>
                            <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['req_statu_ID'] ?>"><?= $status['status_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close-modal">إلغاء</button>
                    <button type="submit" class="btn btn-success" name="update_request">تحديث الحالة</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.dataset.tab;
                document.querySelectorAll('.content-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });
        
        // Modal functionality
        const modals = document.querySelectorAll('.modal');
        const closeButtons = document.querySelectorAll('.close-modal');
        
        // Open customer modal
        document.getElementById('add-customer-btn').addEventListener('click', function() {
            document.getElementById('customer-form').reset();
            document.getElementById('cust_id').value = '';
            document.getElementById('customer-submit-btn').name = 'add_customer';
            document.getElementById('customer-modal').classList.add('active');
        });
        
        // Open provider modal
        document.getElementById('add-provider-btn').addEventListener('click', function() {
            document.getElementById('provider-form').reset();
            document.getElementById('pro_id').value = '';
            document.getElementById('provider-submit-btn').name = 'add_provider';
            document.getElementById('provider-modal').classList.add('active');
        });
        
        // Edit customer
        document.querySelectorAll('#customers-tab .action-btn.edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const custId = this.dataset.id;
                const customer = <?= json_encode($customers) ?>.find(c => c.cust_ID == custId);
                
                if (customer) {
                    document.getElementById('cust_id').value = customer.cust_ID;
                    document.getElementById('cust_name').value = customer.cust_name;
                    document.getElementById('cust_email').value = customer.cust_email;
                    document.getElementById('cust_phone').value = customer.cust_phone;
                    document.getElementById('address').value = customer.address;
                    
                    document.getElementById('customer-submit-btn').name = 'update_customer';
                    document.getElementById('customer-modal').classList.add('active');
                }
            });
        });
        
        // Edit provider
        document.querySelectorAll('#providers-tab .action-btn.edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const proId = this.dataset.id;
                const provider = <?= json_encode($providers) ?>.find(p => p.pro_ID == proId);
                
                if (provider) {
                    document.getElementById('pro_id').value = provider.pro_ID;
                    document.getElementById('pro_name').value = provider.pro_name;
                    document.getElementById('pro_phone').value = provider.pro_phone;
                    document.getElementById('service').value = provider.service;
                    document.getElementById('price').value = provider.price;
                    document.getElementById('statu').value = provider.statu;
                    
                    document.getElementById('provider-submit-btn').name = 'update_provider';
                    document.getElementById('provider-modal').classList.add('active');
                }
            });
        });
        
        // Edit request status
        document.querySelectorAll('#requests-tab .action-btn.edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const reqId = this.dataset.id;
                document.getElementById('req_id').value = reqId;
                document.getElementById('request-modal').classList.add('active');
            });
        });
        
        // Close modals
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                modals.forEach(modal => modal.classList.remove('active'));
            });
        });
        
        // Close modal when clicking outside
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>

</body>
</html>