<!-- ADMIN NAVBAR HEADER -->
<style>
    .vertical-menu {
        background-color: #FFC0CB; /* Thay đổi màu nền cho menu dọc nếu cần */
    }
</style>

<header class="headerMenu clearfix sb-page-header">   
    <div class="nav-header">
        <a class="navbar-brand" href="dashboard.php">
            SHOPEE IMS <img src="Includes/templates/Shopee.png" width="150" height="50" alt="Shopee Logo">
        </a>
        <a class="navbar-brand" href="dashboard.php">
            WELCOME, ADMIN
        </a>
    </div>

    <div class="nav-controls top-nav">
        <ul class="nav top-menu">
            <li id="user-btn" class="main-li dropdown" style="background:none;">
                <div class="dropdown show">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user"></i>
                        <span class="username">Settings</span>
                        <b class="caret"></b>
                    </a>
                    <!-- DROPDOWN MENU -->
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="users.php?do=Edit&user_id=<?php echo $_SESSION['userid'] ?>">
                            <i class="fas fa-user-cog"></i>
                            <span style="padding-left:6px">Edit Profile</span>
                        </a>
                        <a class="dropdown-item" href="website-settings.php">
                            <i class="fas fa-cogs"></i>
                            <span style="padding-left:6px">Website Settings</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span style="padding-left:6px">Logout</span>
                        </a>
                    </div>
                </div>
            </li>
            <li class="main-li webpage-btn">
                <a class="nav-item-button" href="../" target="_blank">
                    <i class="fas fa-eye"></i>
                    <span>Main Page</span>
                </a>
            </li>
        </ul>
    </div>
</header>

<!-- VERTICAL NAVBAR -->
<aside class="vertical-menu" id="vertical-menu">
    <div>
        <ul class="menu-bar">
            <div class="sidenav-menu-heading" style="font-weight: bold; color: black;">
                Real-Time Inventory Management
            </div>

            <div class="dropdown-divider"></div>

            <li class="dropdown">
                <a href="#" class="a-verMenu dropdown-toggle" data-toggle="collapse" data-target="#inventoryMenu" aria-expanded="false">
                    <i class="fas fa-box icon-ver"></i>
                    <span style="padding-left:6px; font-weight: bold; color: black;">Inventory Management</span>
                </a>
                <div id="inventoryMenu" class="collapse">
                    <a class="dropdown-item" href="current_stock.php">View Current Stock Levels</a>
                    <a class="dropdown-item" href="low_stock.php">Identify Low-Stock SKUs</a>
                    <a class="dropdown-item" href="update_inventory.php">Update Inventory Information Manually</a>
                </div>
            </li>

            <div class="dropdown-divider"></div>

            <div class="sidenav-menu-heading" style="font-weight: bold; color: black;">
                AI-Powered Sales Forecasting & Demand Analysis
            </div>

            <div class="dropdown-divider"></div>

            <li class="dropdown">
                <a href="#" class="a-verMenu dropdown-toggle" data-toggle="collapse" data-target ="#forecastingMenu" aria-expanded="false">
                    <i class="fas fa-chart-line icon-ver"></i>
                    <span style="padding-left:6px; font-weight: bold; color: black;">Sales Forecasting</span>
                </a>
                <div id="forecastingMenu" class="collapse">
                    <a class="dropdown-item" href="inventory_performance.php">Inventory Performance Analysis</a>
                    <a class="dropdown-item" href="demand_forecasting.php">Enhanced Demand Forecasting</a>
                    <a class="dropdown-item" href="actionable_reports.php">Generate Actionable Reports</a>
                </div>
            </li>

            <div class="dropdown-divider"></div>

            <div class="sidenav-menu-heading" style="font-weight: bold; color: black;">
                Order Management & Logistics Optimization
            </div>

            <div class="dropdown-divider"></div>

            <li class="dropdown">
                <a href="#" class="a-verMenu dropdown-toggle" data-toggle="collapse" data-target="#orderMenu" aria-expanded="false">
                    <i class="fas fa-shopping-cart icon-ver"></i>
                    <span style="padding-left:6px; font-weight: bold; color: black;">Order Management</span>
                </a>
                <div id="orderMenu" class="collapse">
                    <a class="dropdown-item" href="order_management.php">Order Management</a>
                    <a class="dropdown-item" href="logistics_optimization.php">Logistics Optimization</a>
                </div>
            </li>

            <div class="dropdown-divider"></div>

            <div class="sidenav-menu-heading" style="font-weight: bold; color: black;">
                Others Functions
            </div>

            <div class="dropdown-divider"></div>

            <li class="dropdown">
                <a href="#" class="a-verMenu dropdown-toggle" data-toggle="collapse" data-target="#stockMenu" aria-expanded="false">
                    <i class="fas fa-sync-alt icon-ver"></i>
                    <span style="padding-left:6px; font-weight: bold; color: black;">Others</span>
                </a>
                <div id="stockMenu" class="collapse">
                    <a class="dropdown-item" href="Notifications.php">Notification</a>
                    <a class="dropdown-item" href="Users.php">Users</a>
                    <a class="dropdown-item" href="warehouses.php">Warehouses</a>
                    <a class="dropdown-item" href="customers.php">Customers</a>                    
                </div>
            </li>

            <div class="dropdown-divider"></div>
        </ul>
    </div>
</aside>
<!-- START BODY CONTENT  -->

<div id="content" style="margin-left:240px;"> 
    <section class="content-wrapper" style="width: 100%;padding: 70px 0 0;">
        <div class="inside-page" style="padding:20px">
            <div class="page_title_top" style="margin-bottom: 1.5rem!important;">
                <h1 style="color: #5a5c69!important;font-size: 1.75rem;font-weight: 400;line-height: 1.2;">
                    <?php echo $pageTitle; ?>
                </h1>
            </div>