<?php
/**
 * contact.php
 */
require_once __DIR__ . '/includes/db.php';

$pageTitle  = 'Contact Us';
$activePage = 'contact';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$successMsg = false;
$errorMsg   = '';
$formData   = ['name'=>'','email'=>'','phone'=>'','subject'=>'','message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Security check failed. Please refresh and try again.';
    } else {
        $formData = [
            'name'    => trim(strip_tags($_POST['name']    ?? '')),
            'email'   => trim(strip_tags($_POST['email']   ?? '')),
            'phone'   => trim(strip_tags($_POST['phone']   ?? '')),
            'subject' => trim(strip_tags($_POST['subject'] ?? '')),
            'message' => trim(strip_tags($_POST['message'] ?? '')),
        ];
        $errors = [];
        if (empty($formData['name']))                                       $errors[] = 'Name is required.';
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL))         $errors[] = 'A valid email is required.';
        if (empty($formData['message']))                                     $errors[] = 'Message cannot be empty.';

        if ($errors) {
            $errorMsg = implode(' ', $errors);
        } else {
            try {
                $pdo  = getDB();
                $stmt = $pdo->prepare(
                    'INSERT INTO contact_messages (name, email, phone, message)
                     VALUES (:name, :email, :phone, :message)'
                );
                $stmt->execute([
                    ':name'    => $formData['name'],
                    ':email'   => $formData['email'],
                    ':phone'   => $formData['phone'],
                    ':message' => ($formData['subject'] ? '[' . $formData['subject'] . '] ' : '') . $formData['message'],
                ]);
                $successMsg = true;
                $formData   = array_fill_keys(array_keys($formData), '');
            } catch (PDOException $e) {
                error_log('Contact DB: ' . $e->getMessage());
                $errorMsg = 'Could not send your message right now. Please try again later.';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="assets/contact.css">
<link rel="stylesheet" href="style.css">

<div class="page-header">
    <div class="page-header-label">Get in Touch</div>
    <h1>We'd Love to<br><em style="color:var(--teal-light);font-style:italic;">Hear From You</em></h1>
    <p>Questions, bulk orders, or just want to say hello — we're here.</p>
</div>

<section class="contact-section">
    <div class="container">
        <div class="contact-layout">

            <!-- ── Contact Form ── -->
            <div class="contact-form-wrap">
                <h2 class="contact-form-title">Send a Message</h2>
                <p class="contact-form-sub">We'll get back to you within 1 business day.</p>

                <?php if ($successMsg): ?>
                    <div class="alert alert-success" style="margin-top:24px;">
                        <span class="alert-icon">✓</span>
                        <span>
                            <strong>Message sent!</strong> Thank you for reaching out.
                            We'll be in touch soon.
                        </span>
                    </div>
                <?php endif; ?>

                <?php if ($errorMsg): ?>
                    <div class="alert alert-error" style="margin-top:24px;">
                        <span class="alert-icon">⚠</span>
                        <span><?= htmlspecialchars($errorMsg) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php" class="contact-form" style="margin-top:32px;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                Full Name <span class="req">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="form-control"
                                   value="<?= htmlspecialchars($formData['name']) ?>"
                                   placeholder="Juan dela Cruz" required maxlength="120">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">
                                Email Address <span class="req">*</span>
                            </label>
                            <input type="email" id="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($formData['email']) ?>"
                                   placeholder="juan@email.com" required maxlength="180">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                   value="<?= htmlspecialchars($formData['phone']) ?>"
                                   placeholder="+63 9xx xxx xxxx" maxlength="30">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subject">Subject</label>
                            <select id="subject" name="subject" class="form-control">
                                <option value="">— Select a topic —</option>
                                <option value="Order Inquiry" <?= $formData['subject']==='Order Inquiry'?'selected':'' ?>>Order Inquiry</option>
                                <option value="Bulk/Wholesale" <?= $formData['subject']==='Bulk/Wholesale'?'selected':'' ?>>Bulk / Wholesale</option>
                                <option value="Product Question" <?= $formData['subject']==='Product Question'?'selected':'' ?>>Product Question</option>
                                <option value="Delivery" <?= $formData['subject']==='Delivery'?'selected':'' ?>>Delivery</option>
                                <option value="Other" <?= $formData['subject']==='Other'?'selected':'' ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">
                            Message <span class="req">*</span>
                        </label>
                        <textarea id="message" name="message" class="form-control"
                                  placeholder="How can we help you?"
                                  required maxlength="2000"><?= htmlspecialchars($formData['message']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-full">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- ── Info Panel ── -->
            <div class="contact-info">
                <div class="contact-info-card">
                    <div class="contact-info-icon">📍</div>
                    <div class="contact-info-label">Find Us</div>
                    <div class="contact-info-value">
                        Obando, Bulacan, Philippines
                    </div>
                </div>
                <div class="contact-info-card">
                    <div class="contact-info-icon">📞</div>
                    <div class="contact-info-label">Call or Text</div>
                    <div class="contact-info-value">
                        <a href="tel:+639000000000">+63 900 000 0000</a>
                    </div>
                </div>
                <div class="contact-info-card">
                    <div class="contact-info-icon">✉️</div>
                    <div class="contact-info-label">Email</div>
                    <div class="contact-info-value">
                        <a href="mailto:info@labuyofisheries.ph">info@labuyofisheries.ph</a>
                    </div>
                </div>
                <div class="contact-info-card">
                    <div class="contact-info-icon">🕐</div>
                    <div class="contact-info-label">Business Hours</div>
                    <div class="contact-info-value">
                        Mon–Sat: 6:00 AM – 5:00 PM<br>
                        Sunday: 6:00 AM – 12:00 PM
                    </div>
                </div>

                <div class="contact-cta-card">
                    <div class="contact-cta-title">Ready to order?</div>
                    <p>Skip the message and place your order directly.</p>
                    <a href="order.php" class="btn btn-primary btn-full" style="margin-top:16px;">
                        Place an Order
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
