<?php
/**
 * includes/header.php
 * Before including, set: $pageTitle, $activePage, $extraCss (optional)
 */
if (!isset($pageTitle))  $pageTitle  = 'Labuyo Fisheries Corp.';
if (!isset($activePage)) $activePage = '';
if (!isset($extraCss))   $extraCss   = '';

$navItems = [
    'home'    => ['label' => 'Home',    'href' => 'index.php'],
    'about'   => ['label' => 'About',   'href' => 'about.php'],
    'order'   => ['label' => 'Order',   'href' => 'order.php'],
    'contact' => ['label' => 'Contact', 'href' => 'contact.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Labuyo Fisheries Corp. — Fresh milkfish (Bangus) from Obando, Bulacan, Philippines.">
    <meta name="theme-color" content="#0b1f3a">
    <meta property="og:title"       content="<?= htmlspecialchars($pageTitle) ?> — Labuyo Fisheries">
    <meta property="og:description" content="Fresh, sustainably farmed milkfish from Obando, Bulacan.">
    <meta property="og:type"        content="website">
    <title><?= htmlspecialchars($pageTitle) ?> — Labuyo Fisheries Corp.</title>
    <link rel="stylesheet" href="assets/style.css">
    <?php if ($extraCss): ?>
    <link rel="stylesheet" href="assets/<?= htmlspecialchars($extraCss) ?>">
    <?php endif; ?>
    <script type="application/ld+json">
    {"@context":"http://schema.org","@type":"Organization",
     "name":"Labuyo Fisheries Corp.","url":"","description":"Fresh milkfish from Obando, Bulacan"}
    </script>
</head>
<body>

<!-- ── Navigation ─────────────────────────────────────── -->
<nav class="navbar<?= $activePage !== 'home' ? ' solid' : '' ?>" id="navbar">
    <div class="nav-inner">

        <a href="index.php" class="nav-logo">
            <img src="images/logo.png" alt="Labuyo Fisheries Logo"
                 onerror="this.style.display='none'">
            <div class="nav-logo-text">
                <span class="nav-logo-name">Labuyo</span>
                <span class="nav-logo-sub">Fisheries Corp.</span>
            </div>
        </a>

        <div class="nav-links">
            <?php foreach ($navItems as $key => $item): ?>
                <a href="<?= $item['href'] ?>"
                   class="nav-link<?= $key === 'order' ? ' nav-cta' : '' ?><?= $activePage === $key ? ' active' : '' ?>">
                    <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- Mobile nav overlay -->
<div class="nav-mobile" id="navMobile">
    <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= $item['href'] ?>"
           class="nav-link<?= $key === 'order' ? ' nav-cta' : '' ?>">
            <?= $item['label'] ?>
        </a>
    <?php endforeach; ?>
</div>

<script>
(function() {
    var navbar    = document.getElementById('navbar');
    var hamburger = document.getElementById('hamburger');
    var navMobile = document.getElementById('navMobile');

    // Scroll effect (home page only)
    if (navbar && !navbar.classList.contains('solid')) {
        window.addEventListener('scroll', function() {
            navbar.classList.toggle('scrolled', window.scrollY > 60);
        });
    }

    // Hamburger toggle
    hamburger && hamburger.addEventListener('click', function() {
        hamburger.classList.toggle('open');
        navMobile.classList.toggle('open');
        document.body.style.overflow = navMobile.classList.contains('open') ? 'hidden' : '';
    });
})();
</script>
