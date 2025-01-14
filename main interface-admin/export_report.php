<?php
session_start();
include 'connect.php';

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    // Prepare the SQL statement to get the report data
    $stmt = $con->prepare("
        SELECT w.name AS WarehouseName, p.name AS ProductName, SUM(i.current_stock_level) AS TotalQuantity
        FROM Inventory i
        JOIN Warehouse w ON i.WarehouseID = w.WarehouseID
        JOIN Product p ON i.ProductID = p.ProductID
        GROUP BY w.name, p.name
        ORDER BY w.name, p.name
    ");
    $stmt->execute();
    $reportData = $stmt->fetchAll();

    // Create a file name
    $filename = "inventory_report_" . date('Y-m-d') . ".txt";

    // Set headers to download the file
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Write the report data to the file
    foreach ($reportData as $row) {
        echo "Warehouse Name: " . htmlspecialchars($row['WarehouseName']) . "\n";
        echo "Product Name: " . htmlspecialchars($row['ProductName']) . "\n";
        echo "Total Quantity: " . htmlspecialchars($row['TotalQuantity']) . "\n";
        echo "-------------------------\n";
    }
    exit();
} else {
    header('Location: index.php');
    exit();
}
?>