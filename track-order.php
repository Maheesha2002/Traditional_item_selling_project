<?php
session_start();

if (isset($_SESSION['customer_id']) && $_SESSION['user_type'] === 'customer') {
    include 'Nevbar/nevbar2.php';
    $customer_id = $_SESSION['customer_id'];
} else {
    header("Location: index.html");
    exit();
}

require_once 'Backend/dbconnect.php';

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: my-orders.php");
    exit();
}

$order_id = $_GET['order_id'];

// Get order details
$order_sql = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("ss", $order_id, $customer_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    header("Location: my-orders.php");
    exit();
}

$order = $order_result->fetch_assoc();

// Define tracking steps based on order status
$steps = [
    ['status' => 'pending', 'label' => 'Order Placed', 'icon' => 'fa-shopping-cart', 'date' => $order['created_at']],
    ['status' => 'processing', 'label' => 'Processing', 'icon' => 'fa-cog', 'date' => null],
    ['status' => 'shipped', 'label' => 'Shipped', 'icon' => 'fa-truck', 'date' => null],
    ['status' => 'delivered', 'label' => 'Delivered', 'icon' => 'fa-check-circle', 'date' => null]
];

// Determine current step
$current_step = 0;
switch ($order['status']) {
    case 'pending':
        $current_step = 0;
        break;
    case 'processing':
        $current_step = 1;
        break;
    case 'shipped':
        $current_step = 2;
        break;
    case 'delivered':
        $current_step = 3;
        break;
}

// Get order items
$items_sql = "SELECT oi.*, p.product_name, 
             (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
             FROM order_items oi
             JOIN products p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("s", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order | Traditional Products</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="nevbar/nevbar2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .tracking-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
            margin-top: 100px;
        }
        
        .tracking-header {
            margin-bottom: 30px;
        }
        
        .tracking-header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .tracking-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .tracking-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        @media (max-width: 992px) {
            .tracking-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .tracking-progress {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .tracking-progress h2 {
            margin-bottom: 30px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        .tracking-steps {
            position: relative;
            padding-left: 45px;
        }
        
        .tracking-steps::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            width: 2px;
            height: 100%;
            background: #ddd;
        }
        
        .tracking-step {
            position: relative;
            padding-bottom: 30px;
        }
        
        .tracking-step:last-child {
            padding-bottom: 0;
        }
        
        .step-icon {
            position: absolute;
            left: -45px;
            width: 40px;
            height: 40px;
            background: #f5f5f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            z-index: 1;
        }
        
        .tracking-step.active .step-icon {
            background: #e67e22;
            color: white;
        }
        
        .tracking-step.completed .step-icon {
            background: #4CAF50;
            color: white;
        }
        
        .step-content {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
        }
        
        .step-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .step-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-summary {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
            position: sticky;
            top: 20px;
        }
        
        .order-summary h2 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        .order-info {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .info-row .label {
            font-weight: 600;
            color: #555;
        }
        
        .info-row .value {
            color: #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .status-badge.pending {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-badge.processing {
            background: #e0cffc;
            color: #5a2ca0;
        }
        
        .status-badge.shipped {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-badge.delivered {
            background: #d4edda;
            color: #155724;
        }
        
        .payment-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .payment-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .payment-badge.paid {
            background: #d4edda;
            color: #155724;
        }
        
        .order-items {
            margin-top: 20px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 15px;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-details h4 {
            margin: 0 0 5px 0;
            font-size: 0.95rem;
            color: #333;
        }
        
        .item-details p {
            margin: 0;
            color: #666;
            font-size: 0.85rem;
        }
        
        .summary-totals {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-row.total {
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 10px;
        }
        
        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        
        .actions a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #e67e22;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .actions a:hover {
            background: #d35400;
        }
    </style>
</head>
<body>
    <div class="tracking-container">
        <div class="tracking-header">
            <h1><i class="fas fa-truck"></i> Track Order</h1>
            <p>Order #<?php echo htmlspecialchars($order_id); ?></p>
        </div>
        
        <div class="tracking-grid">
            <div class="tracking-main">
                <div class="tracking-progress">
                    <h2>Order Progress</h2>
                    <div class="tracking-steps">
                        <?php foreach ($steps as $index => $step): ?>
                            <?php
                            $step_class = '';
                            if ($index < $current_step) {
                                $step_class = 'completed';
                            } elseif ($index === $current_step) {
                                $step_class = 'active';
                            }
                            ?>
                            <div class="tracking-step <?php echo $step_class; ?>">
                                <div class="step-icon">
                                    <i class="fas <?php echo $step['icon']; ?>"></i>
                                </div>
                                <div class="step-content">
                                    <div class="step-title"><?php echo $step['label']; ?></div>
                                    <?php if ($index <= $current_step && $step['date']): ?>
                                        <div class="step-date"><?php echo date('F j, Y, g:i a', strtotime($step['date'])); ?></div>
                                    <?php elseif ($index < $current_step): ?>
                                        <div class="step-date">Completed</div>
                                    <?php elseif ($index === $current_step): ?>
                                        <div class="step-date">In Progress</div>
                                    <?php else: ?>
                                        <div class="step-date">Pending</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="delivery-info">
                    <!-- Additional delivery information can be added here -->
                </div>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-info">
                    <div class="info-row">
                        <span class="label">Order Date:</span>
                        <span class="value"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Status:</span>
                        <span class="value">
                            <span class="status-badge <?php echo $order['status']; ?>">
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Payment:</span>
                        <span class="value">
                            <span class="payment-badge <?php echo $order['payment_status']; ?>">
                                <?php echo ucfirst(htmlspecialchars($order['payment_status'])); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Payment Method:</span>
                        <span class="value"><?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></span>
                    </div>
                </div>
                
                <div class="order-items">
                    <h3>Items</h3>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            </div>
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p>Qty: <?php echo $item['quantity']; ?> × LKR <?php echo number_format($item['price'], 2); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>LKR <?php echo number_format($order['subtotal'], 2); ?></span>
                        </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>LKR <?php echo number_format($order['shipping_fee'], 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>LKR <?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="order-confirmation.php?order_id=<?php echo $order_id; ?>">
                        <i class="fas fa-eye"></i> View Order Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer/footer.php'; ?>
</body>
</html>
