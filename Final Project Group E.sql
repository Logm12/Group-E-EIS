DROP DATABASE GroupE;
CREATE DATABASE GroupE;
USE GroupE;
CREATE TABLE Category (
    CategoryID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(50)
);
LOAD DATA INFILE "E:/Data/Category.csv"
INTO TABLE Category
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(CategoryID, Name);
CREATE TABLE Supplier (
    SupplierID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    phone_number VARCHAR(20),
    address TEXT,
    contact_person VARCHAR(100),
    created_at DATE,
    updated_at DATE
);
LOAD DATA INFILE "E:/Data/Supplier.csv"
INTO TABLE Supplier
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(SupplierID, name, email, phone_number, address, contact_person, created_at, updated_at);
CREATE TABLE Product (
    ProductID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    description TEXT,
    SupplierID INT,
    price DECIMAL(10, 2),
    product_code VARCHAR(50),
    CategoryID INT,
    stock_quantity INT,
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID),
	FOREIGN KEY (CategoryID) REFERENCES Category(CategoryID)
);
LOAD DATA INFILE "E:/Data/Product .csv"
INTO TABLE Product
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(ProductID, name, description, SupplierID, price, product_code, CategoryID, stock_quantity, created_at, updated_at);
CREATE TABLE Warehouse (
    WarehouseId INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    location VARCHAR(255),
    contact_number VARCHAR(20),
    manager_name VARCHAR(100),
    created_at DATE,
    updated_at DATE
);
LOAD DATA INFILE "E:/Data/Warehouse.csv"
INTO TABLE Warehouse
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(WarehouseId, name, location, contact_number, manager_name, created_at, updated_at);
CREATE TABLE Inventory (
    InventoryID INT PRIMARY KEY AUTO_INCREMENT,
    ProductID INT,
    WarehouseID INT,
	current_stock_level INT,
    minimum_stock_level INT,
    optimal_stock_level INT,
    last_restock_date DATE,
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
	FOREIGN KEY (WarehouseID) REFERENCES Warehouse(WarehouseID)
);
LOAD DATA INFILE "E:/Data/Inventory.csv"
INTO TABLE Inventory
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(InventoryID, ProductID, WarehouseID, current_stock_level, minimum_stock_level, optimal_stock_level, last_restock_date, created_at, updated_at);
CREATE TABLE Customer (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    phone_number VARCHAR(20),
    address VARCHAR(250),
    created_at DATE,
    updated_at DATE
);
LOAD DATA INFILE "E:/Data/Customer.csv"
INTO TABLE Customer
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(CustomerID, name, email, phone_number, address, created_at, updated_at);
CREATE TABLE Orders (
    OrderID INT PRIMARY KEY AUTO_INCREMENT,
    CustomerID INT,
	status VARCHAR(50),
    total_amount DECIMAL(10, 2),
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID)
);
LOAD DATA INFILE "E:/Data/Order.csv"
INTO TABLE Orders
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(OrderID, CustomerID, status, total_amount, created_at, updated_at);
CREATE TABLE Discount (
    DiscountID INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50),
    description TEXT,
    discount_type ENUM('percentage', 'fixed'),
    value DECIMAL(10, 2),
    start_date DATE,
    end_date DATE
);
LOAD DATA INFILE "E:/Data/Discount.csv"
INTO TABLE Discount
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(DiscountID, code, description, discount_type, value, start_date, end_date);

CREATE TABLE OrderDetail (
    OrderDetailID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    ProductID INT,
    DiscountID INT,
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
    FOREIGN KEY (DiscountID) REFERENCES Discount(DiscountID)
);
LOAD DATA INFILE "E:/Data/OrderDetail.csv"
INTO TABLE OrderDetail
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(OrderDetailID, OrderID, ProductID, DiscountID, quantity, price);
CREATE TABLE Shipment (
    ShipmentID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    shipping_method VARCHAR(50),
    tracking_number VARCHAR(50),
    status VARCHAR(50),
    shipped_at DATE,
    delivered_at DATE,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID)
);
LOAD DATA INFILE "E:/Data/Shipment.csv"
INTO TABLE Shipment
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(ShipmentID, OrderID, shipping_method, tracking_number, status, shipped_at, delivered_at);
CREATE TABLE Log (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    event_type ENUM('inventory_update', 'order_status_change', 'shipment_update'),
    description TEXT,
    created_at DATE
);
LOAD DATA INFILE "E:/Data/Log.csv"
INTO TABLE Log
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(LogID, event_type, description, created_at);
CREATE TABLE APIIntegration (
    IntegrationID INT AUTO_INCREMENT PRIMARY KEY,
    source_system ENUM('ERP', 'CRM'),
    reference_id VARCHAR(255),
    data TEXT,
    last_synced_at DATE
);
LOAD DATA INFILE "E:/Data/API Integration.csv"
INTO TABLE APIIntegration
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(IntegrationID, source_system, reference_id, data, last_synced_at);
CREATE TABLE RestockOrder (
    RestockID INT AUTO_INCREMENT PRIMARY KEY,
    InventoryID INT,
    SupplierID INT,
    order_quantity INT,
    order_status ENUM('pending', 'ordered', 'delivered', 'canceled'),
    estimated_delivery_date DATE,
    actual_delivery_date DATE,
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (InventoryID) REFERENCES Inventory(InventoryID),
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID)
);
LOAD DATA INFILE "E:/Data/Restock Order.csv"
INTO TABLE RestockOrder
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(RestockID, InventoryID, SupplierID, order_quantity, order_status, estimated_delivery_date, actual_delivery_date, created_at, updated_at);
CREATE TABLE RestockOrderItem (
    RestockOrderItemID INT AUTO_INCREMENT PRIMARY KEY,
    RestockID INT,
    ProductID INT,
    quantity INT,
    price INT,
    FOREIGN KEY (RestockID) REFERENCES RestockOrder(RestockID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);
LOAD DATA INFILE "E:/Data/Restock Order Item.csv"
INTO TABLE RestockOrderItem
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(RestockOrderItemID, RestockID, ProductID, quantity, price);

CREATE TABLE Payment (
    PaymentID INT PRIMARY KEY AUTO_INCREMENT,
    OrderID INT,
    payment_method VARCHAR(50),
    status VARCHAR(50),
    amount DECIMAL(10, 2),
    transaction_date DATE,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID)
);
LOAD DATA INFILE "E:/Data/Payment.csv"
INTO TABLE Payment
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(PaymentID, OrderID, payment_method, status, amount, transaction_date);
CREATE TABLE LogisticsPartner (
    PartnerID INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    contact_info VARCHAR(255),
    api_url TEXT,
    created_at DATE,
    updated_at DATE
);
LOAD DATA INFILE "E:/Data/Logistics Partner.csv"
INTO TABLE LogisticsPartner
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(PartnerID, name, contact_info, api_url, created_at, updated_at);
CREATE TABLE UserRolesPermissions (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(250),
    password VARCHAR(250),
    role TEXT,
    created_at DATE
);
LOAD DATA INFILE "E:/Data/User.csv"
INTO TABLE UserRolesPermissions
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(UserID, username, password, role, created_at);
CREATE TABLE Notification (
    NotificationID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT,
    message TEXT,
    status ENUM('sent', 'failed'),
    sent_at DATE,
    FOREIGN KEY (UserID) REFERENCES UserRolesPermissions(UserID)
);
LOAD DATA INFILE "E:/Data/Notification.csv"
INTO TABLE Notification
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(NotificationID, UserID, message, status, sent_at);
CREATE TABLE Address (
    AddressID INT PRIMARY KEY AUTO_INCREMENT,
    CustomerID INT,
    address_line VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    latitude INT,
    longitude INT,
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID)
);
LOAD DATA INFILE "E:/Data/Address.csv"
INTO TABLE Address
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(AddressID, CustomerID, address_line, city, state, country, postal_code, latitude, longitude, created_at, updated_at);
CREATE TABLE Delivery (
    DeliveryID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT,
    PartnerID INT,
    delivery_status ENUM('pending', 'in_transit', 'delivered', 'failed'),
    AddressID INT,
    tracking_number VARCHAR(255),
    estimated_delivery_date DATE,
    actual_delivery_date DATE,
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID),
    FOREIGN KEY (PartnerID) REFERENCES LogisticsPartner(PartnerID)
);
LOAD DATA INFILE "E:/Data/Delivery.csv"
INTO TABLE Delivery
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(DeliveryID, OrderID, PartnerID, delivery_status, AddressID, tracking_number, estimated_delivery_date, actual_delivery_date, created_at, updated_at);

CREATE TABLE RouteOptimization (
    RouteID INT AUTO_INCREMENT PRIMARY KEY,
    DeliveryID INT,
    route_details TEXT,
    total_distance DECIMAL(10, 2),
    total_time DECIMAL(10, 2),
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (DeliveryID) REFERENCES Delivery(DeliveryID)
);
LOAD DATA INFILE "E:/Data/Route Optimization.csv"
INTO TABLE RouteOptimization
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(RouteID, DeliveryID, route_details, total_distance, total_time, created_at, updated_at);
CREATE TABLE StockOptimizationLog (
    StockID INT AUTO_INCREMENT PRIMARY KEY,
    InventoryID INT,
    suggested_restock INT,
    demand_forecast INT,
    generated_at DATE,
    FOREIGN KEY (InventoryID) REFERENCES Inventory(InventoryID)
);
LOAD DATA INFILE "E:/Data/Stock Optimization Log.csv"
INTO TABLE StockOptimizationLog
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(StockID, InventoryID, suggested_restock, demand_forecast, generated_at);
CREATE TABLE SalesData (
    SalesDataID INT PRIMARY KEY AUTO_INCREMENT,
    ProductID INT,
    OrderID INT,
    quantity_sold INT,
    total_sales_amount DECIMAL(10, 2),
    sales_date DATE,
    region VARCHAR(50),
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID)
);
LOAD DATA INFILE "E:/Data/Sales Data.csv"
INTO TABLE SalesData
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(SalesDataID, ProductID, OrderID, quantity_sold, total_sales_amount, sales_date, region, created_at, updated_at);
CREATE TABLE ForecastingModel (
    ForecastID INT AUTO_INCREMENT PRIMARY KEY,
    model_name VARCHAR(255),
    description TEXT,
    accuracy DECIMAL(5, 2),
    last_trained_date DATE,
    created_at DATE,
    updated_at DATE
);
LOAD DATA INFILE "E:/Data/Forecasting Model.csv"
INTO TABLE ForecastingModel
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(ForecastID, model_name, description, accuracy, last_trained_date, created_at, updated_at);
CREATE TABLE ForecastResult (
    ForecastResultID INT AUTO_INCREMENT PRIMARY KEY,
    ProductID INT,
    ForecastID INT,
    forecast_date DATE,
    predicted_sales INT,
    predicted_revenue DECIMAL(15, 2),
    confidence_interval VARCHAR(255),
    created_at DATE,
    updated_at DATE,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
    FOREIGN KEY (ForecastID) REFERENCES ForecastingModel(ForecastID)
);
LOAD DATA INFILE "E:/Data/Forecast Result.csv"
INTO TABLE ForecastResult
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(ForecastResultID, ProductID, ForecastID, forecast_date, predicted_sales, predicted_revenue, confidence_interval, created_at, updated_at);
