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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Traditional Products</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="nevbar/nevbar.js"></script>
    <link rel="stylesheet" href="nevbar/nevbar.css">
    <link rel="stylesheet" href="nevbar/nevbar2.css">
    <style>
        /* Checkbox group styling */
.checkbox-group {
    display: flex;
    align-items: center;
    margin-top: 15px;
    margin-bottom: 10px;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    cursor: pointer;
    accent-color: #e67e22;
}

.checkbox-group label {
    font-size: 0.9rem;
    color: #555;
    cursor: pointer;
    margin-top: 10px;
}

.checkbox-group label:hover {
    color: #e67e22;
}

/* Form actions styling */
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.save-address-btn {
    padding: 12px 25px;
    background-color: #387447;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background-color 0.3s;
    flex: 1;
}

.save-address-btn:hover {
    background-color: #d35400;
}

.cancel-btn {
    padding:
12px 25px;
  background-color:rgb(212, 83, 83);
  color: #fff4f4;
  border:none;
  border-radius:4px;
  font-size: 0.9rem;
  cursor: pointer;
   transition: all 0.3s;
}

.cancel-btn:hover {
    background-color: #e0e0e0;
    color: #333;
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .save-address-btn, .cancel-btn {
        width: 100%;
    }
}

        .checkout-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://img.freepik.com/free-vector/cashier-supermarket-isolated-person-smiling-woman-employee-uniform-standing-cash-desk-store-employee-working-grocery-shop-market-retail-commerce_575670-1280.jpg?t=st=1744896960~exp=1744900560~hmac=8462170a8f840c0901fb98d7a857b77f10cd05f4fdd44ea5bb7136cfe88b978f&w=1380');
            background-size: cover;
            background-position: center;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .checkout-breadcrumb {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .checkout-breadcrumb a {
            color: #e67e22;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .checkout-breadcrumb a:hover {
            color: #d35400;
        }

        .checkout-breadcrumb i {
            font-size: 0.8rem;
            color: #e67e22;
        }

        .checkout-breadcrumb span {
            color: #fff;
        }

        /* Address cards styles */
        .saved-addresses {
            margin-bottom: 30px;
        }

        .address-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .address-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .address-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .address-card.selected {
            border-color: #e67e22;
            background-color: #fff8ee;
        }

        .address-card h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }

        .address-card p {
            margin: 5px 0;
            color: #666;
            font-size: 0.9em;
        }

        .address-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
        }

        .address-actions button {
            background: none;
            border: none;
            font-size: 0.9em;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .address-actions button:hover {
            color: #e67e22;
        }

        .add-address-card {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
            min-height: 150px;
        }

        .add-address-card:hover {
            border-color: #e67e22;
            background-color: #fff8ee;
        }

        .add-address-card i {
            font-size: 2em;
            color: #ddd;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .add-address-card:hover i {
            color: #e67e22;
        }

        .add-address-card p {
            margin: 0;
            color: #666;
            text-align: center;
        }

        /* Form toggle */
        .toggle-form-btn {
            background: none;
            border: none;
            color: #e67e22;
            cursor: pointer;
            font-size: 0.9em;
            padding: 0;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .toggle-form-btn:hover {
            text-decoration: underline;
        }

        /* Notification */
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
            box-shadow: 0 3px 6px rgba(0,0,0,0.16);
        }

        .notification.success {
            background: #4CAF50;
            color: white;
        }

        .notification.warning {
            background: #ff9800;
            color: white;
        }

        .notification.error {
            background: #f44336;
            color: white;
        }

        .notification i {
            font-size: 20px;
        }

        .notification.show {
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <section class="checkout-hero">
        <div class="hero-content">
            <h1>Checkout</h1>
            <div class="checkout-breadcrumb">
                <a href="index2.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="cart.php">Cart</a>
                <i class="fas fa-chevron-right"></i>
                <span>Checkout</span>
            </div>
        </div>
    </section>

    <div class="checkout-container">
        <h1><i class="fas fa-credit-card"></i> Checkout</h1>
        
        <div class="checkout-grid">
            <div class="checkout-form">
                <!-- Shipping Details Section -->
                <div class="form-section">
                    <h2>Shipping Details</h2>
                    
                    <!-- Saved Addresses Section -->
                    <div class="saved-addresses">
                        <h3>Your Addresses</h3>
                        <div class="address-cards" id="addressCards">
                            <?php
                            // Get saved shipping addresses
                            $address_sql = "SELECT * FROM shipping_addresses WHERE customer_id = ? ORDER BY is_default DESC";
                            $address_stmt = $conn->prepare($address_sql);
                            $address_stmt->bind_param("s", $customer_id);
                            $address_stmt->execute();
                            $address_result = $address_stmt->get_result();
                            
                            $has_addresses = false;
                            
                            if ($address_result->num_rows > 0) {
                                $has_addresses = true;
                                while ($address = $address_result->fetch_assoc()) {
                                    $selected = $address['is_default'] ? 'selected' : '';
                                    ?>
                                    <div class="address-card <?php echo $selected; ?>" data-address-id="<?php echo $address['address_id']; ?>">
                                        <h4><?php echo htmlspecialchars($address['full_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($address['address_line1']); ?></p>
                                        <?php if (!empty($address['address_line2'])): ?>
                                            <p><?php echo htmlspecialchars($address['address_line2']); ?></p>
                                        <?php endif; ?>
                                        <p><?php echo htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['province']); ?></p>
                                        <p><?php echo htmlspecialchars($address['postal_code']); ?></p>
                                        <p>Phone: <?php echo htmlspecialchars($address['phone']); ?></p>
                                        <div class="address-actions">
                                            <button onclick="editAddress(<?php echo $address['address_id']; ?>)"><i class="fas fa-edit"></i></button>
                                            <button onclick="deleteAddress(<?php echo $address['address_id']; ?>)"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <!-- Add New Address Card -->
                            <div class="add-address-card" id="addAddressCard">
                                <i class="fas fa-plus-circle"></i>
                                <p>Add New Address</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- New Address Form -->
                    <div id="newAddressForm" style="display: <?php echo $has_addresses ? 'none' : 'block'; ?>;">
                        <h3 id="addressFormTitle">Add New Address</h3>
                        <form id="shippingForm">
                            <input type="hidden" id="addressId" name="address_id" value="">
                            <div class="input-group">
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="fullName" name="full_name" placeholder="Full Name" required>
                                </div>
                                
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" placeholder="Email" required>
                                </div>
                                
                                <div class="input-with-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
                                </div>
                                
                                <div class="input-with-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <input type="text" id="addressLine1" name="address_line1" placeholder="Address Line 1" required>
                                </div>
                                
                                <div class="input-with-icon">
                                    <i class="fas fa-map-marker"></i>
                                    <input type="text" id="addressLine2" name="address_line2" placeholder="Address Line 2">
                                </div>
                                
                                <div class="input-with-icon">
                                    <i class="fas fa-city"></i>
                                    <input type="text" id="city" name="city" placeholder="City" required>
                                </div>
                                
                                <div class="input-with-icon">
                                    <i class="fas fa-map"></i>
                                    <select id="province" name="province" required>
                                        <option value="">Select Province</option>
                                        <option value="Western">Western Province</option>
                                        <option value="Central">Central Province</option>
                                        <option value="Southern">Southern Province</option>
                                        <option value="Northern">Northern Province</option>
                                        <option value="Eastern">Eastern Province</option>
                                        <option value="North-Western">North-Western Province</option>
                                        <option value="North-Central">North-Central Province</option>
                                        <option value="Uva">Uva Province</option>
                                        <option value="Sabaragamuwa">Sabaragamuwa Province</option>
                                    </select>
                                </div>
                                
                                <div class="input-with-icon">
                                    <i class="fas fa-mail-bulk"></i>
                                    <input type="text" id="postalCode" name="postal_code" placeholder="Postal Code" required>
                                </div>
                                
                                <div class="checkbox-group">
                                    <input type="checkbox" id="isDefault" name="is_default">
                                    <label for="isDefault">Set as default address</label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="save-address-btn">Save Address</button>
                                <?php if ($has_addresses): ?>
                                    <button type="button" class="cancel-btn" id="cancelAddressBtn">Cancel</button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="form-section">
                    <h2>Payment Method</h2>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment" value="cod" checked>
                            <span class="radio-custom"></span>
                            <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="card">
                            <span class="radio-custom"></span>
                            <i class="fas fa-credit-card"></i> Credit/Debit Card
                        </label>
                    </div> 
                    
                    <div class="card-details" id="cardDetails" style="display: none;">
                        <div class="card-fields">
                            <div class="input-with-icon">
                                <i class="fas fa-credit-card"></i>
                                <input type="text" placeholder="Card Number" maxlength="16">
                            </div>
                            <div class="card-row">
                                <div class="input-with-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="text" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                    <input type="text" placeholder="CVV" maxlength="3">
                                </div>
                            </div>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" placeholder="Card Holder Name">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-items">
    <?php
    $cart_sql = "SELECT c.*, p.product_name, p.price, p.offer_price, p.main_category, s.shop_name, 
                (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
                FROM cart c
                JOIN products p ON c.product_id = p.product_id
                JOIN sellers s ON p.seller_id = s.seller_id
                WHERE c.customer_id = ?";
    
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->bind_param("s", $customer_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    $subtotal = 0;
    $shipping = 350.00;
    
    if ($cart_result->num_rows > 0) {
        while ($item = $cart_result->fetch_assoc()) {
            $display_price = isset($item['offer_price']) && $item['offer_price'] > 0 
                ? $item['offer_price'] 
                : $item['price'];
            $item_total = $display_price * $item['quantity'];
            $subtotal += $item_total;
            ?>
            <div class="order-item">
                <div class="item-image">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                    <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                </div>
                <div class="item-details">
                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                    <p class="item-seller"><?php echo htmlspecialchars($item['shop_name']); ?></p>
                </div>
                <div class="item-price">
                    LKR <?php echo number_format($item_total, 2); ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>

                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span class="subtotal">LKR <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span class="shipping">LKR <?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span class="total-amount">LKR <?php echo number_format($subtotal + $shipping, 2); ?></span>
                    </div>
                </div>
                <button class="place-order-btn" id="placeOrderBtn" <?php echo ($cart_result->num_rows == 0) ? 'disabled' : ''; ?>>
                    Place Order
                </button>
            </div>
        </div>
    </div>

    <!-- Notification for actions -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <?php include 'footer/footer.php'; ?>
    
    <script>
        // Toggle payment method details
        const paymentOptions = document.querySelectorAll('input[name="payment"]');
        const cardDetails = document.getElementById('cardDetails');
        
        paymentOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.value === 'card') {
                    cardDetails.style.display = 'block';
                } else {
                    cardDetails.style.display = 'none';
                }
            });
        });
        
        // Address card selection
        const addressCards = document.querySelectorAll('.address-card');
        addressCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't select if clicking on action buttons
                if (e.target.closest('.address-actions')) {
                    return;
                }
                
                // Remove selected class from all cards
                addressCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                this.classList.add('selected');
            });
        });
        
        // Add new address card click handler
        const addAddressCard = document.getElementById('addAddressCard');
        const newAddressForm = document.getElementById('newAddressForm');
        const addressFormTitle = document.getElementById('addressFormTitle');
        
        addAddressCard.addEventListener('click', function() {
            // Reset form
            document.getElementById('shippingForm').reset();
            document.getElementById('addressId').value = '';
            addressFormTitle.textContent = 'Add New Address';
            
            // Show form
            newAddressForm.style.display = 'block';
            
            // Scroll to form
            newAddressForm.scrollIntoView({ behavior: 'smooth' });
        });
        
        // Cancel button for address form
        const cancelAddressBtn = document.getElementById('cancelAddressBtn');
        if (cancelAddressBtn) {
            cancelAddressBtn.addEventListener('click', function(e) {
                e.preventDefault();
                newAddressForm.style.display = 'none';
            });
        }
        
        // Function to show notification
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notification-message');
            
            // Set message
            notificationMessage.textContent = message;
            
            // Reset notification classes
            notification.className = 'notification';
            
            // Set notification type
            notification.classList.add(type);
            
            // Set icon based on type
            const icon = notification.querySelector('i');
            if (type === 'success') {
                icon.className = 'fas fa-check-circle';
            } else if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle';
            } else if (type === 'warning') {
                icon.className = 'fas fa-exclamation-triangle';
            }
            
            // Show notification
            notification.classList.add('show');
            
            // Hide after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Save address form submission
        const shippingForm = document.getElementById('shippingForm');
        shippingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const addressData = {};
            
            // Convert FormData to object
            for (const [key, value] of formData.entries()) {
                addressData[key] = value;
            }
            
            // Send data to server
            fetch('Backend/checkout/save_address_checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(addressData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    
                    // Reload page to show updated addresses
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Failed to save address', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });
        
        // Edit address function
        function editAddress(addressId) {
            // Prevent event bubbling
            event.stopPropagation();
            
            // Fetch address details
            fetch(`Backend/checkout/get_address.php?address_id=${addressId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate form with address data
                    const address = data.address;
                    document.getElementById('addressId').value = address.address_id;
                    document.getElementById('fullName').value = address.full_name;
                    document.getElementById('email').value = address.email;
                    document.getElementById('phone').value = address.phone;
                    document.getElementById('addressLine1').value = address.address_line1;
                    document.getElementById('addressLine2').value = address.address_line2 || '';
                    document.getElementById('city').value = address.city;
                    document.getElementById('province').value = address.province;
                    document.getElementById('postalCode').value = address.postal_code;
                    document.getElementById('isDefault').checked = address.is_default == 1;
                    
                    // Update form title
                    addressFormTitle.textContent = 'Edit Address';
                    
                    // Show form
                    newAddressForm.style.display = 'block';
                    
                    // Scroll to form
                    newAddressForm.scrollIntoView({ behavior: 'smooth' });
                } else {
                    showNotification(data.message || 'Failed to get address details', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
        
        // Delete address function
        function deleteAddress(addressId) {
            // Prevent event bubbling
            event.stopPropagation();
            
            if (confirm('Are you sure you want to delete this address?')) {
                fetch('Backend/checkout/delete_address.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ address_id: addressId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        
                        // Remove address card from DOM
                        const addressCard = document.querySelector(`.address-card[data-address-id="${addressId}"]`);
                        if (addressCard) {
                            addressCard.remove();
                        }
                        
                        // If no addresses left, show form
                        const remainingCards = document.querySelectorAll('.address-card');
                        if (remainingCards.length === 0) {
                            newAddressForm.style.display = 'block';
                        }
                    } else {
                        showNotification(data.message || 'Failed to delete address', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
            }
        }
        
        // Place order button click handler
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        placeOrderBtn.addEventListener('click', function() {
            // Get selected address
            const selectedAddress = document.querySelector('.address-card.selected');
            
            if (!selectedAddress) {
                showNotification('Please select a shipping address', 'warning');
                return;
            }
            
            const addressId = selectedAddress.getAttribute('data-address-id');
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            
            // Prepare order data
            const orderData = {
                address_id: addressId,
                payment_method: paymentMethod
            };
            
            // If card payment, validate card details
            if (paymentMethod === 'card') {
                const cardNumber = document.querySelector('#cardDetails input[placeholder="Card Number"]').value;
                const cardExpiry = document.querySelector('#cardDetails input[placeholder="MM/YY"]').value;
                const cardCvv = document.querySelector('#cardDetails input[placeholder="CVV"]').value;
                const cardName = document.querySelector('#cardDetails input[placeholder="Card Holder Name"]').value;
                
                if (!cardNumber || !cardExpiry || !cardCvv || !cardName) {
                    showNotification('Please fill in all card details', 'warning');
                    return;
                }
                
                // Add card details to order data
                orderData.card_details = {
                    number: cardNumber,
                    expiry: cardExpiry,
                    cvv: cardCvv,
                    name: cardName
                };
            }
            
            // Send order to server
            fetch('Backend/checkout/place_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Order placed successfully!', 'success');
                    
                    // Redirect to order confirmation page
                    setTimeout(() => {
                        window.location.href = `order-confirmation.php?order_id=${data.order_id}`;
                    }, 1000);
                } else {
                    showNotification(data.message || 'Failed to place order', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });
    </script>
</body>
</html>

