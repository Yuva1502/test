<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

// Create connection without specifying database first
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // Database created successfully or already exists
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Now check if tables exist and create them if they don't
if (!$conn->query("SHOW TABLES LIKE 'Products'")->num_rows) {
    die("<div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
        <h2>Database Setup Required</h2>
        <p>Database tables not found. Please follow these steps:</p>
        <ol>
            <li>Open phpMyAdmin (typically at <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a>)</li>
            <li>Click on the 'Import' tab at the top</li>
            <li>Click 'Choose File' and select the 'e-cormmerce.sql' file from your project folder</li>
            <li>Click 'Go' at the bottom to import the database structure</li>
            <li>Return to this page after the import is complete</li>
        </ol>
    </div>");
}
?> 