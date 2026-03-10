<?php
session_start();

// Always include nevbar.php for non-logged in users
include 'Nevbar/nevbar.php';

// No need to redirect non-logged in users since this is the public page
$is_logged_in = isset($_SESSION['customer_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traditional Products - Home</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/mainslider.css">
    <link rel="stylesheet" href="nevbar/nevbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="nevbar/nevbar.js" defer></script>
    <script src="js/index.js" defer></script>
    <script src="js/gallery.js" defer></script>
    <style>
        /* Recent Products Section Styles */
        .recent-products {
            padding: 80px 20px;
            background: linear-gradient(to bottom, #fff, #f8f9fa);
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-header h2 {
            font-size: 2.2em;
            color: #2d3436;
            margin-bottom: 10px;
        }

        .section-header p {
            color: #636e72;
            font-size: 1.1em;
        }

        .products-carousel {
            position: relative;
            display: flex;
            gap: 60px;
            /* Increased spacing between slides */
            overflow: hidden;
            padding: 20px 0;
            scroll-behavior: smooth;
        }

        .product-slide {
            min-width: 300px;
            padding: 10px;
            transition: transform 0.4s ease;
        }

        .product-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ff4757;
            color: #fff;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 0.8em;
            z-index: 2;
        }

        .product-thumb {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-thumb img {
            transform: scale(1.1);
        }

        .product-quick-actions {
            position: absolute;
            top: 15px;
            right: -50px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: right 0.3s ease;
        }

        .product-card:hover .product-quick-actions {
            right: 15px;
        }

        .product-quick-actions button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: #fff;
            color: #2d3436;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-quick-actions button:hover {
            background: #2d3436;
            color: #fff;
            transform: scale(1.1);
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            color: #636e72;
            font-size: 0.9em;
            display: block;
            margin-bottom: 8px;
        }

        .product-title {
            font-size: 1.1em;
            color: #2d3436;
            margin: 0 0 10px 0;
        }

        .product-seller {
            font-size: 0.85em;
            color: #636e72;
            margin-bottom: 12px;
        }

        .product-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price {
            font-size: 1.2em;
            font-weight: 600;
            color: #2d3436;
        }

        .stock,
        .out-stock {
            font-size: 0.9em;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .stock {
            background: #dff9e3;
            color: #2ecc71;
        }

        .out-stock {
            background: #ffe9e9;
            color: #ff4757;
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .carousel-controls button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: #fff;
            color: #2d3436;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-controls button:hover {
            background: #2d3436;
            color: #fff;
            transform: translateY(-2px);
        }

        /* Cart Notification */
        .cart-notification {
            position: fixed;
            top: 80px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .cart-notification.show {
            transform: translateX(0);
        }

        .cart-notification i {
            font-size: 20px;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
        }

        .notification.success {
            background: #4CAF50;
            color: white;
        }

        .notification.warning {
            background: #ff9800;
            color: white;
        }

        .notification i {
            font-size: 20px;
        }

        .notification.show {
            transform: translateX(0);
        }

        /* Wishlist button styles */
        .add-TO-wishlist {
            background: white;
            color: #333;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-TO-wishlist:hover {
            background: #ff4757;
            color: white;
        }

        .add-TO-wishlist.active i {
            color: #ff4757 !important;
        }

        .add-TO-wishlist.active:hover {
            background: #ff4757;
        }

        .add-TO-wishlist.active:hover i {
            color: white !important;
        }

        @keyframes heartBeat {
            0% {
                transform: scale(1);
            }

            14% {
                transform: scale(1.3);
            }

            28% {
                transform: scale(1);
            }

            42% {
                transform: scale(1.3);
            }

            70% {
                transform: scale(1);
            }
        }

        .add-TO-wishlist:hover i {
            animation: heartBeat 1s ease-in-out;
        }


        @keyframes heartPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .products-carousel {
                gap: 30px;
            }

            .product-slide {
                min-width: 250px;
            }

            .product-thumb {
                height: 180px;
            }
        }

        @media (max-width: 480px) {
            .product-slide {
                min-width: 220px;
            }

            .product-thumb {
                height: 160px;
            }

            .carousel-controls button {
                width: 40px;
                height: 40px;
            }
        }

        .offer-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            z-index: 2;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9em;
            display: block;
        }

        .offer-price {
            color: #e74c3c;
            font-size: 1.1em;
            font-weight: bold;
        }

        .price {
            font-size: 1.2em;
            font-weight: bold;
            color: #2d3436;
        }

        /* Contact form response styling */
        .form-response {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
            font-size: 0.9rem;
        }

        .form-response.success {
            display: block;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-response.error {
            display: block;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Button loading state */
        #submitContactBtn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <!-- Hero Section with Video Background -->
    <section class="hero-section">
        <div class="video-background">
            <video autoplay muted loop>
                <source src="https://videos.pexels.com/video-files/7399305/7399305-hd_1920_1080_24fps.mp4" type="video/mp4">
            </video>
            <div class="overlay"></div>
        </div>

        <div class="hero-content">
            <h1>Discover Sri Lankan Craftsmanship</h1>
            <p>Explore authentic handmade products from talented local artisans</p>
            <div class="hero-buttons">
                <a href="category.php?main=Traditional%20Masks" class="primary-btn action-link">Shop Now</a>
                <a href="#artisans" class="secondary-btn action-link">Meet Our Artisans</a>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Scroll Down</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Recent Products Section -->
    <section class="recent-products">
        <div class="section-header">
            <h2>Latest Traditional Products</h2>
            <p>Discover our newest handcrafted treasures</p>
        </div>

        <div class="products-carousel">
            <?php
            require_once 'Backend/dbconnect.php';

            $products_sql = "SELECT p.*, s.shop_name, GROUP_CONCAT(pi.image_path) as product_images 
                        FROM products p 
                        LEFT JOIN product_images pi ON p.product_id = pi.product_id 
                        JOIN sellers s ON p.seller_id = s.seller_id
                        GROUP BY p.product_id 
                        ORDER BY p.created_at DESC 
                        LIMIT 10";
            $result = $conn->query($products_sql);

            while ($product = $result->fetch_assoc()):
                $images = explode(',', $product['product_images']);
                $first_image = !empty($images[0]) ? $images[0] : 'assets/images/default-product.jpg';
                ?>
                <div class="product-slide" data-product-id="<?php echo $product['product_id']; ?>">
                    <div class="product-card">
                        <?php if ($product['offer_price']): ?>
                            <div class="offer-badge">
                                <?php
                                $discount = (($product['price'] - $product['offer_price']) / $product['price']) * 100;
                                echo round($discount) . '% OFF';
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="badge">New</div>
                        <?php endif; ?>

                        <div class="product-thumb">
                            <img src="<?php echo htmlspecialchars($first_image); ?>"
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <div class="product-quick-actions">
                                <button class="add-TO-wishlist" data-product='<?php
                                echo json_encode([
                                    "id" => $product["product_id"],
                                    "name" => $product["product_name"],
                                    "price" => $product["offer_price"] ?? $product["price"],
                                    "image" => $first_image,
                                    "category" => $product["main_category"],
                                    "seller" => $product["shop_name"]
                                ]);
                                ?>'>
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button class="add-cart" onclick="addToCart(this)" data-product='<?php
                                echo json_encode([
                                    "id" => $product["product_id"],
                                    "name" => $product["product_name"],
                                    "price" => $product["offer_price"] ?? $product["price"],
                                    "image" => $first_image,
                                    "category" => $product["main_category"],
                                    "seller" => $product["shop_name"],
                                    "quantity" => 1
                                ]);
                                ?>'>
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['main_category']); ?></span>
                            <h4 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h4>
                            <div class="product-seller">By <?php echo htmlspecialchars($product['shop_name']); ?></div>
                            <div class="product-price">
                                <?php if ($product['offer_price']): ?>
                                    <span class="original-price">LKR <?php echo number_format($product['price'], 2); ?></span>
                                    <span class="offer-price">LKR
                                        <?php echo number_format($product['offer_price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="price">LKR <?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                                <?php if ($product['quantity'] > 0): ?>
                                    <span class="stock">In Stock</span>
                                <?php else: ?>
                                    <span class="out-stock">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        </div>

        <div class="carousel-controls">
            <button class="prev-btn"><i class="fas fa-chevron-left"></i></button>
            <button class="next-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>
    <!-- Featured Categories Section -->
    <section class="featured-categories">
        <div class="section-header">
            <h2>Explore Categories</h2>
            <p>Discover the finest Sri Lankan traditional crafts</p>
        </div>

        <div class="category-container">
            <?php
            // Get main categories with product counts
            $categories_sql = "SELECT main_category, COUNT(*) as product_count 
                          FROM products 
                          WHERE status = 'active' 
                          GROUP BY main_category 
                          ORDER BY product_count DESC 
                          LIMIT 4";
            $categories_result = $conn->query($categories_sql);

            // Function to get category image URL
            function getCategoryImageUrl($category)
            {
                $image_urls = [
                    'Traditional Masks' => 'https://cdn.pixabay.com/photo/2018/03/15/01/30/sri-lanka-3226884_1280.jpg',
                    'Batik Products' => 'https://cdn.pixabay.com/photo/2020/10/30/00/36/batik-5697482_1280.jpg',
                    'Brass Items' => 'https://img.freepik.com/free-photo/metallic-items-second-hand-market_23-2149338422.jpg?t=st=1744030817~exp=1744034417~hmac=de63879b34bbd283354b9c10468bc7362dea768f6e3054624793c7622673a794&w=740',
                    'Cane Products' => 'https://plus.unsplash.com/premium_photo-1661396950100-bad1e138d97e?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    'Traditional Jewelry' => 'https://plus.unsplash.com/premium_photo-1661645473770-90d750452fa0?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    'Ceylon Tea' => 'https://images.unsplash.com/photo-1737640665064-652965b8edc7?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    'Spices' => 'https://images.unsplash.com/photo-1517646458010-ea6bd9f4a75f?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    'Ceylon Cinnamon' => 'https://plus.unsplash.com/premium_photo-1668445096155-5b97bcaaeac0?q=80&w=1976&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    'Coconut Products' => 'https://images.unsplash.com/photo-1509277953464-58856345caea?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                    'Kithul Products' => 'https://img.freepik.com/free-photo/healthy-jaggery-still-life-arrangement_23-2149161538.jpg?t=st=1744028480~exp=1744032080~hmac=db3bb548b9c4afec0e96e0c732838dd2eacf01a5a3d0e35db0244b787d9fe5a2&w=1380',
                    'Ceylon Gems' => 'https://cdn.pixabay.com/photo/2017/03/22/20/29/stones-2166377_1280.jpg',
                    'Silver Crafts' => 'https://cdn.pixabay.com/photo/2017/04/30/11/41/diamond-2272699_1280.jpg',
                    'Leather Products' => 'https://img.freepik.com/free-photo/leathercraft-hand-sewing-tool-set_1150-6388.jpg?t=st=1744029078~exp=1744032678~hmac=9b99bfd76adfb26528545877dbb314177bd28859fa49b52e44f873d627ce3c58&w=1380',
                    'Traditional Pottery' => 'https://img.freepik.com/free-photo/view-ancient-pottery-vessels-earthenware_23-2151538377.jpg?t=st=1744029408~exp=1744033008~hmac=09b1b6291f4a37bd27b1965a8184e7725bd52516dee1744fb73008229553ab8e&w=1380',
                    'Other' => 'https://img.freepik.com/free-photo/ornate-handmade-beadwork-necklace-complements-traditional-african-garment-generated-by-ai_188544-13800.jpg?t=st=1744029493~exp=1744033093~hmac=6af1334c1978787ae716dc41b4a4e1279722fcb83bc07900cf44d2b9874ab526&w=1380'
                ];

                // Return the URL for the category, or a default image if not found
                return isset($image_urls[$category]) ? $image_urls[$category] : 'assets/images/categories/default.jpg';
            }

            // If no categories found, show default ones
            if ($categories_result->num_rows == 0) {
                $default_categories = [
                    ['name' => 'Traditional Masks', 'count' => 25, 'link' => 'category.php?main=Traditional%20Masks'],
                    ['name' => 'Ceylon Spices', 'count' => 32, 'link' => 'category.php?main=Spices'],
                    ['name' => 'Batik Textiles', 'count' => 40, 'link' => 'category.php?main=Batik%20Products'],
                    ['name' => 'Traditional Jewelry', 'count' => 28, 'link' => 'category.php?main=Traditional%20Jewelry']
                ];

                foreach ($default_categories as $category) {
                    ?>
                    <div class="category-card">
                        <div class="category-image">
                            <img src="<?php echo getCategoryImageUrl($category['name']); ?>"
                                alt="<?php echo htmlspecialchars($category['name']); ?>">
                            <div class="category-overlay"></div>
                        </div>
                        <div class="category-content">
                            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                            <p><?php echo $category['count']; ?> Products</p>
                            <a href="<?php echo $category['link']; ?>" class="explore-btn action-link">Explore <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                // Display categories from database
                while ($category = $categories_result->fetch_assoc()) {
                    $category_name = $category['main_category'];
                    $product_count = $category['product_count'];
                    $category_url = urlencode($category_name);
                    ?>
                    <div class="category-card">
                        <div class="category-image">
                            <img src="<?php echo getCategoryImageUrl($category_name); ?>"
                                alt="<?php echo htmlspecialchars($category_name); ?>">
                            <div class="category-overlay"></div>
                        </div>
                        <div class="category-content">
                            <h3><?php echo htmlspecialchars($category_name); ?></h3>
                            <p><?php echo $product_count; ?> Products</p>
                            <a href="category.php?main=<?php echo $category_url; ?>" class="explore-btn action-link">Explore <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </section>

 <!-- Special Offers Section -->
 <section class="special-offers">
        <div class="section-header">
            <h2>Special Offers</h2>
            <p>Limited time deals on premium Sri Lankan products</p>
        </div>

        <div class="offers-container">
            <div class="offer-card main-offer">
                <div class="offer-image">
                <img src="https://cdn.pixabay.com/photo/2018/03/15/01/30/sri-lanka-3226884_1280.jpg" alt="Special Deal">
                    <div class="offer-tag">50% OFF</div>
                </div>
                <div class="offer-content">
                    <h3>Traditional Mask Collection</h3>
                    <p>Buy any 3 masks and get 50% off on the fourth one</p>
                    <div class="countdown">
                        <span id="days">02</span>d :
                        <span id="hours">18</span>h :
                        <span id="minutes">45</span>m
                    </div>
                    <a href="category.php?main=Traditional%20Masks" class="shop-now-btn">Shop Now</a>

                </div>
            </div>

            <div class="offer-grid">
                <div class="offer-card mini">
                    <div class="offer-tag">30% OFF</div>
                    <img src="https://images.unsplash.com/photo-1517646458010-ea6bd9f4a75f?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Spices">
                    <div class="offer-content">
                        <h4>Ceylon Spices</h4>
                        <a href="category.php?main=Spices" class="view-deal-btn">View Deal</a>
                    </div>
                </div>

                <div class="offer-card mini">
                    <div class="offer-tag">25% OFF</div>
                    <img src="https://images.unsplash.com/photo-1737640665064-652965b8edc7?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Tea">
                    <div class="offer-content">
                        <h4>Premium Tea</h4>
                        <a href="category.php?main=Ceylon%20Tea" class="view-deal-btn">View Deal</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="connect-section">
        <div class="newsletter-box">
            <h2>Join Our Community</h2>
            <p>Subscribe to receive updates about new products and special offers</p>

            <form class="subscribe-form">
                <div class="input-group">
                    <input type="email" placeholder="Enter your email address">
                    <button type="submit" class="action-btn">Subscribe</button>
                </div>
                <label class="checkbox-label">
                    <input type="checkbox">
                    <span>I agree to receive promotional emails</span>
                </label>
            </form>
        </div>

        <div class="social-box">
            <h3>Follow Us</h3>
            <p>Stay connected with us on social media</p>

            <div class="social-grid">
                <a href="#" class="social-card facebook action-link">
                    <i class="fab fa-facebook-f"></i>
                    <span>Facebook</span>
                    <small>50K Followers</small>
                </a>

                <a href="#" class="social-card instagram action-link">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                    <small>35K Followers</small>
                </a>

                <a href="#" class="social-card youtube action-link">
                    <i class="fab fa-youtube"></i>
                    <span>YouTube</span>
                    <small>20K Subscribers</small>
                </a>

                <a href="#" class="social-card pinterest action-link">
                    <i class="fab fa-pinterest-p"></i>
                    <span>Pinterest</span>
                    <small>15K Followers</small>
                </a>
            </div>
        </div>
    </section>

     <!-- Artisans Section -->
     <section class="artisans-section" id="artisans">
        <div class="section-header">
            <h2>Meet Our Master Craftsmen</h2>
            <p>The skilled hands behind our traditional crafts</p>
        </div>

        <div class="artisans-grid">
            <div class="artisan-card">
                <div class="artisan-image">
                    <img src="https://www.itsalltriptome.com/wp-content/uploads/what-to-buy-in-sri-lanka-wood-workshop.jpg" alt="Master Craftsman">
                    <div class="craft-tag">Mask Carving</div>
                </div>
                <div class="artisan-info">
                    <h3>Ananda Perera</h3>
                    <span class="experience">35 Years of Experience</span>
                    <p>Master of traditional devil mask carving from Southern Province</p>
                    <div class="artisan-specialties">
                        <span>Kolam Masks</span>
                        <span>Sanni Masks</span>
                        <span>Raksha Masks</span>
                    </div>
                    <div class="artisan-actions">
                        <a href="#workshop" class="workshop-btn">Join Workshop</a>
                        <a href="#collection" class="collection-btn">View Collection</a>
                    </div>
                </div>
            </div>

            <div class="artisan-card">
                <div class="artisan-image">
                    <img src="https://www.selvedge.org/cdn/shop/products/BAREFOOT_WeavingCentre_DSC9545sq.jpg?v=1628073658&width=1946" alt="Master Weaver">
                    <div class="craft-tag">Handloom Weaving</div>
                </div>
                <div class="artisan-info">
                    <h3>Kumari Silva</h3>
                    <span class="experience">28 Years of Experience</span>
                    <p>Expert in Dumbara weaving techniques from Central Province</p>
                    <div class="artisan-specialties">
                        <span>Dumbara Textiles</span>
                        <span>Traditional Sarees</span>
                        <span>Wall Hangings</span>
                    </div>
                    <div class="artisan-actions">
                        <a href="#workshop" class="workshop-btn">Join Workshop</a>
                        <a href="#collection" class="collection-btn">View Collection</a>
                    </div>
                </div>
            </div>

            <div class="artisan-card">
                <div class="artisan-image">
                    <img src="https://www.grasshopperadventures.com/wp-content/uploads/2019/06/Blog-Sri-Lanka-heartland-wood-carving-handicrafts-tour.jpg" alt="Master Potter">
                    <div class="craft-tag">Pottery</div>
                </div>
                <div class="artisan-info">
                    <h3>Ranjith Fernando</h3>
                    <span class="experience">40 Years of Experience</span>
                    <p>Traditional potter specializing in ceremonial items</p>
                    <div class="artisan-specialties">
                        <span>Ceremonial Pots</span>
                        <span>Decorative Items</span>
                        <span>Traditional Lamps</span>
                    </div>
                    <div class="artisan-actions">
                        <a href="#workshop" class="workshop-btn">Join Workshop</a>
                        <a href="#collection" class="collection-btn">View Collection</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Gallery Section -->
    <section class="gallery-section" id="Gallery">
        <div class="section-header">
            <h2>Craft Gallery</h2>
            <p>Experience the beauty of Sri Lankan craftsmanship</p>
        </div>

        <div class="gallery-filter">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="masks">Masks</button>
            <button class="filter-btn" data-filter="textiles">Textiles</button>
            <button class="filter-btn" data-filter="pottery">Pottery</button>
            <button class="filter-btn" data-filter="jewelry">Jewelry</button>
        </div>

        <div class="gallery-grid">
            <div class="gallery-item masks" data-category="masks">
                <img src="https://i.pinimg.com/736x/8d/20/2b/8d202b24857f3559366733ca9a17a8aa.jpg" alt="Traditional Mask">
                <div class="item-overlay">
                    <h3>Devil Dance Mask</h3>
                    <p>Hand-carved wooden mask</p>
                    <button class="view-btn"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <div class="gallery-item textiles" data-category="textiles">
                <img src="https://duqjpivknq39s.cloudfront.net/2019/02/800x750-12.jpg" alt="Batik Art">
                <div class="item-overlay">
                    <h3>Batik Wall Art</h3>
                    <p>Hand-painted batik design</p>
                    <button class="view-btn"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <div class="gallery-item pottery" data-category="pottery">
                <img src="https://zaaratravels.com/wp-content/uploads/2024/09/7503815665attr.jpg" alt="Clay Pottery">
                <div class="item-overlay">
                    <h3>Ceremonial Pot</h3>
                    <p>Traditional clay pottery</p>
                    <button class="view-btn"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <div class="gallery-item jewelry" data-category="jewelry">
                <img src="https://www.airport.lk/Duty_Free_Shops/Images/135/IMG_9422.jpg" alt="Jewelry">
                <div class="item-overlay">
                    <h3>Traditional Necklace</h3>
                    <p>Handcrafted silver jewelry</p>
                    <button class="view-btn"><i class="fas fa-eye"></i></button>
                </div>
            </div>
        </div>

        <div class="gallery-modal">
            <span class="close-modal">&times;</span>
            <img src="" alt="" id="modal-img">
            <div class="modal-info">
                <h3></h3>
                <p></p>
            </div>
            <button class="modal-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="modal-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="section-header">
            <h2>Why Choose Sri Lankan Crafts</h2>
            <p>Experience the authenticity of traditional craftsmanship</p>
        </div>

        <div class="features-container">
            <div class="feature-box">
                <div class="feature-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3>Handcrafted with Care</h3>
                <p>Each product is carefully crafted by skilled local artisans</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>Authentic Products</h3>
                <p>100% genuine Sri Lankan traditional crafts and products</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Worldwide Shipping</h3>
                <p>Fast and secure delivery to your doorstep</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <h3>Premium Quality</h3>
                <p>Finest materials and traditional techniques</p>
            </div>
        </div>
    </section>

   <!-- Testimonials Section -->
   <section class="testimonials">
        <div class="section-header">
            <h2>What Our Customers Say</h2>
            <p>Real experiences from our valued customers worldwide</p>
        </div>

        <div class="testimonial-slider">
            <div class="testimonial-card">
                <div class="customer-image">
                    <img src="https://overatours.com/wp-content/uploads/2024/11/Sri-Lanka-Shopping-Guide-for-Tourists.jpg" alt="Customer">
                </div>
                <div class="quote-icon">
                    <i class="fas fa-quote-right"></i>
                </div>
                <p class="testimonial-text">
                    "The quality of Sri Lankan handicrafts is exceptional. The mask I purchased is a true piece of art,
                    reflecting rich cultural heritage."
                </p>
                <div class="customer-info">
                    <h4>Sarah Johnson</h4>
                    <p>United Kingdom</p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="customer-image">
                    <img src="https://static.wixstatic.com/media/e2392d_e4b83b3b9feb4e37873a43ae1d61f429~mv2.png/v1/crop/x_2,y_0,w_300,h_302/fill/w_220,h_222,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Screen%20Shot%202018-09-23%20at%2012_54_52.png" alt="Customer">
                </div>
                <div class="quote-icon">
                    <i class="fas fa-quote-right"></i>
                </div>
                <p class="testimonial-text">
                    "I'm impressed with the craftsmanship of the batik products. The colors are vibrant and the designs
                    are unique. Will definitely order again!"
                </p>
                <div class="customer-info">
                    <h4>Michael Chen</h4>
                    <p>Singapore</p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="customer-image">
                    <img src="https://www.law.uchicago.edu/sites/default/files/styles/extra_large/public/2017-10/rodriguez-ayala_emma_4912x7360.a2.jpg?itok=SAGHZPPi" alt="Customer">
                </div>
                <div class="quote-icon">
                    <i class="fas fa-quote-right"></i>
                </div>
                <p class="testimonial-text">
                    "The Ceylon spices I ordered arrived promptly and the quality is outstanding. The aroma and flavor
                    are incomparable to what I find locally."
                </p>
                <div class="customer-info">
                    <h4>Emma Rodriguez</h4>
                    <p>Australia</p>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>We're here to help with any questions about our products</p>

                <div class="info-items">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Visit Us</h4>
                            <p>123 Galle Road, Colombo 03, Sri Lanka</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <h4>Call Us</h4>
                            <p>+94 11 234 5678</p>
                            <p>+94 77 345 6789</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email Us</h4>
                            <p>info@srilankacraft.com</p>
                            <p>support@srilankacraft.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h4>Opening Hours</h4>
                            <p>Monday - Saturday: 9:00 AM - 8:00 PM</p>
                            <p>Sunday: 10:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form id="contactForm">
                    <div class="form-group">
                        <input type="text" name="name" id="contactName" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" id="contactEmail" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" id="contactPhone" placeholder="Your Phone">
                    </div>
                    <div class="form-group">
                        <textarea name="message" id="contactMessage" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" id="submitContactBtn" class="action-btn">Send Message</button>
                </form>
                <div id="contactFormResponse" class="form-response"></div>
            </div>

        </div>

        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.575840369662!2d79.85777147462652!3d6.927203518358902!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae259251b57a431%3A0x8f44e226d6d20a8e!2sGalle%20Rd%2C%20Colombo!5e0!3m2!1sen!2slk!4v1682559541288!5m2!1sen!2slk"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <!-- Notification for cart and wishlist actions -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <!-- Footer -->
    <?php include 'footer/footer.php'; ?>
    <script src="js/special-offers.js"></script>

    <script src="js/cart01.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contactForm = document.getElementById('contactForm');
            const contactFormResponse = document.getElementById('contactFormResponse');

            if (contactForm) {
                contactForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Show loading state
                    const submitBtn = document.getElementById('submitContactBtn');
                    const originalBtnText = submitBtn.textContent;
                    submitBtn.textContent = 'Sending...';
                    submitBtn.disabled = true;

                    // Get form data
                    const formData = new FormData(contactForm);

                    // Add logged in status to form data
                    formData.append('is_logged_in', '<?php echo $is_logged_in ? "1" : "0"; ?>');

                    // Send AJAX request
                    fetch('Backend/contact/save_message.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Reset button
                            submitBtn.textContent = originalBtnText;
                            submitBtn.disabled = false;

                            // Show response message
                            contactFormResponse.textContent = data.message;
                            contactFormResponse.className = 'form-response ' + (data.success ? 'success' : 'error');

                            // Clear form if successful
                            if (data.success) {
                                contactForm.reset();

                                // Hide message after 5 seconds
                                setTimeout(() => {
                                    contactFormResponse.textContent = '';
                                    contactFormResponse.className = 'form-response';
                                }, 5000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            submitBtn.textContent = originalBtnText;
                            submitBtn.disabled = false;
                            contactFormResponse.textContent = 'An error occurred. Please try again later.';
                            contactFormResponse.className = 'form-response error';
                        });
                });
            }
        });

    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Add event listeners to all wishlist buttons
            document.querySelectorAll('.add-TO-wishlist').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // For non-logged in users, redirect to login
                    <?php if (!$is_logged_in): ?>
                        // Store the intended action in session
                        window.location.href = 'index.php#loginModal';
                        return;
                    <?php endif; ?>

                    // Add active class immediately for better user feedback
                    this.classList.toggle('active');

                    const productData = JSON.parse(this.dataset.product);

                    fetch('Backend/wishlist/add_to_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: productData.id
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateWishlistCount(data.count);
                                showNotification(data.message || 'Product added to wishlist successfully!', 'success');
                            } else {
                                // If product is already in wishlist, remove it
                                if (data.exists) {
                                    removeFromWishlist(productData.id);
                                } else {
                                    showNotification(data.message || 'Failed to add to wishlist', 'warning');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred', 'error');
                            // Revert active class on error
                            this.classList.toggle('active');
                        });
                });
            });

            // Function to remove product from wishlist
            function removeFromWishlist(productId) {
                fetch('Backend/wishlist/remove_from_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateWishlistCount(data.count);
                            showNotification('Product removed from wishlist', 'success');
                        } else {
                            showNotification(data.message || 'Failed to remove from wishlist', 'warning');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred', 'error');
                    });
            }

            // Add to Cart functionality
            function addToCart(button) {
                const productData = JSON.parse(button.dataset.product);

                // For non-logged in users, redirect to login
                <?php if (!$is_logged_in): ?>
                    // Store the intended action in session
                    window.location.href = 'index.php#loginModal';
                    return;
                <?php endif; ?>
                fetch('Backend/cart/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productData.id,
                        quantity: productData.quantity || 1
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart count in navbar
                            updateCartCount(data.count);
                            // Show success notification
                            showNotification('Product added to cart successfully!', 'success');
                        } else {
                            showNotification(data.message || 'Failed to add to cart', 'warning');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred', 'error');
                    });
            }

            // Show notification function
            function showNotification(message, type = 'success') {
                const notification = document.getElementById('notification');
                if (notification) {
                    notification.className = `notification ${type}`;
                    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
                    notification.classList.add('show');
                    setTimeout(() => notification.classList.remove('show'), 3000);
                }
            }

            // Update wishlist count
            function updateWishlistCount(count) {
                const wishlistCount = document.querySelector('.wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = count;
                    wishlistCount.style.display = count > 0 ? 'block' : 'none';
                }
            }

            // Update cart count
            function updateCartCount(count) {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = count;
                    cartCount.style.display = count > 0 ? 'block' : 'none';
                }
            }

            // Initialize wishlist count
            function initializeWishlistCount() {
                <?php if ($is_logged_in): ?>
                    fetch('Backend/wishlist/get_wishlist_count.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateWishlistCount(data.count);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                <?php else: ?>
                    // For non-logged in users, set count to 0
                    updateWishlistCount(0);
                <?php endif; ?>
            }

            // Initialize cart count
            function initializeCartCount() {
                <?php if ($is_logged_in): ?>
                    fetch('Backend/cart/get_cart_count.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateCartCount(data.count);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                <?php else: ?>
                    // For non-logged in users, set count to 0
                    updateCartCount(0);
                <?php endif; ?>
            }

            // Check if product is in wishlist and update UI
            function checkWishlistStatus() {
                <?php if ($is_logged_in): ?>
                    fetch('Backend/wishlist/get_wishlist_items.php')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.items) {
                                // Get all wishlist buttons
                                const wishlistButtons = document.querySelectorAll('.add-TO-wishlist');

                                // For each button, check if the product is in the wishlist
                                wishlistButtons.forEach(button => {
                                    try {
                                        const productData = JSON.parse(button.dataset.product);
                                        const isInWishlist = data.items.some(item => item.product_id === productData.id);

                                        // If the product is in the wishlist, add the active class
                                        if (isInWishlist) {
                                            button.classList.add('active');
                                        }
                                    } catch (error) {
                                        console.error('Error parsing product data:', error);
                                    }
                                });
                            }
                        })
                        .catch(error => console.error('Error checking wishlist status:', error));
                <?php endif; ?>
            }

            // Initialize counts on page load
            initializeWishlistCount();
            initializeCartCount();
            checkWishlistStatus(); // Check wishlist status on page load

            // Carousel functionality
            const carousel = document.querySelector('.products-carousel');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            if (carousel && prevBtn && nextBtn) {
                const slideWidth = document.querySelector('.product-slide').offsetWidth + 60; // Including gap
                const visibleSlides = Math.floor(carousel.offsetWidth / slideWidth);
                let currentPosition = 0;

                nextBtn.addEventListener('click', () => {
                    const maxScroll = carousel.scrollWidth - carousel.offsetWidth;
                    currentPosition = Math.min(currentPosition + slideWidth, maxScroll);
                    carousel.scroll({
                        left: currentPosition,
                        behavior: 'smooth'
                    });
                    updateButtonStates();
                });

                prevBtn.addEventListener('click', () => {
                    currentPosition = Math.max(currentPosition - slideWidth, 0);
                    carousel.scroll({
                        left: currentPosition,
                        behavior: 'smooth'
                    });
                    updateButtonStates();
                });

                function updateButtonStates() {
                    prevBtn.disabled = currentPosition === 0;
                    nextBtn.disabled = currentPosition >= carousel.scrollWidth - carousel.offsetWidth;
                    prevBtn.style.opacity = prevBtn.disabled ? '0.5' : '1';
                    nextBtn.style.opacity = nextBtn.disabled ? '0.5' : '1';
                }

                // Initial button states
                updateButtonStates();

                // Update on window resize
                window.addEventListener('resize', updateButtonStates);
            }

            // Countdown Timer for Special Offers
            function startCountdown() {
                const countDownDate = new Date();
                countDownDate.setHours(countDownDate.getHours() + 48);

                const x = setInterval(function () {
                    const now = new Date().getTime();
                    const distance = countDownDate - now;

                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("days").innerHTML = "00";
                        document.getElementById("hours").innerHTML = "00";
                        document.getElementById("minutes").innerHTML = "00";
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                    document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
                    document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
                    document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
                }, 1000);
            }

            // Initialize countdown
            startCountdown();

            // Make functions available globally
            window.addToCart = addToCart;
            window.updateCartCount = updateCartCount;
            window.updateWishlistCount = updateWishlistCount;

            // For non-logged in users, intercept all action links
            <?php if (!$is_logged_in): ?>
                document.querySelectorAll('.action-link, .action-btn').forEach(element => {
                    element.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Store the intended URL to redirect after login
                        const targetUrl = this.getAttribute('href') || window.location.href;
                        sessionStorage.setItem('redirectAfterLogin', targetUrl);

                        // Open login modal
                        window.location.href = 'index.php#loginModal';
                    });
                });

                // Intercept product card clicks
                document.querySelectorAll('.product-card').forEach(card => {
                    card.addEventListener('click', function (e) {
                        if (!e.target.closest('.product-quick-actions')) {
                            e.preventDefault();
                            const productId = this.closest('.product-slide').dataset.productId;
                            sessionStorage.setItem('redirectAfterLogin', `product.php?id=${productId}`);
                            window.location.href = 'index.php#loginModal';
                        }
                    });
                });
            <?php endif; ?>
        });
    </script>

    <!-- Login redirect script -->
    <script>
        // // Check if there's a redirect URL stored in session storage after login
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (!$is_logged_in): ?>
                // Get all links and buttons that require login
                const allElements = document.querySelectorAll('a, button');

                allElements.forEach(element => {
                    // Skip elements that should not require login
                    if (element.classList.contains('no-login-required') ||
                        element.classList.contains('add-TO-wishlist') ||
                        element.classList.contains('add-cart') ||
                        element.closest('.product-quick-actions') ||
                        element.id === 'loginSubmitBtn' ||
                        element.id === 'signupSubmitBtn' ||
                        element.id === 'submitContactBtn' ||
                        element.closest('#loginModal') ||
                        element.closest('#signupModal') ||
                        element.closest('#contactForm')) {
                        return; // Skip this element
                    }

                    // Skip navigation elements
                    if (element.closest('.navbar') ||
                        element.closest('.footer') ||
                        element.classList.contains('close-modal') ||
                        element.classList.contains('filter-btn')) {
                        return; // Skip this element
                    }

                    // Skip page anchor links
                    if (element.getAttribute('href') && element.getAttribute('href').startsWith('#')) {
                        return; // Skip this element
                    }

                    // Add click event listener to redirect to login
                    element.addEventListener('click', function (e) {
                        e.preventDefault();
                        window.location.href = 'index.php#loginModal';
                    });
                });
            <?php endif; ?>
        });

    </script>
</body>

</html>