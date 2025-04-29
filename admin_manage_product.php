<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
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
                <div class="header-actions">
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
                    <li><a href="admin_manage_product.php"  class="active">ADMIN</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="admin-panel">
            <h2><i class="fas fa-box"></i> Admin - Manage Products</h2>
            
            <?php
            // Include database connection
            include 'db_connection.php';
            
            // Handle delete product
            if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
                $product_id = $_GET['delete'];
                
                // Delete product
                $sql = "DELETE FROM Products WHERE Product_id = $product_id";
                
                if ($conn->query($sql) === TRUE) {
                    echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Product deleted successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error deleting product: ' . $conn->error . '</div>';
                }
            }
            
            // Handle add/edit product form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $product_name = $conn->real_escape_string($_POST['product_name']);
                $description = $conn->real_escape_string($_POST['description']);
                $category = $conn->real_escape_string($_POST['category']);
                $price = floatval($_POST['price']);
                $stock = intval($_POST['stock']);
                
                // Check if editing existing product
                if(isset($_POST['product_id']) && !empty($_POST['product_id'])) {
                    $product_id = intval($_POST['product_id']);
                    
                    // Update existing product
                    $sql = "UPDATE Products SET 
                            Product_name = '$product_name', 
                            Description = '$description', 
                            Category = '$category', 
                            Prod_price = $price, 
                            Prod_stock = $stock 
                            WHERE Product_id = $product_id";
                    
                    if ($conn->query($sql) === TRUE) {
                        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Product updated successfully!</div>';
                    } else {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error updating product: ' . $conn->error . '</div>';
                    }
                } else {
                    // Add new product
                    // Get the maximum Product_id and increment by 1
                    $result = $conn->query("SELECT MAX(Product_id) as max_id FROM Products");
                    $row = $result->fetch_assoc();
                    $new_id = ($row['max_id'] ?? 0) + 1;
                    
                    $sql = "INSERT INTO Products (Product_id, Product_name, Description, Category, Prod_price, Prod_stock) 
                            VALUES ($new_id, '$product_name', '$description', '$category', $price, $stock)";
                    
                    if ($conn->query($sql) === TRUE) {
                        echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Product added successfully!</div>';
                    } else {
                        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error adding product: ' . $conn->error . '</div>';
                    }
                }
            }
            
            // Get product to edit if ID is provided
            $edit_product = null;
            if(isset($_GET['edit']) && is_numeric($_GET['edit'])) {
                $product_id = $_GET['edit'];
                $result = $conn->query("SELECT * FROM Products WHERE Product_id = $product_id");
                
                if ($result->num_rows > 0) {
                    $edit_product = $result->fetch_assoc();
                }
            }
            ?>
            
            <!-- Add/Edit Product Form -->
            <div class="admin-card">
                <h3><?php echo $edit_product ? '<i class="fas fa-edit"></i> Edit Product' : '<i class="fas fa-plus-circle"></i> Add New Product'; ?></h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="admin-form">
                    <?php if($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['Product_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="product_name"><i class="fas fa-tag"></i> Product Name</label>
                        <input type="text" id="product_name" name="product_name" required 
                               value="<?php echo $edit_product ? htmlspecialchars($edit_product['Product_name']) : ''; ?>"
                               placeholder="Enter product name">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group form-group-half">
                            <label for="category"><i class="fas fa-folder"></i> Category</label>
                            <input type="text" id="category" name="category" 
                                   value="<?php echo $edit_product ? htmlspecialchars($edit_product['Category']) : ''; ?>"
                                   placeholder="e.g. Clothing, Electronics, etc.">
                        </div>
                        
                        <div class="form-group form-group-half">
                            <label for="price"><i class="fas fa-dollar-sign"></i> Price</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required 
                                   value="<?php echo $edit_product ? $edit_product['Prod_price'] : ''; ?>"
                                   placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Product Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  placeholder="Provide a detailed description of the product"><?php echo $edit_product ? htmlspecialchars($edit_product['Description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock"><i class="fas fa-cubes"></i> Stock Quantity</label>
                        <input type="number" id="stock" name="stock" min="0" required 
                               value="<?php echo $edit_product ? $edit_product['Prod_stock'] : ''; ?>"
                               placeholder="Number of items in stock">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_product ? '<i class="fas fa-save"></i> Update Product' : '<i class="fas fa-plus"></i> Add Product'; ?>
                        </button>
                        
                        <?php if($edit_product): ?>
                            <a href="admin_manage_product.php" class="btn"><i class="fas fa-times"></i> Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Product List -->
            <div class="admin-card">
                <h3><i class="fas fa-list"></i> Products List</h3>
                <?php
                // Get all products
                $result = $conn->query("SELECT * FROM Products ORDER BY Product_id DESC");
                
                if ($result->num_rows > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table>';
                    echo '<tr>
                            <th width="5%">ID</th>
                            <th width="25%">Name</th>
                            <th width="15%">Category</th>
                            <th width="10%">Price</th>
                            <th width="10%">Stock</th>
                            <th width="15%">Actions</th>
                          </tr>';
                    
                    while($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['Product_id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['Product_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['Category']) . '</td>';
                        echo '<td>$' . number_format($row['Prod_price'], 2) . '</td>';
                        echo '<td>' . $row['Prod_stock'] . '</td>';
                        echo '<td class="actions-cell">
                                <a href="product_show.php?id=' . $row['Product_id'] . '" class="btn btn-sm btn-view" title="View"><i class="fas fa-eye"></i></a>
                                <a href="admin_manage_product.php?edit=' . $row['Product_id'] . '" class="btn btn-sm btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="admin_manage_product.php?delete=' . $row['Product_id'] . '" class="btn btn-sm btn-danger" title="Delete"
                                   onclick="return confirm(\'Are you sure you want to delete this product?\')"><i class="fas fa-trash"></i></a>
                              </td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                    echo '</div>';
                    
                    // Show product count and summary
                    echo '<div class="table-summary">';
                    echo '<p><i class="fas fa-info-circle"></i> Showing ' . $result->num_rows . ' products</p>';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No products found. Add your first product using the form above.</div>';
                }
                
                $conn->close();
                ?>
            </div>
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 