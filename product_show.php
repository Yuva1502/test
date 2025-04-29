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
                    <li><a href="product_show.php" class="active">PRODUCTS</a></li>
                    <li><a href="about.php">ABOUT US</a></li>
                    <li><a href="admin_manage_product.php">ADMIN</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="product-showcase">
            <?php
            // Include database connection
            include 'db_connection.php';
            
            // Display all products
            echo '<div class="section-header">';
            echo '<h2>All Products</h2>';
            echo '<p>Browse our complete collection</p>';
            echo '</div>';
            
            echo '<div class="product-grid">';
            
            // Get all products
            $sql = "SELECT * FROM Products";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<span class="product-badge">Featured</span>';
                    echo '<div class="product-details">';
                    echo '<h3 class="product-name">' . htmlspecialchars($row["Product_name"]) . '</h3>';
                    echo '<div class="product-category">' . htmlspecialchars($row["Category"]) . '</div>';
                    echo '<div class="product-price">$' . number_format($row["Prod_price"], 2) . '</div>';
                    echo '<div class="rating">';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= 4) { // Default 4 stars
                            echo '<i class="fas fa-star"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    echo '<span class="rating-count">(24)</span>';
                    echo '</div>';
                    echo '<div class="product-actions">';
                    echo '<a href="product.php?id=' . $row["Product_id"] . '" class="btn">View</a>';
                    
                    if ($row["Prod_stock"] > 0) {
                        echo '<a href="cart.php?action=add&id=' . $row["Product_id"] . '&quantity=1&redirect=' . urlencode('product_show.php') . '" class="btn btn-primary">Add to Cart</a>';
                    } else {
                        echo '<span class="btn btn-disabled">Out of Stock</span>';
                    }
                    
                    echo '</div>';
                    echo '</div>'; // End product-details
                    echo '</div>'; // End product-card
                }
            } else {
                // Display placeholder products if no data
                for ($i = 1; $i <= 8; $i++) {
                    echo '<div class="product-card">';
                    if ($i % 3 == 0) {
                        echo '<span class="product-badge">20% OFF</span>';
                    }
                    echo '<div class="product-details">';
                    echo '<h3 class="product-name">Fashion Item ' . $i . '</h3>';
                    echo '<div class="product-category">Category Name</div>';
                    echo '<div class="product-price">$' . rand(20, 150) . '.99</div>';
                    echo '<div class="rating">';
                    $rating = rand(3, 5);
                    for ($j = 1; $j <= 5; $j++) {
                        if ($j <= $rating) {
                            echo '<i class="fas fa-star"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    echo '<span class="rating-count">(' . rand(10, 500) . ')</span>';
                    echo '</div>';
                    echo '<div class="product-actions">';
                    echo '<a href="#" class="btn">View</a>';
                    echo '<a href="#" class="btn btn-primary">Add to Cart</a>';
                    echo '</div>';
                    echo '</div>'; // End product-details
                    echo '</div>'; // End product-card
                }
            }
            
            echo '</div>'; // End product-grid
            
            $conn->close();
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

    <!-- JavaScript for interactive elements -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 