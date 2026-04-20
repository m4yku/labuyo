<?php
/**
 * order.php — Full ordering system
 */
require_once __DIR__ . '/includes/db.php';

$pageTitle  = 'Place an Order';
$activePage = 'order';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$successMsg = '';
$errorMsg   = '';
$orderId    = null;
$formData   = [
    'customer_name' => '',
    'email'         => '',
    'phone'         => '',
    'address'       => '',
    'barangay'      => '',
    'city'          => '',
    'product_id'    => '',
    'quantity_kg'   => '',
    'delivery_date' => '',
    'notes'         => '',
];

// Load products from DB
$products = [];
try {
    $pdo = getDB();
    $products = $pdo->query(
        'SELECT id, name, price_per_kg, description FROM products WHERE active = 1 ORDER BY name'
    )->fetchAll();
} catch (PDOException $e) {
    error_log('Order: load products: ' . $e->getMessage());
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Security check failed. Please refresh and try again.';
    } else {
        $formData = [
            'customer_name' => trim(strip_tags($_POST['customer_name'] ?? '')),
            'email'         => trim(strip_tags($_POST['email']         ?? '')),
            'phone'         => trim(strip_tags($_POST['phone']         ?? '')),
            'address'       => trim(strip_tags($_POST['address']       ?? '')),
            'barangay'      => trim(strip_tags($_POST['barangay']      ?? '')),
            'city'          => trim(strip_tags($_POST['city']          ?? '')),
            'product_id'    => (int) ($_POST['product_id'] ?? 0),
            'quantity_kg'   => (float) str_replace(',', '.', $_POST['quantity_kg'] ?? '0'),
            'delivery_date' => trim(strip_tags($_POST['delivery_date'] ?? '')),
            'notes'         => trim(strip_tags($_POST['notes']         ?? '')),
        ];

        $errors = [];
        if (empty($formData['customer_name']))                               $errors[] = 'Full name is required.';
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL))          $errors[] = 'A valid email address is required.';
        if (empty($formData['phone']))                                        $errors[] = 'Phone number is required.';
        if (empty($formData['address']))                                      $errors[] = 'Street address is required.';
        if ($formData['product_id'] <= 0)                                    $errors[] = 'Please select a product.';
        if ($formData['quantity_kg'] < 1)                                    $errors[] = 'Minimum order is 1 kg.';
        if (!empty($formData['delivery_date'])) {
            $d = DateTime::createFromFormat('Y-m-d', $formData['delivery_date']);
            if (!$d || $d <= new DateTime()) $errors[] = 'Delivery date must be a future date.';
        }

        if ($errors) {
            $errorMsg = implode(' ', $errors);
        } else {
            try {
                $prod = $pdo->prepare('SELECT name, price_per_kg FROM products WHERE id=:id AND active=1');
                $prod->execute([':id' => $formData['product_id']]);
                $product = $prod->fetch();

                if (!$product) {
                    $errorMsg = 'Selected product is no longer available.';
                } else {
                    $totalPrice  = round($product['price_per_kg'] * $formData['quantity_kg'], 2);
                    $fullAddress = implode(', ', array_filter([
                        $formData['address'],
                        $formData['barangay'],
                        $formData['city'],
                    ]));

                    $stmt = $pdo->prepare(
                        'INSERT INTO orders
                            (customer_name, email, phone, address, product_id, quantity_kg, total_price, delivery_date, notes)
                         VALUES
                            (:customer_name, :email, :phone, :address, :product_id, :quantity_kg, :total_price, :delivery_date, :notes)'
                    );
                    $stmt->execute([
                        ':customer_name' => $formData['customer_name'],
                        ':email'         => $formData['email'],
                        ':phone'         => $formData['phone'],
                        ':address'       => $fullAddress,
                        ':product_id'    => $formData['product_id'],
                        ':quantity_kg'   => $formData['quantity_kg'],
                        ':total_price'   => $totalPrice,
                        ':delivery_date' => $formData['delivery_date'] ?: null,
                        ':notes'         => $formData['notes'],
                    ]);

                    $orderId    = (int) $pdo->lastInsertId();
                    $successMsg = [
                        'order_id'    => $orderId,
                        'order_num'   => 'LFC-' . str_pad($orderId, 5, '0', STR_PAD_LEFT),
                        'product'     => $product['name'],
                        'qty'         => $formData['quantity_kg'],
                        'total'       => $totalPrice,
                        'customer'    => $formData['customer_name'],
                    ];
                    $formData = array_fill_keys(array_keys($formData), '');
                }
            } catch (PDOException $e) {
                error_log('Order DB error: ' . $e->getMessage());
                $errorMsg = 'Sorry, we could not place your order. Please try again or call us directly.';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="assets/order.css">
<link rel="stylesheet" href="style.css">

<div class="page-header">
    <div class="page-header-label">Fresh Bangus Delivery</div>
    <h1>Place Your <em style="color:var(--teal-light);font-style:italic;">Order</em></h1>
    <p>Fill in your details below and we'll confirm your order within 24 hours.</p>
</div>

<section class="order-section">
    <div class="container">
        <div class="order-layout">

            <!-- ── Order Form ── -->
            <div class="order-form-wrap">

                <?php if ($successMsg && is_array($successMsg)): ?>
                <!-- Success State -->
                <div class="order-success">
                    <div class="order-success-icon">✓</div>
                    <h2 class="order-success-title">Order Placed!</h2>
                    <p class="order-success-sub">
                        Thank you, <strong><?= htmlspecialchars($successMsg['customer']) ?></strong>!
                        Your order has been received and we'll contact you shortly to confirm.
                    </p>
                    <div class="order-receipt">
                        <div class="order-receipt-row">
                            <span>Order Number</span>
                            <strong><?= htmlspecialchars($successMsg['order_num']) ?></strong>
                        </div>
                        <div class="order-receipt-row">
                            <span>Product</span>
                            <strong><?= htmlspecialchars($successMsg['product']) ?></strong>
                        </div>
                        <div class="order-receipt-row">
                            <span>Quantity</span>
                            <strong><?= $successMsg['qty'] ?> kg</strong>
                        </div>
                        <div class="order-receipt-row total">
                            <span>Total Amount</span>
                            <strong>₱<?= number_format($successMsg['total'], 2) ?></strong>
                        </div>
                    </div>
                    <a href="order.php" class="btn btn-primary btn-full" style="margin-top:24px;">
                        Place Another Order
                    </a>
                </div>

                <?php else: ?>

                <?php if ($errorMsg): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">⚠</span>
                        <span><?= htmlspecialchars($errorMsg) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="order.php" class="order-form" id="orderForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <!-- Step 1: Customer Info -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <div class="form-section-num">1</div>
                            <div>
                                <div class="form-section-title">Your Information</div>
                                <div class="form-section-sub">Tell us who you are</div>
                            </div>
                        </div>
                        <div class="form-fields">
                            <div class="form-group">
                                <label class="form-label" for="customer_name">
                                    Full Name <span class="req">*</span>
                                </label>
                                <input type="text" id="customer_name" name="customer_name"
                                       class="form-control"
                                       value="<?= htmlspecialchars($formData['customer_name']) ?>"
                                       placeholder="Juan dela Cruz"
                                       required maxlength="120">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="email">
                                        Email Address <span class="req">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                           class="form-control"
                                           value="<?= htmlspecialchars($formData['email']) ?>"
                                           placeholder="juan@email.com"
                                           required maxlength="180">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="phone">
                                        Phone Number <span class="req">*</span>
                                    </label>
                                    <input type="tel" id="phone" name="phone"
                                           class="form-control"
                                           value="<?= htmlspecialchars($formData['phone']) ?>"
                                           placeholder="+63 9xx xxx xxxx"
                                           required maxlength="30">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Delivery Address -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <div class="form-section-num">2</div>
                            <div>
                                <div class="form-section-title">Delivery Address</div>
                                <div class="form-section-sub">Where should we deliver?</div>
                            </div>
                        </div>
                        <div class="form-fields">
                            <div class="form-group">
                                <label class="form-label" for="address">
                                    Street / House No. <span class="req">*</span>
                                </label>
                                <input type="text" id="address" name="address"
                                       class="form-control"
                                       value="<?= htmlspecialchars($formData['address']) ?>"
                                       placeholder="123 Rizal St."
                                       required maxlength="200">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="barangay">Barangay</label>
                                    <input type="text" id="barangay" name="barangay"
                                           class="form-control"
                                           value="<?= htmlspecialchars($formData['barangay']) ?>"
                                           placeholder="Brgy. San Pascual"
                                           maxlength="120">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="city">City / Municipality</label>
                                    <input type="text" id="city" name="city"
                                           class="form-control"
                                           value="<?= htmlspecialchars($formData['city']) ?>"
                                           placeholder="Obando, Bulacan"
                                           maxlength="120">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Order Details -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <div class="form-section-num">3</div>
                            <div>
                                <div class="form-section-title">Order Details</div>
                                <div class="form-section-sub">What would you like to order?</div>
                            </div>
                        </div>
                        <div class="form-fields">

                            <!-- Product cards -->
                            <div class="form-group">
                                <label class="form-label">
                                    Select Product <span class="req">*</span>
                                </label>
                                <div class="product-picker" id="productPicker">
                                    <?php if ($products): ?>
                                        <?php foreach ($products as $p): ?>
                                            <label class="product-option <?= (int)$formData['product_id'] === (int)$p['id'] ? 'selected' : '' ?>">
                                                <input type="radio" name="product_id"
                                                       value="<?= (int)$p['id'] ?>"
                                                       data-price="<?= $p['price_per_kg'] ?>"
                                                       <?= (int)$formData['product_id'] === (int)$p['id'] ? 'checked' : '' ?>
                                                       required>
                                                <div class="product-option-inner">
                                                    <div class="product-option-name">
                                                        <?= htmlspecialchars($p['name']) ?>
                                                    </div>
                                                    <div class="product-option-desc">
                                                        <?= htmlspecialchars($p['description'] ?? '') ?>
                                                    </div>
                                                    <div class="product-option-price">
                                                        ₱<?= number_format($p['price_per_kg'], 2) ?>/kg
                                                    </div>
                                                </div>
                                                <div class="product-option-check">✓</div>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p style="color:var(--text-mid);padding:20px;text-align:center;">
                                            No products available at the moment.
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="quantity_kg">
                                        Quantity (kg) <span class="req">*</span>
                                    </label>
                                    <input type="number" id="quantity_kg" name="quantity_kg"
                                           class="form-control"
                                           value="<?= htmlspecialchars($formData['quantity_kg']) ?>"
                                           placeholder="e.g. 10"
                                           min="1" step="0.5" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="delivery_date">
                                        Preferred Delivery Date
                                    </label>
                                    <input type="date" id="delivery_date" name="delivery_date"
                                           class="form-control"
                                           value="<?= htmlspecialchars($formData['delivery_date']) ?>"
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="notes">Special Instructions</label>
                                <textarea id="notes" name="notes"
                                          class="form-control"
                                          placeholder="Any specific requests, preparation notes, or delivery instructions…"
                                          maxlength="1000"><?= htmlspecialchars($formData['notes']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-full" id="submitBtn">
                        <span>Confirm Order</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <p style="text-align:center;font-size:0.8rem;color:var(--text-light);margin-top:12px;">
                        We'll confirm your order within 24 hours via phone or email.
                    </p>
                </form>
                <?php endif; ?>
            </div><!-- /form wrap -->

            <!-- ── Order Sidebar ── -->
            <div class="order-sidebar">
                <!-- Live price summary -->
                <div class="order-summary-card" id="summaryCard">
                    <div class="summary-title">Order Summary</div>
                    <div class="summary-product" id="summaryProduct">
                        <span class="summary-empty">No product selected yet</span>
                    </div>
                    <div class="summary-row" id="summaryQtyRow" style="display:none;">
                        <span>Quantity</span>
                        <span id="summaryQty">—</span>
                    </div>
                    <div class="summary-row" id="summaryPriceRow" style="display:none;">
                        <span>Unit Price</span>
                        <span id="summaryUnitPrice">—</span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-total-row">
                        <span>Estimated Total</span>
                        <strong id="summaryTotal">₱0.00</strong>
                    </div>
                    <p class="summary-note">
                        * Final price confirmed upon order verification.
                        Bulk discounts may apply.
                    </p>
                </div>

                <!-- Info cards -->
                <div class="sidebar-info-card">
                    <div class="sidebar-info-icon">🕐</div>
                    <div>
                        <strong>Order Processing</strong>
                        <p>We confirm all orders within 24 hours via call or SMS.</p>
                    </div>
                </div>
                <div class="sidebar-info-card">
                    <div class="sidebar-info-icon">🚚</div>
                    <div>
                        <strong>Delivery Coverage</strong>
                        <p>We deliver across Bulacan and select areas in Metro Manila.</p>
                    </div>
                </div>
                <div class="sidebar-info-card">
                    <div class="sidebar-info-icon">💬</div>
                    <div>
                        <strong>Questions?</strong>
                        <p>Call us at <a href="tel:+639000000000" style="color:var(--teal);">+63 900 000 0000</a>
                           or <a href="contact.php" style="color:var(--teal);">send a message</a>.</p>
                    </div>
                </div>
            </div><!-- /sidebar -->

        </div><!-- /order-layout -->
    </div>
</section>

<script>
(function () {
    var productRadios = document.querySelectorAll('input[name="product_id"]');
    var qtyInput      = document.getElementById('quantity_kg');
    var summaryProduct   = document.getElementById('summaryProduct');
    var summaryQtyRow    = document.getElementById('summaryQtyRow');
    var summaryPriceRow  = document.getElementById('summaryPriceRow');
    var summaryQty       = document.getElementById('summaryQty');
    var summaryUnitPrice = document.getElementById('summaryUnitPrice');
    var summaryTotal     = document.getElementById('summaryTotal');

    function formatMoney(n) {
        return '₱' + n.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function getSelectedProduct() {
        for (var r of productRadios) {
            if (r.checked) return { name: r.closest('.product-option').querySelector('.product-option-name').textContent.trim(), price: parseFloat(r.dataset.price) };
        }
        return null;
    }

    function updateSummary() {
        var prod = getSelectedProduct();
        var qty  = parseFloat(qtyInput ? qtyInput.value : 0) || 0;

        if (!prod) {
            summaryProduct.innerHTML = '<span class="summary-empty">No product selected yet</span>';
            summaryQtyRow.style.display   = 'none';
            summaryPriceRow.style.display = 'none';
            summaryTotal.textContent = '₱0.00';
            return;
        }

        summaryProduct.innerHTML = '<strong>' + prod.name + '</strong>';
        summaryQtyRow.style.display   = '';
        summaryPriceRow.style.display = '';
        summaryQty.textContent       = qty > 0 ? qty + ' kg' : '—';
        summaryUnitPrice.textContent  = formatMoney(prod.price) + '/kg';
        summaryTotal.textContent      = qty > 0 ? formatMoney(prod.price * qty) : '₱0.00';
    }

    // Product option label highlight
    productRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.product-option').forEach(function(el) { el.classList.remove('selected'); });
            this.closest('.product-option').classList.add('selected');
            updateSummary();
        });
    });

    qtyInput && qtyInput.addEventListener('input', updateSummary);

    updateSummary();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
