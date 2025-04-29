<?php
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - E-Commerce Store</title>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        .quantity-selector {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .quantity-selector label {
            margin-right: 15px;
            font-weight: 500;
        }
        
        .quantity-selector input {
            width: 80px;
            padding: 8px;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
        }
        
        .cart-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
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
                
                // Check if "added to cart" message should be shown
                $show_success = isset($_GET['added']) && $_GET['added'] == 1;
                
                echo '<div class="product-detail">';
                echo '<nav class="breadcrumb"><a href="index.php">Home</a> &raquo; ' . htmlspecialchars($product["Product_name"]) . '</nav>';
                
                if ($show_success) {
                    echo '<div class="cart-message success-message">';
                    echo '<i class="fas fa-check-circle"></i>';
                    echo '<div>Product added to your cart successfully!</div>';
                    echo '</div>';
                }
                
                echo '<h1>' . htmlspecialchars($product["Product_name"]) . '</h1>';
                echo '<div class="product-meta">';
                echo '<div class="product-category"><i class="fas fa-tag"></i> Category: ' . htmlspecialchars($product["Category"]) . '</div>';
                echo '<div class="product-price"><i class="fas fa-tag"></i> $' . number_format($product["Prod_price"], 2) . '</div>';
                echo '</div>';
                
                echo '<div class="product-section">';
                echo '<h3><i class="fas fa-info-circle"></i> Product Description</h3>';
                echo '<div class="product-description">' . htmlspecialchars($product["Description"]) . '</div>';
                echo '</div>';
                
                echo '<div class="product-section">';
                echo '<h3><i class="fas fa-box"></i> Availability</h3>'; 
                
                if ($product["Prod_stock"] > 0) {
                    echo '<div class="stock in-stock"><i class="fas fa-check-circle"></i> In Stock: ' . $product["Prod_stock"] . ' items available</div>';
                    
                    // Add to cart form
                    echo '<form action="cart.php?action=add&id=' . $product_id . '&redirect=' . urlencode('product.php?id=' . $product_id . '&added=1') . '" method="GET" style="margin-top: 30px;">';
                    echo '<input type="hidden" name="action" value="add">';
                    echo '<input type="hidden" name="id" value="' . $product_id . '">';
                    echo '<input type="hidden" name="redirect" value="' . urlencode('product.php?id=' . $product_id . '&added=1') . '">';
                    echo '<div class="quantity-selector">';
                    echo '<label for="quantity">Quantity:</label>';
                    echo '<input type="number" name="quantity" id="quantity" value="1" min="1" max="' . $product["Prod_stock"] . '">';
                    echo '</div>';
                    echo '<div class="product-actions">';
                    echo '<button type="submit" class="btn btn-primary btn-large"><i class="fas fa-cart-plus"></i> Add to Cart</button>';
                    echo '<a href="cart.php" class="btn"><i class="fas fa-shopping-cart"></i> View Cart</a>';
                    echo '<a href="product_show.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Products</a>';
                    echo '</div>';
                    echo '</form>';
                    
                } else {
                    echo '<div class="stock out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</div>';
                    echo '<div class="product-actions" style="margin-top: 20px;">';
                    echo '<a href="product_show.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Products</a>';
                    echo '</div>';
                }
                echo '</div>';
                
                echo '</div>';
                
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