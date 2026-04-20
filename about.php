<?php
$pageTitle  = 'About Us';
$activePage = 'about';
include __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="assets/about.css">
<link rel="stylesheet" href="style.css">

<div class="page-header">
    <div class="page-header-label">Who We Are</div>
    <h1>About Labuyo<br>Fisheries Corp.</h1>
    <p>A family legacy of fresh, sustainable milkfish farming in the heart of Bulacan.</p>
</div>

<!-- Story -->
<section class="about-story">
    <div class="container">
        <div class="story-grid">
            <div class="story-text">
                <div class="section-label">Our Story</div>
                <h2 class="section-title">Roots in <em>Obando</em></h2>
                <p>
                    Labuyo Fisheries Corp. was founded by a family deeply rooted in the
                    fishing traditions of Obando, Bulacan — a municipality long known as
                    one of the most productive aquaculture areas in the Philippines.
                </p>
                <p>
                    What began as a small family fishpond has grown into a trusted supplier
                    of premium milkfish (<em>Bangus</em>) to households, restaurants, and
                    markets across Central Luzon. Through every generation, we've held fast
                    to one principle: <strong>freshness first.</strong>
                </p>
                <p>
                    Every fish that leaves our ponds is harvested at peak quality, handled
                    with care, and delivered as quickly as possible — because we believe
                    you deserve nothing less.
                </p>
            </div>
            <div class="story-imgs">
                <div class="story-img-main">
                    <img src="images/316141351_1311490029612412_4380515428651272655_n.jpg"
                         alt="Labuyo Fisheries fishpond"
                         onerror="this.parentElement.style.background='var(--navy-light)'">
                </div>
                <div class="story-img-side">
                    <img src="images/9623Obando_Bulacan_River_Districts_Landmarks_09.jpg"
                         alt="Obando Bulacan"
                         onerror="this.parentElement.style.background='var(--teal-dim)'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="values-section">
    <div class="container">
        <div class="text-center" style="margin-bottom:56px;">
            <div class="section-label">What Drives Us</div>
            <h2 class="section-title">Our Core <em>Values</em></h2>
        </div>
        <div class="values-grid">
            <?php
            $values = [
                ['icon'=>'🌊','title'=>'Sustainability',
                 'desc'=>'We fish responsibly, maintaining ecological balance in our ponds and the surrounding waterways for future generations.'],
                ['icon'=>'🐟','title'=>'Quality',
                 'desc'=>'Every bangus goes through strict quality checks before leaving our facility. No compromises.'],
                ['icon'=>'🤝','title'=>'Community',
                 'desc'=>'We support local fisherfolk, pay fair wages, and invest in the communities where we operate.'],
                ['icon'=>'❤️','title'=>'Family',
                 'desc'=>'We are family-owned and family-run. The same care we give our family, we give to our fish and our customers.'],
            ];
            foreach ($values as $v): ?>
                <div class="value-card">
                    <div class="value-icon"><?= $v['icon'] ?></div>
                    <h3 class="value-title"><?= $v['title'] ?></h3>
                    <p class="value-desc"><?= $v['desc'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Mission -->
<section class="mission-section">
    <div class="container">
        <div class="mission-inner">
            <div class="mission-quote">
                <div class="mission-quote-mark">"</div>
                <blockquote>
                    To nourish Filipino families with the freshest, most
                    responsibly farmed milkfish — delivered with integrity,
                    care, and a deep love for our craft.
                </blockquote>
                <cite>— The Labuyo Family</cite>
            </div>
            <div class="mission-cta">
                <h3>Ready to taste the difference?</h3>
                <p>Order fresh bangus directly from our fishponds.</p>
                <a href="order.php" class="btn btn-primary btn-lg" style="margin-top:24px;">
                    Order Fresh Bangus
                </a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
