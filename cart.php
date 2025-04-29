<?php
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Add product to cart
    if ($action == 'add' && isset($_GET['id']) && isset($_GET['quantity'])) {
        $product_id = $_GET['id'];
        $quantity = (int)$_GET['quantity'];
        
        // Ensure quantity is at least 1
        if ($quantity < 1) $quantity = 1;
        
        // Check if product already in cart
        if (isset($_SESSION['cart'][$product_id])) {
            // Update quantity
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            // Add new product
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        // Redirect back to product page or wherever they came from
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'product.php?id=' . $product_id;
        header("Location: $redirect");
        exit;
    }
    
    // Remove product from cart
    if ($action == 'remove' && isset($_GET['id'])) {
        $product_id = $_GET['id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        header("Location: cart.php");
        exit;
    }
    
    // Update quantity
    if ($action == 'update' && isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        header("Location: cart.php");
        exit;
    }
    
    // Clear cart
    if ($action == 'clear') {
        $_SESSION['cart'] = [];
        header("Location: cart.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - ZALORA</title>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        .cart-container {
            padding: 40px 0;
        }
        
        .cart-title {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--gray-800);
        }
        
        .cart-empty {
            text-align: center;
            padding: 60px 20px;
            background-color: var(--gray-100);
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .cart-empty i {
            font-size: 3rem;
            color: var(--gray-400);
            margin-bottom: 20px;
        }
        
        .cart-empty h3 {
            font-size: 1.5rem;
            color: var(--gray-700);
            margin-bottom: 15px;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .cart-table th {
            background-color: var(--gray-100);
            padding: 15px;
            text-align: left;
            font-weight: 500;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
        }
        
        .cart-table td {
            padding: 20px 15px;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }
        
        .cart-product {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .cart-product-name {
            font-weight: 500;
            color: var(--gray-800);
            margin-bottom: 5px;
        }
        
        .cart-product-category {
            color: var(--gray-600);
            font-size: 0.9rem;
        }
        
        .cart-quantity {
            width: 80px;
            padding: 8px;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
        }
        
        .cart-price {
            font-weight: 500;
            color: var(--gray-800);
        }
        
        .cart-remove {
            color: var(--gray-600);
            transition: color 0.3s;
        }
        
        .cart-remove:hover {
            color: var(--primary-color);
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        
        .cart-update {
            padding: 10px 20px;
            background-color: var(--gray-200);
            color: var(--gray-800);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .cart-update:hover {
            background-color: var(--gray-300);
        }
        
        .cart-clear {
            color: var(--gray-600);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .cart-clear:hover {
            color: var(--primary-color);
        }
        
        .cart-summary {
            background-color: var(--gray-100);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .cart-summary-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--gray-800);
        }
        
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .cart-summary-row.total {
            border-top: 1px solid var(--gray-300);
            padding-top: 15px;
            margin-top: 15px;
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        .checkout-button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        
        .checkout-button:hover {
            background-color: var(--primary-color-dark);
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 20px;
            color: var(--gray-600);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .continue-shopping:hover {
            color: var(--primary-color);
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
                    <a href="cart.php" class="header-action-link active">
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
        <div class="cart-container">
            <h1 class="cart-title">Shopping Cart</h1>
            
            <?php
            // Check if cart is empty
            if (empty($_SESSION['cart'])) {
                echo '<div class="cart-empty">';
                echo '<i class="fas fa-shopping-cart"></i>';
                echo '<h3>Your cart is empty</h3>';
                echo '<p>Looks like you haven\'t added any products to your cart yet.</p>';
                echo '<a href="product_show.php" class="btn btn-primary">Browse Products</a>';
                echo '</div>';
            } else {
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
                
                // Display cart contents
                echo '<form method="post" action="cart.php?action=update">';
                echo '<table class="cart-table">';
                echo '<thead><tr>';
                echo '<th>Product</th>';
                echo '<th>Price</th>';
                echo '<th>Quantity</th>';
                echo '<th>Total</th>';
                echo '<th>Action</th>';
                echo '</tr></thead>';
                echo '<tbody>';
                
                foreach ($cart_items as $item) {
                    echo '<tr>';
                    echo '<td>';
                    echo '<div class="cart-product">';
                    echo '<div class="cart-product-info">';
                    echo '<div class="cart-product-name">' . htmlspecialchars($item['Product_name']) . '</div>';
                    echo '<div class="cart-product-category">' . htmlspecialchars($item['Category']) . '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</td>';
                    echo '<td class="cart-price">$' . number_format($item['Prod_price'], 2) . '</td>';
                    echo '<td>';
                    echo '<input type="number" name="quantity[' . $item['Product_id'] . ']" class="cart-quantity" value="' . $item['quantity'] . '" min="1" max="' . $item['Prod_stock'] . '">';
                    echo '</td>';
                    echo '<td class="cart-price">$' . number_format($item['total'], 2) . '</td>';
                    echo '<td>';
                    echo '<a href="cart.php?action=remove&id=' . $item['Product_id'] . '" class="cart-remove"><i class="fas fa-trash"></i></a>';
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody>';
                echo '</table>';
                
                echo '<div class="cart-actions">';
                echo '<button type="submit" name="update_cart" class="cart-update"><i class="fas fa-sync-alt"></i> Update Cart</button>';
                echo '<a href="cart.php?action=clear" class="cart-clear"><i class="fas fa-trash"></i> Clear Cart</a>';
                echo '</div>';
                echo '</form>';
                
                // Cart summary
                echo '<div class="cart-summary">';
                echo '<h3 class="cart-summary-title">Order Summary</h3>';
                echo '<div class="cart-summary-row">';
                echo '<span>Subtotal:</span>';
                echo '<span>$' . number_format($total_price, 2) . '</span>';
                echo '</div>';
                echo '<div class="cart-summary-row">';
                echo '<span>Shipping:</span>';
                echo '<span>Free</span>';
                echo '</div>';
                echo '<div class="cart-summary-row total">';
                echo '<span>Total:</span>';
                echo '<span>$' . number_format($total_price, 2) . '</span>';
                echo '</div>';
                echo '<a href="checkout.php" class="checkout-button"><i class="fas fa-lock"></i> Proceed to Checkout</a>';
                echo '</div>';
                
                echo '<a href="product_show.php" class="continue-shopping"><i class="fas fa-arrow-left"></i> Continue Shopping</a>';
                
                $conn->close();
            }
            ?>
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