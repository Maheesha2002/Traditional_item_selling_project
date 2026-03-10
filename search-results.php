<?php
session_start();
require_once 'Backend/dbconnect.php';

// Get search query
$search_query = isset($_GET['q']) ? $_GET['q'] : '';

// Check if user is logged in
$is_logged_in = isset($_SESSION['customer_id']);
$customer_id = $is_logged_in ? $_SESSION['customer_id'] : '';

// Get wishlist items if user is logged in
$wishlist_products = [];
if ($is_logged_in) {
    $wishlist_sql = "SELECT product_id FROM wishlists WHERE customer_id = ?";
    $wishlist_stmt = $conn->prepare($wishlist_sql);
    $wishlist_stmt->bind_param("s", $customer_id);
    $wishlist_stmt->execute();
    $wishlist_result = $wishlist_stmt->get_result();
    while ($row = $wishlist_result->fetch_assoc()) {
        $wishlist_products[] = $row['product_id'];
    }
}

// Get liked products if user is logged in
$liked_products = [];
if ($is_logged_in) {
    $likes_sql = "SELECT product_id FROM product_likes WHERE customer_id = ?";
    $likes_stmt = $conn->prepare($likes_sql);
    $likes_stmt->bind_param("s", $customer_id);
    $likes_stmt->execute();
    $likes_result = $likes_stmt->get_result();
    while ($row = $likes_result->fetch_assoc()) {
        $liked_products[] = $row['product_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($search_query); ?>" | Traditional Products</title>
    <link rel="stylesheet" href="Nevbar/nevbar2.cs">
    <link rel="stylesheet" href="css/search-results.css">
    <link rel="stylesheet" href="css/category.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?php echo $is_logged_in ? 'logged-in' : ''; ?> search-results-page">

    <?php include 'Nevbar/nevbar2.php'; ?>

    <div class="category-header search-header">
        <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
        <p>Discover our authentic collection of traditional products matching your search.</p>
    </div>

    <div class="breadcrumb">
        <a href="index2.php">Home</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">Search Results</span>
    </div>

    <div class="filter-controls">
        <div class="search-bar">
            <input type="text" id="productSearch" placeholder="Refine your search..." value="<?php echo htmlspecialchars($search_query); ?>">
            <i class="fas fa-search"></i>
        </div>
        <select id="sortSelect">
            <option value="default">Sort By</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="name-az">Name: A to Z</option>
            <option value="name-za">Name: Z to A</option>
            <option value="rating-high">Rating: High to Low</option>
        </select>
    </div>

    <div class="products-grid">
        <?php
            // Build SQL query for search
            $sql = "SELECT p.*, s.shop_name, pi.image_path,
                    (SELECT COUNT(*) FROM product_likes WHERE product_id = p.product_id) as like_count,
                    (SELECT COUNT(*) FROM product_comments WHERE product_id = p.product_id AND parent_id IS NULL) as comment_count,
                    (SELECT AVG(rating) FROM product_ratings WHERE product_id = p.product_id) as avg_rating,
                    (SELECT COUNT(*) FROM product_ratings WHERE product_id = p.product_id) as rating_count
                    FROM products p
                    JOIN sellers s ON p.seller_id = s.seller_id
                    LEFT JOIN product_images pi ON p.product_id = pi.product_id
                    WHERE p.status = 'active' AND (
                        p.product_name LIKE ? OR 
                        p.description LIKE ? OR 
                        p.main_category LIKE ? OR 
                        p.sub_category LIKE ?
                    )";
            
            $search_param = "%" . $search_query . "%";
            $params = [$search_param, $search_param, $search_param, $search_param];
            $types = "ssss";
            
            $sql .= " GROUP BY p.product_id ORDER BY p.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo '<div class="no-products">
                        <p>No products found matching your search for "' . htmlspecialchars($search_query) . '".</p>
                        <p>Try using different keywords or browse our categories.</p>
                        <div class="search-suggestions">
                            <h3>Popular Categories</h3>
                            <div class="suggestion-links">
                                <a href="category.php?main=Traditional%20Masks">Traditional Masks</a>
                                <a href="category.php?main=Batik%20Products">Batik Products</a>
                                <a href="category.php?main=Brass%20Items">Brass Items</a>
                                <a href="category.php?main=Traditional%20Jewelry">Traditional Jewelry</a>
                                <a href="category.php?main=Ceylon%20Tea">Ceylon Tea</a>
                            </div>
                        </div>
                      </div>';
            }

            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $avg_rating = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
                $rating_count = $row['rating_count'] ? $row['rating_count'] : 0;
                $like_count = $row['like_count'] ? $row['like_count'] : 0;
                $comment_count = $row['comment_count'] ? $row['comment_count'] : 0;
                $is_liked = in_array($product_id, $liked_products);
                ?>
                <div class="product-item" data-type="<?php echo htmlspecialchars($row['sub_category']); ?>"
                    data-name="<?php echo htmlspecialchars($row['product_name']); ?>" 
                    data-price="<?php echo $row['price']; ?>"
                    data-rating="<?php echo $avg_rating; ?>">

                    <div class="product-image">
                        <?php if ($row['offer_price']): ?>
                            <div class="offer-badge">
                                <?php
                                $discount = (($row['price'] - $row['offer_price']) / $row['price']) * 100;
                                echo round($discount) . '% OFF';
                                ?>
                            </div>
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                            alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <div class="product-overlay">
                            <button
                                class="add-wishlist <?php echo in_array($row['product_id'], $wishlist_products) ? 'active' : ''; ?>"
                                data-product-id="<?php echo $row['product_id']; ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>

                    <div class="product-details">
                        <div class="product-type"><?php echo htmlspecialchars($row['sub_category']); ?></div>
                        <a href="productDetails.php?id=<?php echo $row['product_id']; ?>" class="product-name-link">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                        </a>
                        
                        <!-- Product Rating -->
                        <div class="product-rating" data-product-id="<?php echo $product_id; ?>">
                            <div class="stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $avg_rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i - 0.5 <= $avg_rating) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="rating-count"><?php echo $avg_rating; ?> (<?php echo $rating_count; ?> reviews)</span>
                        </div>
                        
                        <div class="product-meta">
                            <a href="shop_profile.php?seller_id=<?php echo $row['seller_id']; ?>" class="artisan">
                                <i class="fas fa-store"></i>
                                By <?php echo htmlspecialchars($row['shop_name']); ?>
                            </a>
                        </div>

                        <div class="stock-status">
                            <?php if ($row['quantity'] > 0): ?>
                                <span class="in-stock">
                                    <i class="fas fa-check-circle"></i> In Stock
                                </span>
                            <?php else: ?>
                                <span class="out-of-stock">
                                    <i class="fas fa-times-circle"></i> Out of Stock
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="product-price">
                            <?php if ($row['offer_price']): ?>
                                <div class="price-container">
                                    <span class="original-price">
                                        <i class="fas fa-tag"></i>
                                        LKR <?php echo number_format($row['price'], 2); ?>
                                    </span>
                                    <span class="offer-price">
                                        <i class="fas fa-tags"></i>
                                        LKR <?php echo number_format($row['offer_price'], 2); ?>
                                    </span>
                                </div>
                                <?php else: ?>
                                <span class="regular-price">
                                    <i class="fas fa-tag"></i>
                                    LKR <?php echo number_format($row['price'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="product-actions">
                            <a href="productDetails.php?id=<?php echo $row['product_id']; ?>" class="view-details">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <?php if ($row['quantity'] > 0): ?>
                                <button class="add-to-cart" data-product-id="<?php echo $row['product_id']; ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="notify-me" data-product-id="<?php echo $row['product_id']; ?>">
                                    <i class="fas fa-bell"></i> Notify Me
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
    </div>

    <div class="pagination">
        <!-- Pagination will be added dynamically if needed -->
    </div>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    <!-- Include Footer -->
    <?php include 'footer/footer.php'; ?>
    <?php include 'chat/chat-widget.php'; ?>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    const isLoggedIn = document.body.classList.contains('logged-in');
    
    // Type filter
    const typeButtons = document.querySelectorAll('.type-btn');
    const productItems = document.querySelectorAll('.product-item');
    
    // Initialize comments modal
    initializeCommentsModal();

    // Initialize rating modal
    initializeRatingModal();
    
    // Type filter
    typeButtons.forEach(button => {
        button.addEventListener('click', () => {
            typeButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const selectedType = button.getAttribute('data-type');
            productItems.forEach(item => {
                item.style.display = selectedType === 'all' || 
                                   item.getAttribute('data-type') === selectedType ? 'block' : 'none';
            });
        });
    });

    // Search functionality
    const searchInput = document.getElementById('productSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            productItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                const type = item.getAttribute('data-type').toLowerCase();
                item.style.display = name.includes(searchTerm) || 
                                   type.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortBy = this.value;
            const itemsArray = Array.from(productItems);
            
            itemsArray.sort((a, b) => {
                switch(sortBy) {
                    case 'price-low':
                        return parseFloat(a.getAttribute('data-price')) - 
                               parseFloat(b.getAttribute('data-price'));
                    case 'price-high':
                        return parseFloat(b.getAttribute('data-price')) - 
                               parseFloat(a.getAttribute('data-price'));
                    case 'name-az':
                        return a.getAttribute('data-name')
                                .localeCompare(b.getAttribute('data-name'));
                    case 'name-za':
                        return b.getAttribute('data-name')
                                .localeCompare(a.getAttribute('data-name'));
                    case 'rating-high':
                        return parseFloat(b.getAttribute('data-rating') || 0) - 
                               parseFloat(a.getAttribute('data-rating') || 0);
                    default:
                        return 0;
                }
            });
            
            const productsGrid = document.querySelector('.products-grid');
            if (productsGrid) {
                itemsArray.forEach(item => productsGrid.appendChild(item));
            }
        });
    }

    // Wishlist functionality - UPDATED
    document.querySelectorAll('.add-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            if (!isLoggedIn) {
                showNotification('Please login to add items to your wishlist', 'warning');
                return;
            }
            
            const productId = this.getAttribute('data-product-id');
            const isActive = this.classList.contains('active');
            
            fetch(`Backend/wishlist/${isActive ? 'remove_from_wishlist.php' : 'add_to_wishlist.php'}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.classList.toggle('active');
                    showNotification(
                        isActive ? 'Item removed from wishlist' : 'Item added to wishlist',
                        'success'
                    );
                    updateWishlistCount(data.count);
                } else {
                    showNotification(data.message, 'warning');
                }
            })
            .catch(error => {
                console.error('Wishlist error:', error);
                showNotification('An error occurred', 'error');
            });
        });
    });

    // Cart functionality - UPDATED
    window.addToCart = function(button) {
        if (button.disabled) return;
        
        if (!isLoggedIn) {
            showNotification('Please login to add items to your cart', 'warning');
            return;
        }
        
        const productData = JSON.parse(button.getAttribute('data-product'));
        
        fetch('Backend/cart/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productData.product_id,
                quantity: productData.quantity
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('Item added to cart successfully', 'success');
                updateCartCount(data.count);
            } else {
                showNotification(data.message, 'warning');
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            showNotification('An error occurred', 'error');
        });
    };
    
    // Add to cart button click handler - UPDATED
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            if (!isLoggedIn) {
                showNotification('Please login to add items to your cart', 'warning');
                return;
            }
            
            const productId = this.getAttribute('data-product-id');
            
            fetch('Backend/cart/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('Item added to cart successfully', 'success');
                    updateCartCount(data.count);
                } else {
                    showNotification(data.message, 'warning');
                }
            })
            .catch(error => {
                console.error('Cart error:', error);
                showNotification('An error occurred', 'error');
            });
        });
    });
    
    // Like functionality
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!isLoggedIn) {
                showNotification('Please login to like products', 'warning');
                return;
            }
            
            const productId = this.getAttribute('data-product-id');
            const isLiked = this.classList.contains('active');
            const likeCountElement = this.querySelector('.like-count');
            
            fetch(`Backend/products/toggle_like.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    action: isLiked ? 'unlike' : 'like'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Toggle active class
                    this.classList.toggle('active');
                    
                    // Update icon
                    const icon = this.querySelector('i');
                    icon.className = this.classList.contains('active') ? 'fas fa-thumbs-up' : 'far fa-thumbs-up';
                    
                    // Update count
                    likeCountElement.textContent = data.count;
                    
                    showNotification(
                        isLiked ? 'Product unliked' : 'Product liked',
                        'success'
                    );
                } else {
                    showNotification(data.message, 'warning');
                }
            })
            .catch(error => {
                console.error('Like error:', error);
                showNotification('An error occurred', 'error');
            });
        });
    });

    // Comment button click - Open modal
    document.querySelectorAll('.comment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            
            // Open comments modal
            openCommentsModal(productId, productName);
        });
    });
    
    // Rate button click - Open modal
    document.querySelectorAll('.rate-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (!isLoggedIn) {
                showNotification('Please login to rate products', 'warning');
                return;
            }
            
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            
            // Open rating modal
            openRatingModal(productId, productName);
        });
    });
    
    // Rating functionality
    document.querySelectorAll('.rating-stars').forEach(container => {
        const productId = container.getAttribute('data-product-id');
        const stars = container.querySelectorAll('.star');
        const ratingValue = container.querySelector('.rating-value');
        const userRating = parseInt(container.getAttribute('data-user-rating') || '0');
        
        // Set initial user rating if exists
        if (userRating > 0) {
            highlightStars(stars, userRating);
        }
        
        // Add hover effect
        stars.forEach((star, index) => {
            // Mouseover - preview rating
            star.addEventListener('mouseover', () => {
                highlightStars(stars, index + 1);
            });
            
            // Mouseout - restore original rating
            star.addEventListener('mouseout', () => {
                highlightStars(stars, userRating);
            });
            
            // Click - set rating
            star.addEventListener('click', () => {
                const rating = index + 1;
                
                // Check if user is logged in
                if (!isLoggedIn) {
                    showNotification('Please login to rate products', 'warning');
                    return;
                }
                
                // Send rating to server
                fetch('Backend/products/rate_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        rating: rating
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update user rating
                        container.setAttribute('data-user-rating', rating);
                        
                        // Update display
                        highlightStars(stars, rating);
                        
                        // Update average rating if returned
                        if (data.average_rating) {
                            if (ratingValue) {
                                ratingValue.textContent = parseFloat(data.average_rating).toFixed(1);
                            }
                            
                            // Update product card rating display
                            const productCard = container.closest('.product-item');
                            if (productCard) {
                                const avgRatingDisplay = productCard.querySelector('.average-rating');
                                if (avgRatingDisplay) {
                                    avgRatingDisplay.textContent = parseFloat(data.average_rating).toFixed(1);
                                }
                                
                                // Update rating stars in product card
                                const cardStars = productCard.querySelectorAll('.rating-star');
                                if (cardStars.length > 0) {
                                    updateStarDisplay(cardStars, data.average_rating);
                                }
                            }
                        }
                        
                        showNotification('Rating submitted successfully', 'success');
                    } else {
                        showNotification(data.message || 'Failed to submit rating', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error submitting rating:', error);
                    showNotification('An error occurred while submitting your rating', 'error');
                });
            });
        });
    });
    
    // Initialize product ratings display
    document.querySelectorAll('.product-rating').forEach(container => {
        const rating = parseFloat(container.getAttribute('data-rating') || '0');
        const stars = container.querySelectorAll('.rating-star');
        
        if (stars.length > 0) {
            updateStarDisplay(stars, rating);
        }
    });
});

// Initialize comments modal
function initializeCommentsModal() {
    // Create modal container if it doesn't exist
    if (!document.getElementById('comments-modal')) {
        const modalHTML = `
            <div id="comments-modal" class="comments-modal">
                <div class="comments-modal-content">
                    <div class="comments-modal-header">
                        <h3 id="comments-modal-title">Comments</h3>
                        <button class="comments-modal-close">&times;</button>
                    </div>
                    <div class="comments-modal-body">
                        <div id="modal-comments-container">
                            <div id="modal-comments-list" class="comments-list"></div>
                        </div>
                        <div class="comment-form-container">
                            <div class="comment-form">
                                <input type="text" id="modal-comment-input" class="comment-input" placeholder="Write a comment...">
                                <button id="modal-comment-submit" class="comment-submit">Post</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add event listeners for modal
        const modal = document.getElementById('comments-modal');
        const closeBtn = document.querySelector('.comments-modal-close');
        
        closeBtn.addEventListener('click', function() {
            closeCommentsModal();
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeCommentsModal();
            }
        });
                // Post comment button
                const commentSubmit = document.getElementById('modal-comment-submit');
        commentSubmit.addEventListener('click', function() {
            submitComment();
        });
        
        // Submit comment on Enter key
        const commentInput = document.getElementById('modal-comment-input');
        commentInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                submitComment();
            }
        });
    }
}

// Initialize rating modal
function initializeRatingModal() {
    // Create modal container if it doesn't exist
    if (!document.getElementById('rating-modal')) {
        const modalHTML = `
            <div id="rating-modal" class="rating-modal">
                <div class="rating-modal-content">
                    <div class="rating-modal-header">
                        <h3 id="rating-modal-title">Rate Product</h3>
                        <button class="rating-modal-close">&times;</button>
                    </div>
                    <div class="rating-modal-body">
                        <div class="rating-stars-container">
                            <div id="modal-rating-stars" class="rating-stars large">
                                <span class="star" data-value="1"><i class="far fa-star"></i></span>
                                <span class="star" data-value="2"><i class="far fa-star"></i></span>
                                <span class="star" data-value="3"><i class="far fa-star"></i></span>
                                <span class="star" data-value="4"><i class="far fa-star"></i></span>
                                <span class="star" data-value="5"><i class="far fa-star"></i></span>
                            </div>
                            <div class="rating-value-display">
                                <span id="modal-rating-value">0</span>/5
                            </div>
                        </div>
                        <div class="rating-form-container">
                            <textarea id="modal-review-text" placeholder="Write your review (optional)"></textarea>
                            <button id="modal-rating-submit" class="rating-submit">Submit Rating</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add event listeners for modal
        const modal = document.getElementById('rating-modal');
        const closeBtn = document.querySelector('.rating-modal-close');
        
        closeBtn.addEventListener('click', function() {
            closeRatingModal();
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeRatingModal();
            }
        });
        
        // Rating stars functionality
        const stars = document.querySelectorAll('#modal-rating-stars .star');
        const ratingValue = document.getElementById('modal-rating-value');
        let selectedRating = 0;
        
        stars.forEach((star, index) => {
            // Mouseover - preview rating
            star.addEventListener('mouseover', () => {
                const rating = index + 1;
                highlightModalStars(rating);
                ratingValue.textContent = rating;
            });
            
            // Mouseout - restore selected rating
            star.addEventListener('mouseout', () => {
                highlightModalStars(selectedRating);
                ratingValue.textContent = selectedRating;
            });
            
            // Click - set rating
            star.addEventListener('click', () => {
                selectedRating = index + 1;
                highlightModalStars(selectedRating);
                ratingValue.textContent = selectedRating;
            });
        });
        
        // Submit rating button
        const ratingSubmit = document.getElementById('modal-rating-submit');
        ratingSubmit.addEventListener('click', function() {
            submitRating();
        });
    }
}

// Open comments modal
function openCommentsModal(productId, productName) {
    const modal = document.getElementById('comments-modal');
    const modalTitle = document.getElementById('comments-modal-title');
    const commentsList = document.getElementById('modal-comments-list');
    const commentInput = document.getElementById('modal-comment-input');
    
    // Set modal title
    modalTitle.textContent = `Comments for ${productName}`;
    
    // Clear previous comments
    commentsList.innerHTML = '<div class="loading-comments">Loading comments...</div>';
    
    // Store product ID for comment submission
    commentInput.setAttribute('data-product-id', productId);
    
    // Show modal
    modal.style.display = 'flex';
    
    // Load comments
    loadComments(productId);
}

// Close comments modal
function closeCommentsModal() {
    const modal = document.getElementById('comments-modal');
    modal.style.display = 'none';
}

// Load comments for a product
function loadComments(productId) {
    const commentsList = document.getElementById('modal-comments-list');
    
    fetch(`Backend/products/get_comments.php?product_id=${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.comments.length > 0) {
                    // Render comments
                    commentsList.innerHTML = '';
                    data.comments.forEach(comment => {
                        const commentHTML = createCommentHTML(comment);
                        commentsList.insertAdjacentHTML('beforeend', commentHTML);
                    });
                    
                    // Add reply functionality
                    addReplyFunctionality();
                } else {
                    commentsList.innerHTML = '<div class="no-comments">No comments yet. Be the first to comment!</div>';
                }
            } else {
                commentsList.innerHTML = '<div class="error-message">Failed to load comments</div>';
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
            commentsList.innerHTML = '<div class="error-message">Error loading comments</div>';
        });
}

// Create HTML for a comment
function createCommentHTML(comment) {
    const isReply = comment.parent_id !== null;
    const replyClass = isReply ? 'comment-reply' : '';
    const date = new Date(comment.created_at);
    const formattedDate = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    return `
        <div class="comment ${replyClass}" data-comment-id="${comment.comment_id}">
            <div class="comment-header">
                <div class="comment-user">
                    <img src="${comment.profile_image || 'assets/images/default-avatar.png'}" alt="${comment.full_name}" class="comment-avatar">
                    <div class="comment-user-info">
                        <div class="comment-username">${comment.full_name}</div>
                        <div class="comment-date">${formattedDate}</div>
                    </div>
                </div>
            </div>
            <div class="comment-content">${comment.comment_text}</div>
            <div class="comment-actions">
                <button class="reply-btn" data-comment-id="${comment.comment_id}">Reply</button>
            </div>
            <div class="reply-form-container" id="reply-form-${comment.comment_id}" style="display: none;">
                <div class="reply-form">
                    <input type="text" class="reply-input" placeholder="Write a reply...">
                    <button class="reply-submit" data-comment-id="${comment.comment_id}">Reply</button>
                </div>
            </div>
            <div class="replies" id="replies-${comment.comment_id}"></div>
        </div>
    `;
}

// Add reply functionality to comments
function addReplyFunctionality() {
    // Reply button click
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            
            // Toggle reply form
            replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
            
            // Focus input
            if (replyForm.style.display === 'block') {
                replyForm.querySelector('.reply-input').focus();
            }
        });
    });
    
    // Reply submit button
    document.querySelectorAll('.reply-submit').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const replyInput = this.parentElement.querySelector('.reply-input');
            const replyText = replyInput.value.trim();
            
            if (replyText) {
                submitReply(commentId, replyText);
                replyInput.value = '';
            }
        });
    });
    
    // Submit reply on Enter key
    document.querySelectorAll('.reply-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const commentId = this.parentElement.querySelector('.reply-submit').getAttribute('data-comment-id');
                const replyText = this.value.trim();
                
                if (replyText) {
                    submitReply(commentId, replyText);
                    this.value = '';
                }
            }
        });
    });
}

// Submit a comment
function submitComment() {
    const commentInput = document.getElementById('modal-comment-input');
    const commentText = commentInput.value.trim();
    const productId = commentInput.getAttribute('data-product-id');
    
    if (!commentText) return;
    
    // Check if user is logged in
    if (!document.body.classList.contains('logged-in')) {
        showNotification('Please login to post comments', 'warning');
        return;
    }
    
    fetch('Backend/products/add_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id: productId,
            comment_text: commentText
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Clear input
            commentInput.value = '';
            
            // Reload comments
            loadComments(productId);
            
            // Update comment count on product card
            updateCommentCount(productId);
            
            showNotification('Comment posted successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to post comment', 'error');
        }
    })
    .catch(error => {
        console.error('Error posting comment:', error);
        showNotification('An error occurred while posting your comment', 'error');
    });
}

// Submit a reply
function submitReply(parentId, replyText) {
    // Check if user is logged in
    if (!document.body.classList.contains('logged-in')) {
        showNotification('Please login to post replies', 'warning');
        return;
    }
    
    const commentInput = document.getElementById('modal-comment-input');
    const productId = commentInput.getAttribute('data-product-id');
    
    fetch('Backend/products/add_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id: productId,
            comment_text: replyText,
            parent_id: parentId
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Hide reply form
            document.getElementById(`reply-form-${parentId}`).style.display = 'none';
            
            // Reload comments
            loadComments(productId);
            
            showNotification('Reply posted successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to post reply', 'error');
        }
    })
    .catch(error => {
        console.error('Error posting reply:', error);
        showNotification('An error occurred while posting your reply', 'error');
    });
}

// Update comment count on product card
function updateCommentCount(productId) {
    const commentBtn = document.querySelector(`.comment-btn[data-product-id="${productId}"]`);
    if (commentBtn) {
        const countElement = commentBtn.querySelector('.comment-count');
        if (countElement) {
            const currentCount = parseInt(countElement.textContent);
            countElement.textContent = currentCount + 1;
        }
    }
}

// Open rating modal
function openRatingModal(productId, productName) {
    const modal = document.getElementById('rating-modal');
    const modalTitle = document.getElementById('rating-modal-title');
    const ratingSubmit = document.getElementById('modal-rating-submit');
    
    // Set modal title
    modalTitle.textContent = `Rate ${productName}`;
    
    // Store product ID for rating submission
    ratingSubmit.setAttribute('data-product-id', productId);
    
    // Reset rating
    const stars = document.querySelectorAll('#modal-rating-stars .star');
    highlightModalStars(0);
    document.getElementById('modal-rating-value').textContent = '0';
    document.getElementById('modal-review-text').value = '';
    
    // Show modal
    modal.style.display = 'flex';
    
    // Check if user has already rated
    fetch(`Backend/products/get_user_rating.php?product_id=${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.rating) {
                // Set existing rating
                const rating = parseInt(data.rating);
                highlightModalStars(rating);
                document.getElementById('modal-rating-value').textContent = rating;
                
                // Set review text if exists
                if (data.review_text) {
                    document.getElementById('modal-review-text').value = data.review_text;
                }
            }
        })
        .catch(error => {
            console.error('Error getting user rating:', error);
        });
}

// Close rating modal
function closeRatingModal() {
    const modal = document.getElementById('rating-modal');
    modal.style.display = 'none';
}

// Highlight stars in modal
function highlightModalStars(rating) {
    const stars = document.querySelectorAll('#modal-rating-stars .star');
    stars.forEach((star, index) => {
        const starIcon = star.querySelector('i');
        if (index < rating) {
            starIcon.className = 'fas fa-star';
        } else {
            starIcon.className = 'far fa-star';
        }
    });
}

// Submit rating
function submitRating() {
    const ratingSubmit = document.getElementById('modal-rating-submit');
    const productId = ratingSubmit.getAttribute('data-product-id');
    const ratingValue = document.getElementById('modal-rating-value').textContent;
    const reviewText = document.getElementById('modal-review-text').value.trim();
    
    // Check if rating is selected
    if (parseInt(ratingValue) === 0) {
        showNotification('Please select a rating', 'warning');
        return;
    }
    
    // Check if user is logged in
    if (!document.body.classList.contains('logged-in')) {
        showNotification('Please login to rate products', 'warning');
        return;
    }
    
    fetch('Backend/products/rate_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id: productId,
            rating: parseInt(ratingValue),
            review_text: reviewText
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            closeRatingModal();
            
            // Update rating display on product card
            updateProductRating(productId, data.average_rating);
            
            showNotification('Rating submitted successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to submit rating', 'error');
        }
    })
    .catch(error => {
        console.error('Error submitting rating:', error);
        showNotification('An error occurred while submitting your rating', 'error');
    });
}

// Update product rating display
function updateProductRating(productId, averageRating) {
    const productCard = document.querySelector(`.product-item[data-id="${productId}"]`);
    if (productCard) {
        // Update average rating text
        const ratingText = productCard.querySelector('.average-rating');
        if (ratingText) {
            ratingText.textContent = parseFloat(averageRating).toFixed(1);
        }
        
        // Update stars
        const stars = productCard.querySelectorAll('.rating-star');
        if (stars.length > 0) {
            updateStarDisplay(stars, averageRating);
        }
    }
}

// Update star display based on rating
function updateStarDisplay(stars, rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating - fullStars >= 0.5;
    
    stars.forEach((star, index) => {
        if (index < fullStars) {
            star.className = 'rating-star fas fa-star';
        } else if (index === fullStars && hasHalfStar) {
            star.className = 'rating-star fas fa-star-half-alt';
        } else {
            star.className = 'rating-star far fa-star';
        }
    });
}

// Highlight stars based on rating
function highlightStars(stars, rating) {
    stars.forEach((star, index) => {
        const starIcon = star.querySelector('i');
        if (index < rating) {
            starIcon.className = 'fas fa-star';
        } else {
            starIcon.className = 'far fa-star';
        }
    });
}

// Update wishlist count
function updateWishlistCount(count) {
    const wishlistCount = document.querySelector('.wishlist-count');
    if (wishlistCount) {
        wishlistCount.textContent = count;
    }
}

// Update cart count
function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = count;
    }
}

// Show notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'info' ? 'fa-info-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    const container = document.getElementById('notification-container');
    if (!container) {
        const newContainer = document.createElement('div');
        newContainer.id = 'notification-container';
        document.body.appendChild(newContainer);
        newContainer.appendChild(notification);
    } else {
        container.appendChild(notification);
    }
    
    // Automatically remove notification after 3 seconds
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>


        
           
</body>
</html>

