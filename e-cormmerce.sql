CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

CREATE TABLE Customer (
    Cust_id INT PRIMARY KEY,
    Cust_name VARCHAR(100) NOT NULL,
    Cust_email VARCHAR(100) UNIQUE NOT NULL,
    Cust_address VARCHAR(255)
);

CREATE TABLE Payment (
    Payment_id INT PRIMARY KEY,
    Order_id INT,
    Payment_date DATETIME NOT NULL,
    Payment_method VARCHAR(50) NOT NULL,
    Payment_amount DECIMAL(10,2) NOT NULL
);

CREATE TABLE Products (
    Product_id INT PRIMARY KEY,
    Product_name VARCHAR(100) NOT NULL,
    Description TEXT,
    Category VARCHAR(50),
    Prod_price DECIMAL(10,2) NOT NULL,
    Prod_stock INT NOT NULL DEFAULT 0
);

CREATE TABLE Orders (
    Order_id INT PRIMARY KEY,
    Cust_id INT NOT NULL,
    Payment_id INT,
    Order_date DATETIME NOT NULL,
    Total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (Cust_id) REFERENCES Customer(Cust_id),
    FOREIGN KEY (Payment_id) REFERENCES Payment(Payment_id)
);

CREATE TABLE Order_Products (
    Order_Products_id INT PRIMARY KEY,
    Order_id INT NOT NULL,
    Product_id INT NOT NULL,
    Quantity INT NOT NULL,
    Total_Price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (Order_id) REFERENCES Orders(Order_id),
    FOREIGN KEY (Product_id) REFERENCES Products(Product_id)
);

-- Add foreign key for Payment table that depends on Order
ALTER TABLE Payment
ADD CONSTRAINT FK_Payment_Order FOREIGN KEY (Order_id) REFERENCES Orders(Order_id);