<?php
session_start();
include 'connect.php';

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    // Fetch shipping orders
    $stmt = $con->prepare("
        SELECT 
            o.OrderID AS order_id,
            o.created_at AS date,
            o.status,
            o.total_amount,
            c.name AS client_name
        FROM Orders o
        JOIN Customer c ON o.CustomerID = c.CustomerID
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $shippingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a file name
    $filename = "shipping_orders_" . date('Y-m-d') . ".xls";

    // Set headers to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Write the report data to the file
    echo "Order ID\tDate\tStatus\tClient Name\tTotal Amount\n"; // Header row
    foreach ($shippingOrders as $order) {
        echo htmlspecialchars($order['order_id']) . "\t";
        echo date('m/d/Y', strtotime($order['date'])) . "\t";
        echo htmlspecialchars($order['status']) . "\t";
        echo htmlspecialchars($order['client_name']) . "\t";
        echo htmlspecialchars($order['total_amount']) . "\n";
    }
    exit();
} else {
    header('Location: index.php');
    exit();
}
?>