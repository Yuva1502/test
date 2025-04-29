<?php
session_start();

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Include database connection
include 'db_connection.php';

// Get products in cart
$cart_items = [];
$total_price = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $sql = "SELECT * FROM Products WHERE Product_id = $product_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $product['quantity'] = $quantity;
        $product['total'] = $quantity * $product['Prod_price'];
        $cart_items[] = $product;
        $total_price += $product['total'];
    }
}

// Process checkout form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    
    // Validate products are available
    $all_available = true;
    $unavailable_products = [];
    
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['Prod_stock']) {
            $all_available = false;
            $unavailable_products[] = [
                'name' => $item['Product_name'],
                'requested' => $item['quantity'],
                'available' => $item['Prod_stock']
            ];
        }
    }
    
    if (!$all_available) {
        $error_message = "Some products are not available in requested quantity:<br>";
        foreach ($unavailable_products as $product) {
            $error_message .= "- " . htmlspecialchars($product['name']) . 
                             ": Requested " . $product['requested'] . 
                             ", Available: " . $product['available'] . "<br>";
        }
    } else {
        // Check if customer with this email already exists
        $email_check = $conn->query("SELECT Cust_id FROM Customer WHERE Cust_email = '$email'");
        
        if ($email_check->num_rows > 0) {
            // Use existing customer
            $customer_row = $email_check->fetch_assoc();
            $customer_id = $customer_row['Cust_id'];
            
            // Special case for admin account - don't modify admin data
            if ($email === 'admin@gmail.com') {
                // Just use the customer ID, don't update admin info
            } else {
                // Update customer information in case it has changed
                $update_customer = "UPDATE Customer SET Cust_name = '$name', Cust_address = '$address' 
                                   WHERE Cust_id = $customer_id";
                $conn->query($update_customer);
            }
        } else {
            // Check if trying to use admin email but with different case
            if (strtolower($email) === 'admin@gmail.com') {
                $error_message = "The email address admin@gmail.com is reserved. Please use a different email address.";
            } else {
                // Get the maximum Customer ID and increment by 1
                $result = $conn->query("SELECT MAX(Cust_id) as max_id FROM Customer");
                $row = $result->fetch_assoc();
                $customer_id = ($row['max_id'] ?? 0) + 1;
                
                // Insert customer with the generated ID
                $customer_sql = "INSERT INTO Customer (Cust_id, Cust_name, Cust_email, Cust_address) 
                               VALUES ($customer_id, '$name', '$email', '$address')";
                
                if (!$conn->query($customer_sql)) {
                    $error_message = "Error creating customer record: " . $conn->error;
                }
            }
        }
        
        // If no errors, create the order
        if (!isset($error_message)) {
            // Get max Order_id and increment
            $result = $conn->query("SELECT MAX(Order_id) as max_id FROM Orders");
            $row = $result->fetch_assoc();
            $order_id = ($row['max_id'] ?? 0) + 1;
            
            // Create order
            $order_date = date('Y-m-d H:i:s');
            $order_sql = "INSERT INTO Orders (Order_id, Cust_id, Order_date, Total_amount) 
                        VALUES ($order_id, $customer_id, '$order_date', $total_price)";
            
            if ($conn->query($order_sql) === TRUE) {
                // Get max Payment_id and increment
                $result = $conn->query("SELECT MAX(Payment_id) as max_id FROM Payment");
                $row = $result->fetch_assoc();
                $payment_id = ($row['max_id'] ?? 0) + 1;
                
                // Create payment
                $payment_sql = "INSERT INTO Payment (Payment_id, Order_id, Payment_date, Payment_method, Payment_amount) 
                            VALUES ($payment_id, $order_id, '$order_date', '$payment_method', $total_price)";
                
                if ($conn->query($payment_sql) === TRUE) {
                    $all_products_processed = true;
                    
                    // Add each product to order_products and update inventory
                    foreach ($cart_items as $item) {
                        // Get max Order_Products_id and increment
                        $result = $conn->query("SELECT MAX(Order_Products_id) as max_id FROM Order_Products");
                        $row = $result->fetch_assoc();
                        $order_product_id = ($row['max_id'] ?? 0) + 1;
                        
                        // Create order product entry
                        $product_total = $item['quantity'] * $item['Prod_price'];
                        $order_product_sql = "INSERT INTO Order_Products (Order_Products_id, Order_id, Product_id, Quantity, Total_Price) 
                                            VALUES ($order_product_id, $order_id, {$item['Product_id']}, {$item['quantity']}, $product_total)";
                        
                        if ($conn->query($order_product_sql) === TRUE) {
                            // Update product stock
                            $new_stock = $item['Prod_stock'] - $item['quantity'];
                            $update_product_sql = "UPDATE Products SET Prod_stock = $new_stock WHERE Product_id = {$item['Product_id']}";
                            $conn->query($update_product_sql);
                        } else {
                            $all_products_processed = false;
                            $error_message = "Error adding products to order: " . $conn->error;
                            break;
                        }
                    }
                    
                    if ($all_products_processed) {
                        // Update order with payment_id
                        $update_order_sql = "UPDATE Orders SET Payment_id = $payment_id WHERE Order_id = $order_id";
                        $conn->query($update_order_sql);
                        
                        // Clear the cart
                        $_SESSION['cart'] = [];
                        
                        // Success, show confirmation
                        $order_success = true;
                    }
                } else {
                    $error_message = "Error processing payment: " . $conn->error;
                }
            } else {
                $error_message = "Error creating order: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ZALORA</title>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        .checkout-container {
            padding: 40px 0;
        }
        
        .checkout-title {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--gray-800);
        }
        
        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }
        
        @media (max-width: 991px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }
        }
        
        .checkout-form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        
        .form-section-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--gray-700);
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .notice {
            background-color: var(--gray-100);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .notice p {
            margin: 0;
            color: var(--gray-700);
            font-size: 0.9rem;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .form-actions button {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            flex: 1;
        }
        
        .form-actions a {
            padding: 12px 24px;
            background-color: var(--gray-200);
            color: var(--gray-700);
            border: none;
            border-radius: 4px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
        }
        
        .order-summary {
            background-color: var(--gray-100);
            padding: 30px;
            border-radius: 8px;
            position: sticky;
            top: 20px;
        }
        
        .order-summary-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--gray-800);
        }
        
        .cart-items {
            margin-bottom: 20px;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: 500;
            color: var(--gray-800);
            margin-bottom: 5px;
        }
        
        .item-details {
            color: var(--gray-600);
            font-size: 0.9rem;
        }
        
        .item-price {
            font-weight: 500;
            color: var(--gray-800);
            text-align: right;
        }
        
        .order-totals {
            margin-top: 20px;
            border-top: 1px solid var(--gray-300);
            padding-top: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .total-row.final {
            font-weight: 700;
            font-size: 1.1rem;
            padding-top: 10px;
            border-top: 1px solid var(--gray-300);
            margin-top: 10px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .order-confirmation {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .order-success {
            margin-bottom: 30px;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .order-details {
            text-align: left;
            background-color: var(--gray-100);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .order-detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .order-detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .order-products {
            margin-top: 20px;
        }
        
        .order-product {
            padding: 15px;
            background-color: white;
            border-radius: 4px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        
        .order-actions {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-wrapper">
                <div class="logo">
                    <h1>ZALORA</h1>
                </div>
                <div class="search-bar">
                    <form action="search.php" method="GET">
                        <input type="text" name="query" placeholder="Search for products...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="header-actions">
                    <a href="index.php" class="header-action-link">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="cart.php" class="header-action-link">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php
                        if (!empty($_SESSION['cart'])) {
                            $itemCount = array_sum($_SESSION['cart']);
                            echo '<span class="cart-count">' . $itemCount . '</span>';
                        }
                        ?>
                    </a>
                    <a href="admin_manage_product.php" class="header-action-link">
                        <i class="far fa-user"></i> Admin
                    </a>
                </div>
            </div>
        </div>
        <div class="main-nav">
            <div class="container">
                <ul>
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="product_show.php">PRODUCTS</a></li>
                    <li><a href="about.php">ABOUT US</a></li>
                    <li><a href="admin_manage_product.php">ADMIN</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="checkout-container">
            <?php if (isset($order_success) && $order_success): ?>
                <!-- Order Confirmation -->
                <div class="order-confirmation">
                    <div class="order-success">
                        <i class="fas fa-check-circle success-icon"></i>
                        <h2>Thank You for Your Order!</h2>
                        <p>Your order has been placed successfully.</p>
                    </div>
                    
                    <div class="order-details">
                        <h3><i class="fas fa-info-circle"></i> Order Summary</h3>
                        <div class="order-detail-item">
                            <span>Order ID:</span>
                            <span>#<?php echo $order_id; ?></span>
                        </div>
                        <div class="order-detail-item">
                            <span>Order Date:</span>
                            <span><?php echo date('F j, Y g:i A', strtotime($order_date)); ?></span>
                        </div>
                        <div class="order-detail-item">
                            <span>Payment Method:</span>
                            <span><?php echo htmlspecialchars($payment_method); ?></span>
                        </div>
                        <div class="order-detail-item">
                            <span>Total Amount:</span>
                            <span>$<?php echo number_format($total_price, 2); ?></span>
                        </div>
                        
                        <h4 style="margin-top: 20px;">Purchased Items:</h4>
                        <div class="order-products">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="order-product">
                                <div>
                                    <div class="item-name"><?php echo htmlspecialchars($item['Product_name']); ?></div>
                                    <div class="item-details">Quantity: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="item-price">$<?php echo number_format($item['total'], 2); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="order-actions">
                        <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Return to Home</a>
                    </div>
                </div>
                
            <?php else: ?>
                <h1 class="checkout-title">Checkout</h1>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <div class="checkout-layout">
                    <div class="checkout-form-container">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <h3 class="form-section-title">
                                <i class="fas fa-user"></i> Customer Information
                            </h3>
                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" placeholder="John Doe" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" placeholder="john@example.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Shipping Address:</label>
                                <textarea id="address" name="address" rows="3" placeholder="123 Main St, City, Country" required></textarea>
                            </div>
                            
                            <h3 class="form-section-title">
                                <i class="fas fa-credit-card"></i> Payment Details
                            </h3>
                            <div class="form-group">
                                <label for="payment_method">Payment Method:</label>
                                <select id="payment_method" name="payment_method" required>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                            
                            <div class="form-group notice">
                                <p><i class="fas fa-info-circle"></i> This is a demo store. No actual payment will be processed.</p>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-lock"></i> Place Order</button>
                                <a href="cart.php"><i class="fas fa-arrow-left"></i> Back to Cart</a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="order-summary">
                        <h3 class="order-summary-title">Order Summary</h3>
                        
                        <div class="cart-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <div class="item-info">
                                    <div class="item-name"><?php echo htmlspecialchars($item['Product_name']); ?></div>
                                    <div class="item-details">
                                        $<?php echo number_format($item['Prod_price'], 2); ?> × <?php echo $item['quantity']; ?>
                                    </div>
                                </div>
                                <div class="item-price">$<?php echo number_format($item['total'], 2); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-totals">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="total-row final">
                                <span>Total:</span>
                                <span>$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Zalora</h3>
                    <p>Zalora is Asia's leading online fashion destination. We offer thousands of brands at affordable prices to fashion lovers across the region.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Returns & Exchanges</a></li>
                        <li><a href="#">Shipping Information</a></li>
                        <li><a href="#">Size Guide</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>My Account</h3>
                    <ul>
                        <li><a href="#">Sign In</a></li>
                        <li><a href="#">Register</a></li>
                        <li><a href="#">Order History</a></li>
                        <li><a href="#">My Wishlist</a></li>
                        <li><a href="#">Track Order</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Fashion Street, Singapore</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+65 1234 5678</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>support@zalora.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Zalora. All Rights Reserved.</p>
                <div class="payment-methods">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-cc-amex"></i>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 