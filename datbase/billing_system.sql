-- Advanced GST Billing Management System Database
-- Created for Final Year Project

CREATE DATABASE IF NOT EXISTS billing_system;
USE billing_system;

-- Admin Table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(200) NOT NULL,
    category_id INT,
    barcode VARCHAR(50) UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    low_stock_alert INT DEFAULT 10,
    product_image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bills Table
CREATE TABLE IF NOT EXISTS bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(15),
    subtotal DECIMAL(10,2) NOT NULL,
    gst_amount DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(20) DEFAULT 'Cash',
    bill_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Bill Items Table
CREATE TABLE IF NOT EXISTS bill_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    gst_amount DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert Default Admin (password: admin123)
INSERT INTO admin (username, email, password, full_name, phone) 
VALUES ('admin', 'admin@billing.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '9876543210');

-- Insert Sample Categories
INSERT INTO categories (category_name, description) VALUES
('Grocery', 'Daily grocery items'),
('Beverages', 'Soft drinks, juices, tea, coffee'),
('Snacks', 'Chips, biscuits, namkeen'),
('Personal Care', 'Soaps, shampoos, cosmetics'),
('Household', 'Cleaning supplies, utensils'),
('Dairy', 'Milk, butter, cheese, curd'),
('Stationery', 'Pens, notebooks, files'),
('Electronics', 'Mobile accessories, batteries');

-- Insert Sample Products
INSERT INTO products (product_name, category_id, barcode, price, stock_quantity, low_stock_alert, description) VALUES
('Tata Tea Gold 1kg', 2, 'TEA001', 450.00, 50, 10, 'Premium quality tea leaves'),
('Parle-G Biscuits 1kg', 3, 'BIS001', 80.00, 100, 20, 'Classic glucose biscuits'),
('Amul Butter 500g', 6, 'BUT001', 250.00, 30, 5, 'Fresh table butter'),
('Clinic Plus Shampoo 340ml', 4, 'SHA001', 180.00, 40, 10, 'Hair shampoo with vitamins'),
('Lays Chips 50g', 3, 'CHI001', 20.00, 200, 50, 'Crispy potato chips'),
('Maggi Noodles 560g', 1, 'NOO001', 120.00, 75, 15, '8 pack instant noodles'),
('Colgate Toothpaste 200g', 4, 'TOO001', 140.00, 60, 15, 'Dental care toothpaste'),
('Surf Excel 1kg', 5, 'DET001', 220.00, 45, 10, 'Detergent powder'),
('Fortune Sunflower Oil 1L', 1, 'OIL001', 180.00, 35, 8, 'Refined cooking oil'),
('Himalaya Face Wash 150ml', 4, 'FAC001', 160.00, 50, 10, 'Herbal face wash'),
('Dettol Soap 125g', 4, 'SOA001', 45.00, 150, 30, 'Antibacterial soap'),
('Mother Dairy Milk 1L', 6, 'MIL001', 60.00, 25, 5, 'Full cream milk'),
('Britannia Bread 400g', 1, 'BRE001', 40.00, 80, 20, 'Whole wheat bread'),
('Reynolds Pen (Blue)', 7, 'PEN001', 10.00, 300, 50, 'Ball point pen'),
('Duracell Battery AA', 8, 'BAT001', 25.00, 100, 20, 'Alkaline battery');

-- Insert Sample Customers
INSERT INTO customers (customer_name, phone, email, address) VALUES
('Rajesh Kumar', '9876543211', 'rajesh@email.com', 'MG Road, Mumbai'),
('Priya Sharma', '9876543212', 'priya@email.com', 'Park Street, Delhi'),
('Amit Patel', '9876543213', 'amit@email.com', 'Civil Lines, Nagpur'),
('Sneha Reddy', '9876543214', 'sneha@email.com', 'Jubilee Hills, Hyderabad'),
('Vikram Singh', '9876543215', 'vikram@email.com', 'Banjara Hills, Bangalore');

-- Insert Sample Bills
INSERT INTO bills (bill_number, customer_id, customer_name, customer_phone, subtotal, gst_amount, discount_amount, final_amount, payment_method, bill_date) VALUES
('BILL-0001', 1, 'Rajesh Kumar', '9876543211', 500.00, 25.00, 0, 525.00, 'Cash', '2024-05-20'),
('BILL-0002', 2, 'Priya Sharma', '9876543212', 750.00, 37.50, 20, 767.50, 'UPI', '2024-05-21'),
('BILL-0003', 3, 'Amit Patel', '9876543213', 320.00, 16.00, 0, 336.00, 'Cash', '2024-05-22'),
('BILL-0004', 4, 'Sneha Reddy', '9876543214', 890.00, 44.50, 50, 884.50, 'Card', '2024-05-23'),
('BILL-0005', 5, 'Vikram Singh', '9876543215', 1200.00, 60.00, 100, 1160.00, 'Cash', CURDATE());

-- Insert Sample Bill Items for BILL-0001
INSERT INTO bill_items (bill_id, product_id, product_name, quantity, price, gst_amount, total_amount) VALUES
(1, 1, 'Tata Tea Gold 1kg', 1, 450.00, 22.50, 472.50),
(1, 5, 'Lays Chips 50g', 2, 20.00, 2.00, 42.00);

-- Insert Sample Bill Items for BILL-0002
INSERT INTO bill_items (bill_id, product_id, product_name, quantity, price, gst_amount, total_amount) VALUES
(2, 3, 'Amul Butter 500g', 2, 250.00, 25.00, 525.00),
(2, 6, 'Maggi Noodles 560g', 2, 120.00, 12.00, 252.00);

-- Create Indexes for Better Performance
CREATE INDEX idx_product_barcode ON products(barcode);
CREATE INDEX idx_bill_number ON bills(bill_number);
CREATE INDEX idx_bill_date ON bills(bill_date);
CREATE INDEX idx_customer_phone ON customers(phone);

-- Views for Dashboard
CREATE VIEW dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM products WHERE stock_quantity <= low_stock_alert) as low_stock_products,
    (SELECT COUNT(*) FROM customers) as total_customers,
    (SELECT COUNT(*) FROM bills) as total_bills,
    (SELECT COALESCE(SUM(final_amount), 0) FROM bills) as total_sales,
    (SELECT COALESCE(SUM(final_amount), 0) FROM bills WHERE DATE(bill_date) = CURDATE()) as today_sales,
    (SELECT COALESCE(SUM(final_amount), 0) FROM bills WHERE MONTH(bill_date) = MONTH(CURDATE()) AND YEAR(bill_date) = YEAR(CURDATE())) as monthly_sales;

-- View for Low Stock Products
CREATE VIEW low_stock_view AS
SELECT p.id, p.product_name, p.stock_quantity, p.low_stock_alert, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.stock_quantity <= p.low_stock_alert
ORDER BY p.stock_quantity ASC;