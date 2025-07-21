<?php
include('db.php');  // الاتصال بقاعدة البيانات

// التحقق من استلام البيانات عبر POST من نموذج إرسال الطلب
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من وجود الحقول المطلوبة في $_POST
    $name = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $phone = isset($_POST['phonenumber']) ? mysqli_real_escape_string($conn, $_POST['phonenumber']) : '';
    $service_type = isset($_POST['serviceType']) ? mysqli_real_escape_string($conn, $_POST['serviceType']) : '';
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $address = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';

        // التحقق من وجود البيانات المدخلة
    $errors = [];

    if (empty($name)) $errors[] = 'الاسم الكامل';
    if (empty($email)) $errors[] = 'البريد الإلكتروني';
    if (empty($phone)) $errors[] = 'رقم الجوال';
    if (empty($service_type)) $errors[] = 'نوع الخدمة';
    if (empty($description)) $errors[] = 'الوصف';

    if (count($errors) > 0) {
        echo "يرجى ملء الحقول التالية: " . implode(', ', $errors);
        exit();
    }

    // التحقق من وجود العميل باستخدام البريد الإلكتروني
    $check_email_sql = "SELECT cust_ID FROM customers WHERE cust_email = '$email' LIMIT 1";
    $email_result = $conn->query($check_email_sql);

    if ($email_result->num_rows > 0) {
        // إذا كان العميل موجودًا، احصل على cust_ID
        $customer_row = $email_result->fetch_assoc();
        $cust_id = $customer_row['cust_ID'];
    } else {
        // إذا لم يكن العميل موجودًا، ضع cust_id كـ NULL
        $cust_id = NULL;
    }

    // استعلام لاسترجاع السعر بناءً على نوع الخدمة
    $service_provider_sql = "SELECT pro_ID, price FROM service_providers WHERE service_type_ID = '$service_type' LIMIT 1";
    $service_provider_result = $conn->query($service_provider_sql);

    // التحقق من نجاح الاستعلام
    if ($service_provider_result === false) {
        echo "خطأ في الاستعلام: " . $conn->error;
        exit();  // إيقاف تنفيذ الكود إذا كان الاستعلام فاشلًا
    }

    if ($service_provider_result->num_rows > 0) {
        $service_provider_row = $service_provider_result->fetch_assoc();
        $service_provider = $service_provider_row['pro_ID'];
        $price = $service_provider_row['price'];  // استرجاع السعر من قاعدة البيانات
    } else {
        echo "مقدم الخدمة غير موجود لهذا النوع من الخدمة.";
        exit();
    }

    // استعلام لإدخال البيانات في جدول `requests` مع cust_id (قد يكون NULL)
    $sql = "INSERT INTO requests (cust_ID, cust_name, cust_email, cust_phone, service_type_ID, pro_ID, price, description, req_statu_ID)
            VALUES ('$cust_id', '$name', '$email', '$phone', '$service_type', '$service_provider', '$price', '$description', 1)";  // الحالة الافتراضية هي قيد الانتظار (1)

    // تنفيذ الاستعلام
    if ($conn->query($sql) === TRUE) {
        echo "تم إرسال الطلب بنجاح!";
    } else {
        echo "حدث خطأ أثناء إدخال البيانات: " . $conn->error;
    }

    $conn->close();  // إغلاق الاتصال بقاعدة البيانات
}
?>