<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ZALORA</title>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        /* About Us page specific styles */
        .about-hero {
            background: linear-gradient(45deg, rgba(246, 36, 89, 0.8), rgba(0, 0, 0, 0.6));
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 60px;
        }
        
        .about-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }
        
        .about-hero h1 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 20px;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .about-hero p {
            font-size: 1.2rem;
            line-height: 1.5;
            margin-bottom: 20px;
            font-weight: 300;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .about-section {
            padding: 60px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .about-section:last-child {
            border-bottom: none;
        }
        
        .about-section h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: var(--gray-800);
            position: relative;
        }
        
        .about-section h2:after {
            content: '';
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .about-intro {
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.7;
            font-size: 1.1rem;
            color: var(--gray-700);
            text-align: center;
            margin-bottom: 40px;
        }
        
        .about-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .about-text h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--gray-800);
            text-align: center;
        }
        
        .about-text p {
            margin-bottom: 20px;
            line-height: 1.6;
            color: var(--gray-700);
            text-align: justify;
        }
        
        .about-values {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .value-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all 0.3s;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .value-icon {
            width: 80px;
            height: 80px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            margin: 0 auto 20px;
            font-size: 2rem;
        }
        
        .value-card h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--gray-800);
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .team-member {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            padding: 30px;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .team-info {
            text-align: center;
        }
        
        .team-info h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: var(--gray-800);
        }
        
        .team-info .position {
            color: var(--primary-color);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 10px;
            display: block;
        }
        
        .team-social {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
        
        .team-social a {
            width: 36px;
            height: 36px;
            background-color: var(--gray-100);
            color: var(--gray-700);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .team-social a:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        @media (max-width: 991px) {
            .about-hero h1 {
                font-size: 2.5rem;
            }
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
                    <li><a href="about.php" class="active">ABOUT US</a></li>
                    <li><a href="admin_manage_product.php">ADMIN</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="about-hero">
        <div class="about-hero-content">
            <h1>About ZALORA</h1>
            <p>Asia's Leading Online Fashion Destination</p>
        </div>
    </div>

    <main class="container">
        <section class="about-section">
            <h2>Our Story</h2>
            <div class="about-intro">
                <p>Founded in 2012, ZALORA has grown to become the leading fashion e-commerce platform in Asia, offering an extensive collection of products from international and local brands.</p>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <h3>From Simple Beginnings</h3>
                    <p>ZALORA began with a simple vision: to revolutionize the way people shop for fashion in Asia. What started as a small team with big dreams has evolved into a company that operates across multiple countries and serves millions of customers.</p>
                    <p>Today, we offer thousands of products across categories including clothing, shoes, accessories, beauty, and lifestyle products. Our platform connects consumers with both international brands and local designers, making fashion accessible to everyone.</p>
                    <p>We're proud of our journey so far, but we're even more excited about the future as we continue to innovate and expand our offerings to meet the evolving needs of our customers.</p>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <h2>Our Mission</h2>
            <div class="about-intro">
                <p>To provide the most engaging and seamless fashion shopping experience for everyone in Asia, while supporting sustainable practices and local communities.</p>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <h3>What Drives Us</h3>
                    <p>At ZALORA, we're passionate about fashion and technology. We believe in the power of self-expression through style and are committed to making fashion accessible to everyone, regardless of where they live.</p>
                    <p>We're also committed to responsible fashion. We work with brands that share our values and are continuously improving our operations to reduce our environmental impact while supporting ethical manufacturing practices.</p>
                    <p>As a tech-driven company, we're constantly innovating to create a shopping experience that's not just convenient but also inspiring and enjoyable. We invest in technology that helps us understand our customers better and deliver personalized experiences that meet their unique needs.</p>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <h2>Our Values</h2>
            <div class="about-intro">
                <p>These core principles guide everything we do at ZALORA, from how we develop our platform to how we interact with our customers and partners.</p>
            </div>
            
            <div class="about-values">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Customer First</h3>
                    <p>We prioritize our customers in everything we do, constantly seeking to understand and anticipate their needs to deliver exceptional experiences.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>We embrace change and continuously challenge ourselves to think creatively and find better ways to serve our customers and partners.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Integrity</h3>
                    <p>We conduct our business with honesty, transparency, and respect, building trust with our customers, partners, and team members.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Sustainability</h3>
                    <p>We're committed to reducing our environmental impact and promoting responsible fashion choices throughout our platform.</p>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <h2>Our Team</h2>
            <div class="about-intro">
                <p>ZALORA is powered by a diverse team of passionate individuals who bring their unique skills, perspectives, and creativity to our mission.</p>
            </div>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="team-info">
                        <h3>Yuvaneshwaren A/L Thiraviasamy</h3>
                        <span class="position">Chief Executive Officer</span>
                        <p>With over 15 years of experience in e-commerce and fashion retail, Sarah leads our company with vision and passion.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="team-info">
                        <h3>Lucas Yin Weng Hou</h3>
                        <span class="position">Chief Technology Officer</span>
                        <p>Michael oversees our technology strategy and leads our engineering teams to build innovative solutions.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="team-info">
                        <h3>Jeremy Tang Yu Cheng</h3>
                        <span class="position">Creative Director</span>
                        <p>Jasmine brings our brand to life through compelling visual storytelling and creative direction.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            
            </div>
        </section>
    </main>

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