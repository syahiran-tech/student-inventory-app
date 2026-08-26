<?php
require '_base.php';
require '_head.php';
$_category = $pdo->query('SELECT * FROM category')->fetchAll();

function cat_img($photo) {
    if ($photo && $photo !== '' && file_exists(__DIR__ . '/uploads/' . $photo)) {
        return '/uploads/' . htmlspecialchars($photo);
    }
    return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300"><rect width="400" height="300" fill="#c0392b"/><text x="200" y="160" font-size="80" text-anchor="middle">🍽️</text></svg>');
}
?>

<div class="hero-section">
    <div class="hero-content">
        <div class="hero-tag">🇲🇾 Malaysian Flavours</div>
        <h1 class="hero-title">Sit<span>N</span>Eat <span class="hero-emoji">🍔</span></h1>
        <p class="hero-sub">Fresh food, fast service —<br>dine in or take away!</p>
        <div class="hero-actions">
            <a href="/menu.php" class="btn-hero-primary">🍽️ View Our Menu</a>
            <?php if (!$_user): ?>
                <a href="/user/register.php" class="btn-hero-secondary">Join Now</a>
            <?php endif; ?>
        </div>
        <div class="hero-stats">
            <div class="hero-stat"><strong><?= count($_category) ?></strong><span>Categories</span></div>
            <div class="hero-stat-div"></div>
            <?php $menu_count = $pdo->query('SELECT COUNT(*) FROM menu_item')->fetchColumn(); ?>
            <div class="hero-stat"><strong><?= $menu_count ?></strong><span>Menu Items</span></div>
            <div class="hero-stat-div"></div>
            <div class="hero-stat"><strong>Fast</strong><span>Delivery</span></div>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hero-ring hero-ring-1"></div>
        <div class="hero-ring hero-ring-2"></div>
        <div class="hero-burger">🍔</div>
    </div>
</div>

<div class="home-section">
    <div class="section-header">
        <h2>Our Categories</h2>
        <a href="/menu.php" class="section-link">View All →</a>
    </div>
    <div class="home-grid">
        <?php foreach ($_category as $cat): ?>
        <div class="home-card">
            <div class="home-card-img">
                <img src="<?= cat_img($cat->photo) ?>" alt="<?= htmlspecialchars($cat->name) ?>">
                <div class="home-card-overlay"></div>
            </div>
            <div class="home-card-body">
                <h3><?= htmlspecialchars($cat->name) ?></h3>
                <p><?= htmlspecialchars($cat->description) ?></p>
                <a href="/menu.php?category_id=<?= $cat->id ?>" class="btn-card">Browse →</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="site-footer">© 2026 SitNEat 🍔 — All rights reserved.</div>

</div></body></html>