<?php
session_start();

if (isset($_SESSION['customer_id']) && $_SESSION['user_type'] === 'customer') {
    include 'Nevbar/nevbar2.php';
} else {
    header("Location: index.html");
    exit();
}

require_once 'Backend/dbconnect.php';
$customer_id = $_SESSION['customer_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | Traditional Products</title>
    <link rel="stylesheet" href="Backend/wishlist/wishlist.css">
    <link rel="stylesheet" href="nevbar/nevbar2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="nevbar/nevbar2.js" defer></script>
    <style>
        /* Additional styles for table layout */
        .wishlist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .wishlist-table th {
            background: #2d3436;
            color: #fff;
            padding: 15px;
            text-align: left;
        }

        .wishlist-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .wishlist-table tr:last-child td {
            border-bottom: none;
        }

        .wishlist-table img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .wishlist-actions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .select-all-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .delete-selected {
            background: #ff4757;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .delete-selected:hover {
            background: #ff6b81;
        }

        .delete-selected:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .wishlist-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {

            .wishlist-table th:nth-child(4),
            .wishlist-table td:nth-child(4),
            .wishlist-table th:nth-child(5),
            .wishlist-table td:nth-child(5) {
                display: none;
            }

            .wishlist-table img {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 480px) {

            .wishlist-table th:nth-child(3),
            .wishlist-table td:nth-child(3) {
                display: none;
            }

            .wishlist-table img {
                width: 50px;
                height: 50px;
            }

            .wishlist-actions-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
        .price-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.original-price {
    text-decoration: line-through;
    color: #999;
    font-size: 0.9rem;
}

.offer-price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 1.1rem;
}

.price {
    color: #2d3436;
    font-weight: bold;
    font-size: 1.1rem;
}

    </style>
</head>

<body>
    <div class="wishlist-container">
        <div class="wishlist-header">
            <h1>My Wishlist</h1>
            <p>Products you've saved for later</p>
        </div>

        <?php
        // Get wishlist items
        $wishlist_sql = "SELECT w.*, p.product_name, p.price, p.offer_price, p.main_category, p.quantity, s.shop_name, 
                         (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
                         FROM wishlists w
                         JOIN products p ON w.product_id = p.product_id
                         JOIN sellers s ON p.seller_id = s.seller_id
                         WHERE w.customer_id = ?
                         ORDER BY w.added_at DESC";

        $wishlist_stmt = $conn->prepare($wishlist_sql);
        $wishlist_stmt->bind_param("s", $customer_id);
        $wishlist_stmt->execute();
        $wishlist_result = $wishlist_stmt->get_result();

        if ($wishlist_result->num_rows > 0) {
            ?>
            <div class="wishlist-actions-header">
                <div class="select-all-container">
                    <div class="checkbox-container">
                        <input type="checkbox" id="select-all" class="wishlist-checkbox">
                        <label for="select-all">Select All</label>
                    </div>
                </div>
                <button id="delete-selected" class="delete-selected" disabled>
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            </div>

            <table class="wishlist-table">
                <thead>
                    <tr>
                        <th width="5%"></th>
                        <th width="15%">Product</th>
                        <th width="25%">Name</th>
                        <th width="15%">Category</th>
                        <th width="15%">Seller</th>
                        <th width="10%">Price</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($item = $wishlist_result->fetch_assoc()) {
                        $image = !empty($item['image_path']) ? $item['image_path'] : 'assets/images/default-product.jpg';
                        ?>
                        <tr data-product-id="<?php echo $item['product_id']; ?>">
                            <td>
                                <input type="checkbox" class="item-checkbox wishlist-checkbox"
                                    data-product-id="<?php echo $item['product_id']; ?>">
                            </td>
                            <td>
                                <img src="<?php echo htmlspecialchars($image); ?>"
                                    alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            </td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['main_category']); ?></td>
                            <td><?php echo htmlspecialchars($item['shop_name']); ?></td>
                            <td>
                                <?php if ($item['offer_price']): ?>
                                    <div class="price-info">
                                        <span class="original-price">LKR <?php echo number_format($item['price'], 2); ?></span>
                                        <span class="offer-price">LKR <?php echo number_format($item['offer_price'], 2); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="price-info">
                                        <span class="price">LKR <?php echo number_format($item['price'], 2); ?></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 10px;">
                                    <?php if ($item['quantity'] > 0): ?>
                                        <button class="add-to-cart" onclick="addProductToCart('<?php echo $item['product_id']; ?>', 
                                            '<?php echo addslashes($item['product_name']); ?>', 
                                            '<?php echo number_format($item['price'], 2); ?>', 
                                            '<?php echo addslashes($image); ?>', 
                                            '<?php echo addslashes($item['main_category']); ?>', 
                                            '<?php echo addslashes($item['shop_name']); ?>')">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="out-stock">Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        } else {
            // Display empty wishlist message
            ?>
            <div class="empty-wishlist">
                <i class="far fa-heart"></i>
                <h2>Your wishlist is empty</h2>
                <p>Add items you love to your wishlist. Review them anytime and easily move them to the cart.</p>
                <a href="index2.php" class="shop-now-btn">Start Shopping</a>
            </div>
            <?php
        }
        ?>
    </div>

    <!-- Notification for actions -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <?php include 'footer/footer.php'; ?>
    <script>
        // Function to update wishlist count in navbar
        function updateWishlistCount(count) {
            const wishlistCount = document.querySelector('.wishlist-count');
            if (wishlistCount) {
                wishlistCount.textContent = count;
                wishlistCount.style.display = count > 0 ? 'block' : 'none';
            }
        }

        // Function to show notification with support for warning type
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');

            // Set message
            notificationMessage.textContent = message;

            // Reset notification classes
            notification.className = 'notification';

            // Set notification type (success, error, or warning)
            if (type === 'error') {
                notification.classList.add('error');
                notification.querySelector('i').className = 'fas fa-exclamation-circle';
            } else if (type === 'warning') {
                notification.classList.add('warning');
                notification.querySelector('i').className = 'fas fa-exclamation-triangle';
            } else {
                notification.querySelector('i').className = 'fas fa-check-circle';
            }

            // Show notification
            notification.classList.add('show');

            // Hide after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        function addProductToCart(productId, productName, price, image, category, shopName) {
            // Implement cart functionality here
            showNotification('Product added to cart', 'success');
        }

        // Initialize wishlist count on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetch('Backend/wishlist/get_wishlist_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateWishlistCount(data.count);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
        // Function to add product to cart
        function addProductToCart(productId, productName, price, image, category, shopName) {
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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count in navbar
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.count;
                            cartCount.style.display = data.count > 0 ? 'block' : 'none';
                        }

                        // Create message with product name
                        let message = '';
                        if (data.added) {
                            message = `"${productName}" added to cart successfully!`;
                        } else {
                            message = `Quantity of "${productName}" updated in cart!`;
                        }

                        // Show success notification
                        showNotification(message, 'success');

                        // Create cart notification
                        const cartNotification = document.createElement('div');
                        cartNotification.className = 'cart-notification';
                        cartNotification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            `;
                        document.body.appendChild(cartNotification);

                        // Show notification
                        setTimeout(() => {
                            cartNotification.classList.add('show');
                        }, 100);

                        // Hide notification after 3 seconds
                        setTimeout(() => {
                            cartNotification.classList.remove('show');
                            setTimeout(() => {
                                cartNotification.remove();
                            }, 300);
                        }, 3000);
                    } else {
                        showNotification(data.message || 'Failed to add to cart', 'warning');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
        }

        // Initialize wishlist count on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetch('Backend/wishlist/get_wishlist_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateWishlistCount(data.count);
                    }
                })
                .catch(error => console.error('Error:', error));

            // Select All functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const deleteSelectedBtn = document.getElementById('delete-selected');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.item-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });

                    // Enable/disable delete button based on selection
                    deleteSelectedBtn.disabled = !this.checked;
                });
            }

            // Individual checkbox functionality
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            if (itemCheckboxes.length > 0) {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        // Check if any checkbox is selected
                        const anyChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
                        deleteSelectedBtn.disabled = !anyChecked;

                        // Update "select all" checkbox
                        const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = allChecked;
                        }
                    });
                });
            }

            // Delete selected items
            if (deleteSelectedBtn) {
                deleteSelectedBtn.addEventListener('click', function () {
                    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
                    if (selectedItems.length === 0) return;

                    const productIds = Array.from(selectedItems).map(item => item.getAttribute('data-product-id'));
                    // Confirm deletion
                    if (confirm(`Are you sure you want to remove ${productIds.length} item(s) from your wishlist?`)) {
                        // Delete multiple items
                        fetch('Backend/wishlist/remove_multiple_from_wishlist.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                product_ids: productIds
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove items from DOM
                                    productIds.forEach(id => {
                                        const item = document.querySelector(`tr[data-product-id="${id}"]`);
                                        if (item) {
                                            item.remove();
                                        }
                                    });

                                    // Update wishlist count in navbar
                                    updateWishlistCount(data.count);

                                    // Show notification
                                    showNotification(`${productIds.length} item(s) removed from wishlist`, 'warning');

                                    // Disable delete button
                                    deleteSelectedBtn.disabled = true;

                                    // Uncheck select all
                                    if (selectAllCheckbox) {
                                        selectAllCheckbox.checked = false;
                                    }

                                    // If no items left, show empty wishlist message
                                    if (data.count === 0) {
                                        const wishlistTable = document.querySelector('.wishlist-table');
                                        const actionsHeader = document.querySelector('.wishlist-actions-header');
                                        if (wishlistTable) {
                                            wishlistTable.remove();
                                        }
                                        if (actionsHeader) {
                                            actionsHeader.remove();
                                        }

                                        const container = document.querySelector('.wishlist-container');
                                        container.innerHTML = `
                                    <div class="wishlist-header">
                                        <h1>My Wishlist</h1>
                                        <p>Products you've saved for later</p>
                                    </div>
                                    <div class="empty-wishlist">
                                        <i class="far fa-heart"></i>
                                        <h2>Your wishlist is empty</h2>
                                        <p>Add items you love to your wishlist. Review them anytime and easily move them to the cart.</p>
                                        <a href="index2.php" class="shop-now-btn">Start Shopping</a>
                                    </div>`;
                                    }
                                } else {
                                    showNotification(data.message || 'Failed to remove items from wishlist', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('An error occurred', 'error');
                            });
                    }
                });
            }
        });
    </script>
</body>

</html>