<?php
require '_base.php';
require '_head.php';

$categories = $pdo->query('SELECT * FROM category')->fetchAll();
$category_id = $_GET['category_id'] ?? null;
$search      = trim($_GET['search'] ?? '');

if (isset($_POST['add'])) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $id    = $_POST['id'];
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $qty   = (int)($_POST['qty'] ?? 1);
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$id] = ['name' => $name, 'price' => $price, 'qty' => $qty];
    }
    header('Location: menu.php' . ($category_id ? '?category_id=' . $category_id : '') . ($search ? '?search=' . urlencode($search) : ''));
    exit;
}

$params = [];
$sql = 'SELECT m.*, c.name AS category_name
        FROM menu_item m
        JOIN category c ON m.category_id = c.id
        WHERE 1';
if ($category_id) { $sql .= ' AND m.category_id = ?'; $params[] = $category_id; }
if ($search)      { $sql .= ' AND m.name LIKE ?';      $params[] = "%$search%"; }
$sort = $_GET['sort'] ?? 'asc';
if ($sort == 'asc') {
    $sql .= ' ORDER BY m.price ASC';}else {$sql .= ' ORDER BY m.price DESC';}
$nextSort = ($sort == 'asc') ? 'desc' : 'asc';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

function menu_img($photo) {
    if ($photo && $photo !== 'default.png' && file_exists(__DIR__ . '/uploads/' . $photo)) {
        return '/uploads/' . htmlspecialchars($photo);
    }
    return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="260"><rect width="400" height="260" fill="#c0392b"/><text x="200" y="145" font-size="72" text-anchor="middle">🍔</text></svg>');
}
?>

<div class="menu-wrap">
    <div class="menu-top">
        <div>
            <h2 class="menu-title">🍽️ Our Menu</h2>
            <p class="menu-sub">Fresh, delicious food made just for you</p>
        </div>
        <?php if ($_user && in_array($_user->role, ['Owner', 'Admin'])): ?>
            <a href="/product/insert.php" class="btn-admin-add">＋ Add Item</a>
        <?php endif; ?>
    </div>

    <form method="GET" action="" class="menu-search-form">
        <?php if ($category_id): ?>
            <input type="hidden" name="category_id" value="<?= htmlspecialchars($category_id) ?>">
        <?php endif; ?>
        <div class="menu-search-bar">
            <input type="text" name="search" placeholder="🔍 Search for food..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <?php if ($search || $category_id): ?>
                <a href="/menu.php" class="btn-clear">✕ Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="cat-tabs">
        <a href="/menu.php" class="cat-tab <?= !$category_id && !$search ? 'active' : '' ?>">All</a>
        <?php foreach ($categories as $cat): ?>
            <a href="/menu.php?category_id=<?= $cat->id ?>"
               class="cat-tab <?= $category_id == $cat->id ? 'active' : '' ?>">
                <?= htmlspecialchars($cat->name) ?>
            </a>
        <?php endforeach; ?>
        <a href="?sort=<?= $nextSort ?>
        <?= $category_id ? '&category_id='.$category_id : '' ?>
        <?= $search ? '&search='.$search : '' ?>"class="btn btn-warning">Price <?= $sort == 'asc' ? '↑' : '↓' ?></a>
    </div>

    <?php if ($items): ?>
        <div class="menu-grid">
            <?php foreach ($items as $item): ?>
                <div class="menu-card <?= !$item->is_available ? 'menu-card-unavail' : '' ?>">
                    <div class="menu-card-img">
                        <img src="<?= menu_img($item->photo) ?>" alt="<?= htmlspecialchars($item->name) ?>">
                        <?php if (!$item->is_available): ?>
                            <div class="unavail-overlay">Unavailable</div>
                        <?php endif; ?>
                        <span class="cat-badge"><?= htmlspecialchars($item->category_name) ?></span>
                        <?php if ($_user && in_array($_user->role, ['Owner', 'Admin'])): ?>
                            <a href="/product/update.php?id=<?= $item->id ?>" class="edit-badge">✏️ Edit</a>
                        <?php endif; ?>
                    </div>
                    <div class="menu-card-body">
                        <h3 class="menu-item-name"><?= htmlspecialchars($item->name) ?></h3>
                        <p class="menu-item-desc"><?= htmlspecialchars($item->description) ?></p>
                        <div class="menu-card-footer">
                            <span class="menu-price">RM <?= number_format($item->price, 2) ?></span>
                            <?php if ($item->is_available): ?>
                                <?php if ($_user): ?>
                                    <form method="POST" action="/menu.php" class="add-form">
                                        <input type="hidden" name="add" value="1">
                                        <input type="hidden" name="id" value="<?= $item->id ?>">
                                        <input type="hidden" name="name" value="<?= htmlspecialchars($item->name) ?>">
                                        <input type="hidden" name="price" value="<?= $item->price ?>">
                                        <div class="qty-row">
                                            <input type="number" name="qty" value="1" min="1" max="20" class="qty-input">
                                            <button type="submit" class="btn-add-cart">🛒 Add</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <a href="/login.php" class="btn-add-cart">Login to Order</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="btn-unavail">Unavailable</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="menu-empty">
            <div>🔍</div>
            <h3>No items found</h3>
            <p>Try a different search or category</p>
            <a href="/menu.php" class="btn-clear-search">Clear Search</a>
        </div>
    <?php endif; ?>
    <a href="cart.php" class="cart-floating">
    🛒
    <span class="cart-count">
        <?= isset($_SESSION['cart'])
            ? array_sum(array_column($_SESSION['cart'], 'qty'))
            : 0 ?>
    </span>
</a>
</div>

<div class="footer">© 2026 SitNEat 🍔 All rights reserved.</div>

</div></body></html>