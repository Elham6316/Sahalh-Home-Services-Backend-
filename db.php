<?php
// Database configuration
$servername = "sql309.infinityfree.com";
$username   = "if0_39494302";
$password   = "TOxVuLkZez";
$dbname     = "if0_39494302_sahalh";

// Create connection (port 3306)
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ضبط الترميز للاتصال
$conn->set_charset('utf8mb4');

// (اختياري) للتأكيد يمكنك أيضاً:
$conn->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");

// لا حاجة لإخراج شيء هنا
// echo "";

?>
