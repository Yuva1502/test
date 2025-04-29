<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - E-Commerce Store</title>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
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
        <?php
        // Include database connection
        include 'db_connection.php';
        
        // Check if product ID is provided
        if(isset($_GET['id']) && is_numeric($_GET['id'])) {
            $product_id = $_GET['id'];
            
            // Get product details
            $sql = "SELECT * FROM Products WHERE Product_id = $product_id";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                
                // Process payment form submission
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $name = $conn->real_escape_string($_POST['name']);
                    $email = $conn->real_escape_string($_POST['email']);
                    $address = $conn->real_escape_string($_POST['address']);
                    $payment_method = $conn->real_escape_string($_POST['payment_method']);
                    $quantity = intval($_POST['quantity']);
                    
                    if ($quantity > 0 && $quantity <= $product['Prod_stock']) {
                        $total_amount = $quantity * $product['Prod_price'];
                        
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
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> The email address admin@gmail.com is reserved. Please use a different email address.</div>';
                                exit;
                            }
                            
                            // Get the maximum Customer ID and increment by 1
                            $result = $conn->query("SELECT MAX(Cust_id) as max_id FROM Customer");
                            $row = $result->fetch_assoc();
                            $customer_id = ($row['max_id'] ?? 0) + 1;
                            
                            // Insert customer with the generated ID
                            $customer_sql = "INSERT INTO Customer (Cust_id, Cust_name, Cust_email, Cust_address) 
                                           VALUES ($customer_id, '$name', '$email', '$address')";
                            
                            if (!$conn->query($customer_sql)) {
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error creating customer record: ' . $conn->error . '</div>';
                                exit;
                            }
                        }
                        
                        // Get max Order_id and increment
                        $result = $conn->query("SELECT MAX(Order_id) as max_id FROM Orders");
                        $row = $result->fetch_assoc();
                        $order_id = ($row['max_id'] ?? 0) + 1;
                        
                        // Create order
                        $order_date = date('Y-m-d H:i:s');
                        $order_sql = "INSERT INTO Orders (Order_id, Cust_id, Order_date, Total_amount) 
                                    VALUES ($order_id, $customer_id, '$order_date', $total_amount)";
                        
                        if ($conn->query($order_sql) === TRUE) {
                            // Get max Payment_id and increment
                            $result = $conn->query("SELECT MAX(Payment_id) as max_id FROM Payment");
                            $row = $result->fetch_assoc();
                            $payment_id = ($row['max_id'] ?? 0) + 1;
                            
                            // Create payment
                            $payment_sql = "INSERT INTO Payment (Payment_id, Order_id, Payment_date, Payment_method, Payment_amount) 
                                        VALUES ($payment_id, $order_id, '$order_date', '$payment_method', $total_amount)";
                            
                            if ($conn->query($payment_sql) === TRUE) {
                                // Get max Order_Products_id and increment
                                $result = $conn->query("SELECT MAX(Order_Products_id) as max_id FROM Order_Products");
                                $row = $result->fetch_assoc();
                                $order_product_id = ($row['max_id'] ?? 0) + 1;
                                
                                // Create order product entry
                                $order_product_sql = "INSERT INTO Order_Products (Order_Products_id, Order_id, Product_id, Quantity, Total_Price) 
                                                    VALUES ($order_product_id, $order_id, $product_id, $quantity, $total_amount)";
                                
                                if ($conn->query($order_product_sql) === TRUE) {
                                    // Update product stock
                                    $new_stock = $product['Prod_stock'] - $quantity;
                                    $update_product_sql = "UPDATE Products SET Prod_stock = $new_stock WHERE Product_id = $product_id";
                                    $conn->query($update_product_sql);
                                    
                                    // Update order with payment_id
                                    $update_order_sql = "UPDATE Orders SET Payment_id = $payment_id WHERE Order_id = $order_id";
                                    $conn->query($update_order_sql);
                                    
                                    echo '<div class="order-confirmation">';
                                    echo '<div class="order-success">';
                                    echo '<i class="fas fa-check-circle success-icon"></i>';
                                    echo '<h2>Order Successful!</h2>';
                                    echo '<p>Your order has been placed successfully.</p>';
                                    echo '</div>';
                                    
                                    echo '<div class="order-details">';
                                    echo '<h3><i class="fas fa-info-circle"></i> Order Summary</h3>';
                                    echo '<div class="order-detail-item"><span>Order ID:</span> <span>#' . $order_id . '</span></div>';
                                    echo '<div class="order-detail-item"><span>Product:</span> <span>' . htmlspecialchars($product["Product_name"]) . '</span></div>';
                                    echo '<div class="order-detail-item"><span>Quantity:</span> <span>' . $quantity . '</span></div>';
                                    echo '<div class="order-detail-item"><span>Total Amount:</span> <span>$' . number_format($total_amount, 2) . '</span></div>';
                                    echo '<div class="order-detail-item"><span>Payment Method:</span> <span>' . htmlspecialchars($payment_method) . '</span></div>';
                                    echo '</div>';
                                    
                                    echo '<div class="order-actions">';
                                    echo '<a href="index.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>';
                                    echo '</div>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error creating order details: ' . $conn->error . '</div>';
                                }
                            } else {
                                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error processing payment: ' . $conn->error . '</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error creating order: ' . $conn->error . '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Invalid quantity requested.</div>';
                    }
                } else {
                    // Display payment form
                    ?>
                    <div class="payment-container">
                        <nav class="breadcrumb">
                            <a href="index.php">Home</a> &raquo; 
                            <a href="product.php?id=<?php echo $product_id; ?>"><?php echo htmlspecialchars($product["Product_name"]); ?></a> &raquo; 
                            <span>Checkout</span>
                        </nav>
                        
                        <h2><i class="fas fa-credit-card"></i> Complete Your Purchase</h2>
                        
                        <div class="payment-layout">
                            <div class="payment-form-container">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $product_id); ?>">
                                    <h3><i class="fas fa-user"></i> Customer Information</h3>
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
                                    
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product["Prod_stock"]; ?>" value="1" required>
                                    </div>
                                    
                                    <h3><i class="fas fa-credit-card"></i> Payment Details</h3>
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method:</label>
                                        <select id="payment_method" name="payment_method" required>
                                            <option value="Credit Card">Credit Card</option>
                                            <option value="PayPal">PayPal</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Simplified payment demo - in a real application, you would include secure payment fields -->
                                    <div class="form-group notice">
                                        <p><i class="fas fa-info-circle"></i> This is a demo store. No actual payment will be processed.</p>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-lock"></i> Complete Purchase</button>
                                        <a href="index.php" class="btn"><i class="fas fa-arrow-left"></i> Cancel</a>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="order-summary">
                                <div class="product-summary">
                                    <h3><i class="fas fa-shopping-cart"></i> Order Summary</h3>
                                    <div class="product-info">
                                        <div class="product-image-placeholder"></div>
                                        <div class="product-details">
                                            <h4><?php echo htmlspecialchars($product["Product_name"]); ?></h4>
                                            <p class="product-category"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($product["Category"]); ?></p>
                                            <p class="product-price">$<?php echo number_format($product["Prod_price"], 2); ?> <span class="unit-price">per item</span></p>
                                            <p class="stock-info"><i class="fas fa-box"></i> <?php echo $product["Prod_stock"]; ?> in stock</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="order-total">
                                    <div class="order-total-row">
                                        <span>Subtotal:</span>
                                        <span>$<?php echo number_format($product["Prod_price"], 2); ?></span>
                                    </div>
                                    <div class="order-total-row">
                                        <span>Shipping:</span>
                                        <span>Free</span>
                                    </div>
                                    <div class="order-total-row total">
                                        <span>Total:</span>
                                        <span>$<?php echo number_format($product["Prod_price"], 2); ?></span>
                                    </div>
                                    <div class="order-note">
                                        <small>* Final price will be calculated based on quantity</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Product not found!</div>';
                echo '<a href="index.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Products</a>';
            }
        } else {
            echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Invalid product ID!</div>';
            echo '<a href="index.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Products</a>';
        }
        
        $conn->close();
        ?>
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 