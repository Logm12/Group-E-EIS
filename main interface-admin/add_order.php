<?php
// add_order.php
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerID = $_POST['customerID'];
    $status = $_POST['status'];
    $totalAmount = $_POST['totalAmount'];
    $createdAt = $_POST['createdAt'];
    $updatedAt = $_POST['updatedAt'];

    // Thêm đơn hàng vào cơ sở dữ liệu
    $stmt = $con->prepare("INSERT INTO Orders (CustomerID, status, total_amount, created_at, updated_at) VALUES (?, ?, ?, ?, ? )");
    $stmt->bind_param("issss", $customerID, $status, $totalAmount, $createdAt, $updatedAt);

    if ($stmt->execute()) {
        echo "New order added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
}
?>