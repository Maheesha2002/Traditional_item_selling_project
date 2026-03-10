<?php
session_start();

// Check if user is logged in as customer
if (!isset($_SESSION['customer_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: index.php");
    exit();
}

// Include appropriate navbar
if (isset($_SESSION['customer_id']) && $_SESSION['user_type'] === 'customer') {
    include 'Nevbar/nevbar2.php';
} else {
    include 'Nevbar/nevbar.php';
}

require_once 'Backend/dbconnect.php';

// Get customer ID
$customer_id = $_SESSION['customer_id'];

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("Location: Myprofile.php#orders");
    exit();
}

$order_id = $_GET['id'];

// Get order details
$order_sql = "SELECT o.* FROM orders o WHERE o.order_id = ? AND o.customer_id = ?";
$order_stmt = $conn->prepare($order_sql);

// Check if prepare was successful
if ($order_stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$order_stmt->bind_param("ss", $order_id, $customer_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    header("Location: Myprofile.php#orders");
    exit();
}

$order = $order_result->fetch_assoc();

// Get shipping address
$address_sql = "SELECT * FROM shipping_addresses WHERE address_id = ?";
$address_stmt = $conn->prepare($address_sql);

// Check if prepare was successful
if ($address_stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$address_stmt->bind_param("i", $order['address_id']);
$address_stmt->execute();
$address_result = $address_stmt->get_result();
$shipping_address = $address_result->fetch_assoc();

// Get order items
$items_sql = "SELECT oi.*, p.product_name, s.shop_name, 
             (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
             FROM order_items oi
             JOIN products p ON oi.product_id = p.product_id
             JOIN sellers s ON oi.seller_id = s.seller_id
             WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);

// Check if prepare was successful
if ($items_stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$items_stmt->bind_param("s", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | Traditional Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/myprofile.css">
    <link rel="stylesheet" href="nevbar/nevbar2.css">
    <style>
        .order-details-container {
            max-width: 1200px;
            margin: 80px auto 30px;
            padding: 0 20px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .order-header h1 {
            font-size: 2rem;
            color: #333;
            margin: 0;
        }

        .order-header .order-id {
            color: #666;
            font-size: 1.1rem;
        }

        .order-actions {
            display: flex;
            gap: 15px;
        }

        .order-actions a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .track-order-btn {
            background-color: #e67e22;
            color: white;
        }

        .track-order-btn:hover {
            background-color: #d35400;
        }

        .cancel-order-btn {
            background-color: #f5f5f5;
            color: #666;
        }

        .cancel-order-btn:hover {
            background-color: #e0e0e0;
        }

        .order-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .order-grid {
                grid-template-columns: 1fr;
            }
        }

        .order-main {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 25px;
        }

        .order-card h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .order-items {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-item {
            display: flex;
            border-bottom: 1px solid #f5f5f5;
            padding-bottom: 20px;
        }

        .order-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .item-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 20px;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin: 0 0 5px 0;
            font-size: 1.1rem;
        }

        .item-seller {
            color: #666;
            margin: 0 0 10px 0;
            font-size: 0.9rem;
        }

        .item-price {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .item-quantity {
            color: #666;
        }

        .item-total {
            font-weight: 600;
            color: #333;
        }

        .shipping-address {
            margin-bottom: 20px;
        }

        .address-card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
        }

        .address-name {
            font-weight: 600;
            margin: 0 0 5px 0;
        }

        .address-line {
            margin: 0 0 5px 0;
            color: #666;
        }

        .payment-method {
            margin-bottom: 20px;
        }

        .payment-card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
        }

        .payment-icon {
            margin-right: 15px;
            font-size: 1.5rem;
            color: #666;
        }

        .payment-details {
            flex: 1;
        }

        .payment-name {
            font-weight: 600;
            margin: 0 0 5px 0;
        }

        .payment-info {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .order-summary {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 25px;
            position: sticky;
            top: 20px;
        }

        .order-summary h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row .label {
            color: #666;
        }

        .summary-row .value {
            font-weight: 600;
            color: #333;
        }

        .summary-row.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 1.2rem;
        }

        .summary-row.total .label {
            color: #333;
        }

        .summary-row.total .value {
            color: #e67e22;
        }

        .order-status {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .status-label {
            margin-bottom: 10px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            width: 100%;
        }

        .status-badge.pending {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-badge.processing {
            background-color: #e0cffc;
            color: #5a2ca0;
        }

        .status-badge.shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .payment-status {
            margin-top: 15px;
        }

        .payment-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            width: 100%;
        }

        .payment-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .payment-badge.paid {
            background-color: #d4edda;
            color: #155724;
        }

        .payment-badge.failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .order-date {
            margin-top: 15px;
            color: #666;
            font-size: 0.9rem;
        }

        .write-review-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #f5f5f5;
            color: #333;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .write-review-btn:hover {
            background-color: #e0e0e0;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .cancel-btn {
            padding: 10px 20px;
            background: #f5f5f5;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .save-btn {
            padding: 10px 20px;
            background: #e67e22;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="order-details-container">
        <div class="order-header">
            <div>
                <h1>Order Details</h1>
                <p class="order-id">Order #<?php echo htmlspecialchars($order_id); ?></p>
            </div>
            <div class="order-actions">
                <a href="track-order.php?order_id=<?php echo $order_id; ?>" class="track-order-btn">
                    <i class="fas fa-truck"></i> Track Order
                </a>
                <?php if ($order['status'] === 'pending'): ?>
                <a href="#" class="cancel-order-btn" id="cancelOrderBtn">
                    <i class="fas fa-times"></i> Cancel Order
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="order-grid">
            <div class="order-main">
                <div class="order-card">
                    <h2>Order Items</h2>
                    <div class="order-items">
                        <?php while ($item = $items_result->fetch_assoc()): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                </div>
                                <div class="item-details">
                                    <h3 class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                    <p class="item-seller">Sold by: <?php echo htmlspecialchars($item['shop_name']); ?></p>
                                    <div class="item-price">
                                        <span class="item-quantity"><?php echo $item['quantity']; ?> × LKR <?php echo number_format($item['price'], 2); ?></span>
                                        <span class="item-total">LKR <?php echo number_format($item['item_total'], 2); ?></span>
                                    </div>
                                    <?php if ($order['status'] === 'delivered'): ?>
                                        <a href="write_review.php?product_id=<?php echo $item['product_id']; ?>&order_id=<?php echo $order_id; ?>" class="write-review-btn">
                                            <i class="fas fa-star"></i> Write a Review
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="order-card">
                    <h2>Shipping Information</h2>
                    <div class="shipping-address">
                        <h3>Delivery Address</h3>
                        <div class="address-card">
                            <p class="address-name"><?php echo htmlspecialchars($shipping_address['full_name']); ?></p>
                            <p class="address-line"><?php echo htmlspecialchars($shipping_address['address_line1']); ?></p>
                            <?php if (!empty($shipping_address['address_line2'])): ?>
                                <p class="address-line"><?php echo htmlspecialchars($shipping_address['address_line2']); ?></p>
                            <?php endif; ?>
                            <p class="address-line">
                                <?php echo htmlspecialchars($shipping_address['city']); ?>, 
                                <?php echo htmlspecialchars($shipping_address['province']); ?> 
                                <?php echo htmlspecialchars($shipping_address['postal_code']); ?>
                            </p>
                            <p class="address-line">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($shipping_address['phone']); ?>
                            </p>
                            <p class="address-line">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($shipping_address['email']); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="payment-method">
                        <h3>Payment Method</h3>
                        <div class="payment-card">
                            <?php if ($order['payment_method'] === 'cash_on_delivery'): ?>
                                <div class="payment-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="payment-details">
                                    <p class="payment-name">Cash on Delivery</p>
                                    <p class="payment-info">Pay when you receive your order</p>
                                </div>
                            <?php elseif ($order['payment_method'] === 'card'): ?>
                                <div class="payment-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="payment-details">
                                    <p class="payment-name">Credit/Debit Card</p>
                                    <p class="payment-info">Online payment</p>
                                </div>
                            <?php elseif ($order['payment_method'] === 'bank_transfer'): ?>
                                <div class="payment-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-details">
                                    <p class="payment-name">Bank Transfer</p>
                                    <p class="payment-info">Bank payment</p>
                                </div>
                            <?php else: ?>
                                <div class="payment-icon">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="payment-details">
                                    <p class="payment-name"><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-row">
                    <span class="label">Subtotal</span>
                    <span class="value">LKR <?php echo number_format($order['subtotal'], 2); ?></span>
                </div>
                <div class="summary-row">
                    <span class="label">Shipping Fee</span>
                    <span class="value">LKR <?php echo number_format($order['shipping_fee'], 2); ?></span>
                </div>
                <?php 
                // Calculate discount if any (difference between subtotal + shipping and total)
                $discount = ($order['subtotal'] + $order['shipping_fee']) - $order['total_amount'];
                if ($discount > 0): 
                ?>
                <div class="summary-row">
                    <span class="label">Discount</span>
                    <span class="value">-LKR <?php echo number_format($discount, 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="summary-row total">
                    <span class="label">Total</span>
                    <span class="value">LKR <?php echo number_format($order['total_amount'], 2); ?></span>
                </div>

                <div class="order-status">
                    <p class="status-label">Order Status</p>
                    <div class="status-badge <?php echo strtolower($order['status']); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </div>
                    
                    <div class="payment-status">
                        <p class="status-label">Payment Status</p>
                        <div class="payment-badge <?php echo strtolower($order['payment_status']); ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </div>
                    </div>
                    
                    <p class="order-date">
                        <i class="far fa-calendar-alt"></i> Ordered on: <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Order Cancellation -->
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cancel Order</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this order? This action cannot be undone.</p>
                <div class="form-actions">
                    <button class="cancel-btn" id="cancelNoBtn">No, Keep Order</button>
                    <button class="save-btn" id="cancelYesBtn" style="background-color: #e74c3c;">Yes, Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cancel Order Modal
            const cancelOrderBtn = document.getElementById('cancelOrderBtn');
            const cancelModal = document.getElementById('cancelModal');
            const closeModalBtn = document.querySelector('.close-modal');
            const cancelNoBtn = document.getElementById('cancelNoBtn');
            const cancelYesBtn = document.getElementById('cancelYesBtn');
            
            if (cancelOrderBtn) {
                cancelOrderBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    cancelModal.style.display = 'block';
                });
            }
            
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function() {
                    cancelModal.style.display = 'none';
                });
            }
            
            if (cancelNoBtn) {
                cancelNoBtn.addEventListener('click', function() {
                    cancelModal.style.display = 'none';
                });
            }
            
            if (cancelYesBtn) {
                cancelYesBtn.addEventListener('click', function() {
                    // Send request to cancel order
                    fetch('Backend/orders/cancel_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ order_id: '<?php echo $order_id; ?>' }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Redirect to profile page with success message
                            window.location.href = 'Myprofile.php?success=' + encodeURIComponent('Order cancelled successfully');
                        } else {
                            alert(data.message || 'Failed to cancel order');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while cancelling the order');
                    });
                });
            }
            
            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === cancelModal) {
                    cancelModal.style.display = 'none';
                }
            });
        });
    </script>

    <?php include 'footer/footer.php'; ?>
</body>
</html>
