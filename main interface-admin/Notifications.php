<?php
ob_start();
session_start();

$pageTitle = 'Notifications';

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';

    $user_id = $_SESSION['userid'] ?? 5; // Get actual user ID from session

    // Fetch notifications
    try {
        $stmt = $con->prepare(
            "SELECT 
                n.NotificationID,
                u.username AS SenderName,
                n.message AS Message,
                n.sent_at AS SendTime,
                n.status AS IsRead,
                n.UserID AS RecipientID
             FROM Notification n
             JOIN UserRolesPermissions u ON n.UserID = u.UserID
             ORDER BY n.sent_at DESC"
        );
        $stmt->execute();
        $notifications = $stmt->fetchAll();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error fetching notifications: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    // Fetch stock warnings
    try {
        $stmt = $con->prepare(
            "SELECT 
                p.name AS ProductName,
                i.current_stock_level AS StockQuantity,
                i.last_restock_date AS LastRestockDate,
                CASE 
                    WHEN i.current_stock_level <= i.minimum_stock_level THEN 'Critical'
                    WHEN i.current_stock_level <= i.optimal_stock_level THEN 'Warning'
                    ELSE 'Normal'
                END as StockStatus
             FROM Inventory i
             JOIN Product p ON i.ProductID = p.ProductID
             WHERE i.current_stock_level <= i.optimal_stock_level
             ORDER BY i.current_stock_level ASC"
        );
        $stmt->execute();
        $stockWarnings = $stmt->fetchAll();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error fetching stock warnings: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>
    
    <div class="notification-container">
        <!-- Notifications Section -->
        <div class="notifications-section">
            <div class="section-header">
                <div class="header-left">
                    <img src="Includes/templates/message.png" alt="Notifications" class="section-icon">
                    <h2>Notifications</h2>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <input type="text" placeholder="Search notifications..." class="search-input">
                        <i class="search-icon"></i>
                    </div>
                    <span class="unread-count">Unread: <?php echo count(array_filter($notifications, function($n) { return $n['IsRead'] === 'failed'; })); ?></span>
                </div>
            </div>
    
            <div class="notifications-list">
                <?php if (!empty($notifications)) { 
                    foreach ($notifications as $notification) { ?>
                        <div class="notification-item <?php echo $notification['IsRead'] === 'failed' ? 'unread' : ''; ?>">
                            <div class="notification-avatar">
                                <img src="Includes/templates/user.jpg" alt="User Avatar">
                            </div>
                            <div class="notification-content">
                                <div class="notification-header">
                                    <span class="sender-name"><?php echo htmlspecialchars($notification['SenderName']); ?></span>
                                    <span class="notification-time"><?php echo htmlspecialchars($notification['SendTime']); ?></span>
                                </div>
                                <p class="notification-text"><?php echo htmlspecialchars($notification['Message']); ?></p>
                            </div>
                            <?php if ($notification['IsRead'] === 'failed') { ?>
                                <span class="unread-indicator"></span>
                            <?php } ?>
                        </div>
                    <?php }
                } else { ?>
                    <div class="no-notifications">No notifications available.</div>
                <?php } ?>
            </div>
        </div>
    
        <!-- Stock Warnings Section -->
        <div class="stock-warnings-section">
            <div class="section-header">
                <img src="Includes/templates/sale_stock_warning.png" width="200" height="200" alt="Warning" class="section-icon">
                <h2>Low Stock Warning</h2>
            </div>
    
            <div class="stock-warnings-list">
                <div class="stock-table-header">
                    <span class="product-col">Product</span>
                    <span class="stock-col">Stock</span>
                    <span class="status-col">Status</span>
                    <span class="date-col">Last Restock</span>
                </div>
                <?php if (!empty($stockWarnings)) {
                    foreach ($stockWarnings as $warning) { ?>
                        <div class="stock-item">
                            <span class="product-col"><?php echo htmlspecialchars($warning['ProductName']); ?></span>
                            <span class="stock-col"><?php echo htmlspecialchars($warning['StockQuantity']); ?></span>
                            <span class="status-col">
                                <span class="status-indicator <?php echo strtolower($warning['StockStatus']); ?>">
                                    <?php echo htmlspecialchars($warning['StockStatus']); ?>
                                </span>
                            </span>
                            <span class="date-col"><?php echo htmlspecialchars($warning['LastRestockDate']); ?></span>
                        </div>
                    <?php }
                } else { ?>
                    <div class="no-warnings">No stock warnings.</div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <style>
        .notification-container {
            padding: 20px;
            background-color: #f5f5f5;
        }
    
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background-color: #fff;
            border-radius: 8px 8px 0 0;
            border-bottom: 1px solid #eee;
        }
    
        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    
        .section-icon {
            width: 24px;
            height: 24px;
        }
    
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
    
        .search-box {
            position: relative;
        }
    
        .search-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 250px;
        }
    
        .notifications-section, .stock-warnings-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    
        .notification-item {
            display: flex;
            padding: 15px;
            border-bottom: 1px solid #eee;
            position: relative;
        }
    
        .notification-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }
    
        .notification-content {
            flex: 1;
        }
    
        .notification-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
    
        .sender-name {
            font-weight: 600;
            color: #333;
        }
    
        .notification-time {
            color: #888;
            font-size: 0.9em;
        }
    
        .notification-text {
            color: #666;
            margin: 0;
        }
    
        .unread-indicator {
            width: 8px;
            height: 8px;
            background-color: #ee4d2d;
            border-radius: 50%;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
    
        .stock-table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            padding: 12px 15px;
            background-color: #f8f8f8;
            font-weight: 600;
        }
    
        .stock-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
    
        .status-indicator {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.9em;
            text-align: center;
            display: inline-block;
        }
    
        .status-indicator.critical {
            background-color: #ffe4e4;
            color: #ff4d4f;
        }
    
        .status-indicator.warning {
            background-color: #fff7e6;
            color: #ffa940;
        }
    
        .status-indicator.normal {
            background-color: #e6f7ff;
            color: #1890ff;
        }
    
        .unread {
            background-color: #fff9f8;
        }
    
        .no-messages, .no-warnings {
            padding: 20px;
            text-align: center;
            color: #888;
        }
    </style>
    
<?php include 'Includes/templates/footer.php'; } 
else { 
    header('Location: index.php'); 
    exit(); } ?>