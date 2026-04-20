<?php
$pageTitle  = 'Home';
$activePage = 'home';
include __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="assets/home.css">
<link rel="stylesheet" href="style.css">

<!-- ════════════════════════════════════════════════════════
     HERO
     ════════════════════════════════════════════════════════ -->
<section class="hero" id="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content container">
        <div class="hero-badge animate-fade-up">
            <span class="hero-badge-dot"></span>
            Fresh catch available daily
        </div>

        <h1 class="hero-title animate-fade-up delay-1">
            The Finest <em>Bangus</em><br>
            from Obando, Bulacan
        </h1>

        <p class="hero-sub animate-fade-up delay-2">
            Sustainably farmed milkfish — harvested fresh, delivered to your door.
            Quality you can taste, from a family that cares.
        </p>

        <div class="hero-actions animate-fade-up delay-3">
            <a href="order.php" class="btn btn-primary btn-lg">Order Now</a>
            <a href="about.php" class="btn btn-outline btn-lg">Learn More</a>
        </div>
    </div>

    <div class="hero-stats animate-fade-up delay-4">
        <div class="container">
            <div class="hero-stats-inner">
                <div class="hero-stat">
                    <div class="hero-stat-num">20+</div>
                    <div class="hero-stat-label">Years of Operation</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">500+</div>
                    <div class="hero-stat-label">Happy Clients</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">100%</div>
                    <div class="hero-stat-label">Fresh, Local Fish</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">4</div>
                    <div class="hero-stat-label">Product Varieties</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════════════════
     ABOUT STRIP
     ════════════════════════════════════════════════════════ -->
<section class="about-strip">
    <div class="container">
        <div class="about-strip-grid">
            <div class="about-strip-img">
                <img src="images/ObandoBulacanPhilippines.jpg"
                     alt="Obando Bulacan fishponds"
                     onerror="this.parentElement.classList.add('img-placeholder')">
                <div class="about-strip-img-badge">
                    <span>🐟</span>
                    <span>Obando, Bulacan</span>
                </div>
            </div>
            <div class="about-strip-text">
                <div class="section-label">Our Story</div>
                <h2 class="section-title">A Family Tradition<br>of <em>Fresh</em> Fishing</h2>
                <p class="section-sub">
                    For over two decades, Labuyo Fisheries Corp. has been raising premium
                    milkfish in the clean waters of Obando, Bulacan. We combine traditional
                    aquaculture knowledge with modern best practices — ensuring every fish
                    meets the highest standards before reaching your table.
                </p>
                <div class="about-features">
                    <div class="about-feature">
                        <div class="about-feature-icon">🌊</div>
                        <div>
                            <strong>Sustainable Farming</strong>
                            <p>Environmentally responsible practices that protect our waterways.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon">🧊</div>
                        <div>
                            <strong>Fresh Daily Harvest</strong>
                            <p>Fish are harvested and processed same-day for maximum freshness.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon">🚚</div>
                        <div>
                            <strong>Direct Delivery</strong>
                            <p>We deliver across Central Luzon — no middlemen, better prices.</p>
                        </div>
                    </div>
                </div>
                <a href="about.php" class="btn btn-navy mt-8">Read Our Story</a>
            </div>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════════════════
     PRODUCTS
     ════════════════════════════════════════════════════════ -->
<section class="products-section">
    <div class="container">
        <div class="products-header text-center">
            <div class="section-label">Our Products</div>
            <h2 class="section-title">Premium <em>Bangus</em> Varieties</h2>
            <p class="section-sub" style="margin:0 auto;">
                From whole fresh milkfish to ready-to-cook specialties —
                we have the perfect Bangus for every table.
            </p>
        </div>

        <div class="products-grid">
            <?php
            $products = [
                ['icon'=>'🐟','name'=>'Fresh Bangus','desc'=>'Whole, freshly harvested milkfish. Best for sinigang, paksiw, or grilling.','price'=>'₱180','unit'=>'/kg','tag'=>'Most Popular'],
                ['icon'=>'🔪','name'=>'Deboned Bangus','desc'=>'Expertly cleaned and deboned — ready to cook right out of the pack.','price'=>'₱250','unit'=>'/kg','tag'=>'Bestseller'],
                ['icon'=>'🔥','name'=>'Smoked Bangus','desc'=>'Traditional tinapa-style smoked milkfish. Perfect with garlic fried rice.','price'=>'₱300','unit'=>'/kg','tag'=>''],
                ['icon'=>'🌿','name'=>'Marinated Daing','desc'=>'Overnight-marinated, sun-dried bangus packed in vacuum-sealed bags.','price'=>'₱320','unit'=>'/kg','tag'=>''],
            ];
            foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if ($p['tag']): ?>
                        <div class="product-tag"><?= $p['tag'] ?></div>
                    <?php endif; ?>
                    <div class="product-icon"><?= $p['icon'] ?></div>
                    <h3 class="product-name"><?= $p['name'] ?></h3>
                    <p class="product-desc"><?= $p['desc'] ?></p>
                    <div class="product-footer">
                        <div class="product-price">
                            <?= $p['price'] ?><span><?= $p['unit'] ?></span>
                        </div>
                        <a href="order.php" class="btn btn-primary" style="padding:10px 20px;font-size:0.875rem;">
                            Order
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ════════════════════════════════════════════════════════
     GALLERY
     ════════════════════════════════════════════════════════ -->
<section class="gallery-section">
    <div class="container">
        <div class="section-label text-center">Photo Gallery</div>
        <h2 class="section-title text-center">From Our <em>Fishponds</em></h2>
    </div>
    <div class="gallery-grid">
        <div class="gallery-item tall" style="background-image:url('images/316141351_1311490029612412_4380515428651272655_n.jpg')"></div>
        <div class="gallery-item" style="background-image:url('images/316037506_589880222907196_2945888925033025239_n.jpg')"></div>
        <div class="gallery-item" style="background-image:url('images/fresh-milkfish-local-fish-market-191471895.jpg')"></div>
        <div class="gallery-item wide" style="background-image:url('images/9623Obando_Bulacan_River_Districts_Landmarks_09.jpg')"></div>
    </div>
</section>

<!-- ════════════════════════════════════════════════════════
     CTA BANNER
     ════════════════════════════════════════════════════════ -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <div class="cta-text">
                <h2 class="cta-title">Ready to Order <em>Fresh Bangus?</em></h2>
                <p>Place your order today and we'll confirm within 24 hours.</p>
            </div>
            <div class="cta-actions">
                <a href="order.php" class="btn btn-primary btn-lg">Place an Order</a>
                <a href="contact.php" class="btn btn-outline btn-lg">Ask a Question</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
