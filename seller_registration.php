<?php
session_start();
require_once 'Backend/dbconnect.php';

// Check if user is logged in as a customer
$is_logged_in = isset($_SESSION['customer_id']) && $_SESSION['user_type'] === 'customer';
$customer_id = $is_logged_in ? $_SESSION['customer_id'] : null;

// If not logged in, redirect to login page with a message
if (!$is_logged_in) {
    // Store the intended destination for after login
    $_SESSION['redirect_after_login'] = 'seller_registration.php';

    echo "<script>
            alert('Please log in as a customer first to register as a seller.');
            window.location.href = 'index.html#loginModal';
          </script>";
    exit();
}

// Check if customer is already registered as a seller
$check_seller_sql = "SELECT seller_id FROM sellers WHERE customer_id = ?";
$check_seller_stmt = $conn->prepare($check_seller_sql);
$check_seller_stmt->bind_param("s", $customer_id);
$check_seller_stmt->execute();
$check_seller_result = $check_seller_stmt->get_result();

if ($check_seller_result->num_rows > 0) {
    // User is already a seller, redirect to seller dashboard
    echo "<script>
            window.location.href = 'DASHseller.php';
          </script>";
    exit();
}

// Continue with seller registration page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Seller | Heritage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/seller-registration.css">
    <link rel="stylesheet" href="Nevbar/nevbar2.css">
</head>

<body>
    <?php include 'Nevbar/nevbar2.php'; ?>

    <div class="seller-registration-container">
        <div class="registration-header">
            <h1><i class="fas fa-store"></i> Become a Seller</h1>
            <p>Join our marketplace and start selling your traditional Sri Lankan products</p>
        </div>

        <div class="form-container">
            <form id="sellerRegistrationForm" action="Backend/seller/Regseller.php" method="POST"
                enctype="multipart/form-data">
                <!-- Shop Information Section -->
                <div class="form-section">
                    <h2><i class="fas fa-shopping-bag"></i> Shop Information</h2>

                    <div class="form-group">
                        <label for="shop_name" class="required-field">Shop Name</label>
                        <input type="text" id="shop_name" name="shop_name" required>
                        <div class="hint">This name will be displayed to customers</div>
                    </div>

                    <div class="form-group">
                        <label for="main_category" class="required-field">Main Product Category</label>
                        <select id="main_category" name="main_category" required>
                            <option value="">Select a category</option>
                            <optgroup label="Crafting Products">
                                <option value="Traditional Masks">Traditional Masks</option>
                                <option value="Batik Products">Batik Products</option>
                                <option value="Brass Items">Brass Items</option>
                                <option value="Cane Products">Cane Products</option>
                                <option value="Traditional Jewelry">Traditional Jewelry</option>
                            </optgroup>
                            <optgroup label="Natural Products">
                                <option value="Ceylon Tea">Ceylon Tea</option>
                                <option value="Spices">Spices</option>
                                <option value="Ceylon Cinnamon">Ceylon Cinnamon</option>
                                <option value="Coconut Products">Coconut Products</option>
                                <option value="Kithul Products">Kithul Products</option>
                            </optgroup>
                            <optgroup label="Premium Items">
                                <option value="Ceylon Gems">Ceylon Gems</option>
                                <option value="Silver Crafts">Silver Crafts</option>
                                <option value="Leather Products">Leather Products</option>
                                <option value="Traditional Pottery">Traditional Pottery</option>
                                <option value="Other">Other products</option>
                            </optgroup>
                        </select>
                    </div>

                </div>

                <!-- Address Information Section -->
                <div class="form-section">
                    <h2><i class="fas fa-map-marker-alt"></i> Shop Address</h2>

                    <div class="form-group">
                        <label for="street_address" class="required-field">Street Address</label>
                        <input type="text" id="street_address" name="street_address" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city" class="required-field">City</label>
                            <input type="text" id="city" name="city" required>
                        </div>

                        <div class="form-group">
                            <label for="district" class="required-field">District</label>
                            <select id="district" name="district" required>
                                <option value="">Select District</option>
                                <option value="Ampara">Ampara</option>
                                <option value="Anuradhapura">Anuradhapura</option>
                                <option value="Badulla">Badulla</option>
                                <option value="Batticaloa">Batticaloa</option>
                                <option value="Colombo">Colombo</option>
                                <option value="Galle">Galle</option>
                                <option value="Gampaha">Gampaha</option>
                                <option value="Hambantota">Hambantota</option>
                                <option value="Jaffna">Jaffna</option>
                                <option value="Kalutara">Kalutara</option>
                                <option value="Kandy">Kandy</option>
                                <option value="Kegalle">Kegalle</option>
                                <option value="Kilinochchi">Kilinochchi</option>
                                <option value="Kurunegala">Kurunegala</option>
                                <option value="Mannar">Mannar</option>
                                <option value="Matale">Matale</option>
                                <option value="Matara">Matara</option>
                                <option value="Monaragala">Monaragala</option>
                                <option value="Mullaitivu">Mullaitivu</option>
                                <option value="Nuwara Eliya">Nuwara Eliya</option>
                                <option value="Polonnaruwa">Polonnaruwa</option>
                                <option value="Puttalam">Puttalam</option>
                                <option value="Ratnapura">Ratnapura</option>
                                <option value="Trincomalee">Trincomalee</option>
                                <option value="Vavuniya">Vavuniya</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="province" class="required-field">Province</label>
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
                </div>

                <!-- Business Information Section -->
                <div class="form-section">
                    <h2><i class="fas fa-briefcase"></i> Business Information</h2>

                    <div class="business-toggle">
                        <label>
                            <input type="checkbox" id="has_business" name="has_business">
                            I have a registered business
                        </label>
                    </div>

                    <div id="businessDetails" class="business-details">
                        <div class="form-group">
                            <label for="business_name">Business Name</label>
                            <input type="text" id="business_name" name="business_name">
                        </div>

                        <div class="form-group">
                            <label for="business_reg_no">Business Registration Number</label>
                            <input type="text" id="business_reg_no" name="business_reg_no">
                        </div>

                        <div class="form-group">
                            <label for="business_description">Business Description</label>
                            <textarea id="business_description" name="business_description"
                                placeholder="Tell us about your business, products, and experience..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="business_doc">Business Registration Document (PDF/Image)</label>
                            <div class="file-upload">
                                <button type="button" class="file-upload-btn">Choose File</button>
                                <input type="file" id="business_doc" name="business_doc" accept=".pdf,.jpg,.jpeg,.png">
                                <span class="file-name" id="businessDocName">No file chosen</span>
                            </div>
                            <div class="hint">Upload a copy of your business registration certificate</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Register as Seller</button>
            </form>
        </div>
    </div>

    <?php include 'footer/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle business details section
            const hasBusinessCheckbox = document.getElementById('has_business');
            const businessDetailsSection = document.getElementById('businessDetails');

            hasBusinessCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    businessDetailsSection.style.display = 'block';
                } else {
                    businessDetailsSection.style.display = 'none';
                    // Clear business fields
                    document.getElementById('business_name').value = '';
                    document.getElementById('business_reg_no').value = '';
                    document.getElementById('business_description').value = '';
                    document.getElementById('business_doc').value = '';
                    document.getElementById('businessDocName').textContent = 'No file chosen';
                }
            });

            // Display file name when selected
            const businessDocInput = document.getElementById('business_doc');
            const businessDocName = document.getElementById('businessDocName');

            businessDocInput.addEventListener('change', function () {
                if (this.files.length > 0) {
                    businessDocName.textContent = this.files[0].name;
                } else {
                    businessDocName.textContent = 'No file chosen';
                }
            });
            // Make the Choose File button work
            const fileUploadBtn = document.querySelector('.file-upload-btn');
            fileUploadBtn.addEventListener('click', function () {
                businessDocInput.click(); // This triggers the file input click
            });
            // Form validation
            const sellerRegistrationForm = document.getElementById('sellerRegistrationForm');

            sellerRegistrationForm.addEventListener('submit', function (event) {
                // Validate required fields
                const requiredFields = [
                    { id: 'shop_name', name: 'Shop Name' },
                    { id: 'main_category', name: 'Main Category' },
                    { id: 'street_address', name: 'Street Address' },
                    { id: 'city', name: 'City' },
                    { id: 'district', name: 'District' },
                    { id: 'province', name: 'Province' }
                ];

                let isValid = true;

                // Remove any existing error messages
                const errorMessages = document.querySelectorAll('.error-message');
                errorMessages.forEach(msg => msg.remove());

                // Check required fields
                requiredFields.forEach(field => {
                    const input = document.getElementById(field.id);
                    if (!input.value.trim()) {
                        isValid = false;
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = `${field.name} is required`;
                        input.parentNode.appendChild(errorMsg);
                    }
                });

                // Check business details if checkbox is checked
                if (hasBusinessCheckbox.checked) {
                    const businessFields = [
                        { id: 'business_name', name: 'Business Name' },
                        { id: 'business_reg_no', name: 'Business Registration Number' }
                    ];

                    businessFields.forEach(field => {
                        const input = document.getElementById(field.id);
                        if (!input.value.trim()) {
                            isValid = false;
                            const errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            errorMsg.textContent = `${field.name} is required`;
                            input.parentNode.appendChild(errorMsg);
                        }
                    });

                    // Check if business document is uploaded
                    if (businessDocInput.files.length === 0) {
                        isValid = false;
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Business Registration Document is required';
                        businessDocInput.parentNode.parentNode.appendChild(errorMsg);
                    }
                }

                if (!isValid) {
                    event.preventDefault();
                    // Scroll to the first error
                    const firstError = document.querySelector('.error-message');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    </script>
</body>

</html>