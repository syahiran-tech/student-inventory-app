<?php
require '_base.php';
require '_head.php';




// Get all categories for the tabs
$categories = $pdo->query('SELECT * FROM category')->fetchAll();




// Get selected category and search
$category_id = $_GET['category_id'] ?? null;
$search      = trim($_GET['search'] ?? '');




// Build query based on filters
$params = [];
$sql = 'SELECT m.*, c.name AS category_name
        FROM menu_item m
        JOIN category c ON m.category_id = c.id
        WHERE 1';




if ($category_id) {
    $sql .= ' AND m.category_id = ?';
    $params[] = $category_id;
}




if ($search) {
    $sql .= ' AND m.name LIKE ?';
    $params[] = "%$search%";
}




$sql .= ' ORDER BY m.category_id, m.name';




$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>




<div class="page-header">
    <h2>🍽️ Our Menu</h2>
</div>




<!-- Search Bar -->
<form method="GET" action="">
    <?php if ($category_id): ?>
        <input type="hidden" name="category_id"
               value="<?= htmlspecialchars($category_id) ?>">
    <?php endif; ?>
    <div class="search-bar">
        <input type="text" name="search"
               placeholder="🔍 Search for food..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search || $category_id): ?>
            <a href="menu.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </div>
</form>




<!-- Category Tabs -->
<br><div class="cat-tabs">
    <a href="menu.php"
       class="cat-tab btn <?= !$category_id ? 'active' : '' ?>">
        All
    </a>
    <?php foreach ($categories as $cat): ?>
        <a href="menu.php?category_id=<?= $cat->id ?>"
           class="cat-tab btn <?= $category_id == $cat->id ? 'active' : '' ?>">
            <?= htmlspecialchars($cat->name) ?>
        </a>
    <?php endforeach; ?>
</div>




<!-- Menu Items Grid -->
<?php if ($items): ?>
    <br><div class="menu-grid">
        <?php foreach ($items as $item): ?>
            <div class="menu-card <?= !$item->is_available ? 'unavailable' : '' ?>">
                <div style="background:#c0392b; height:300px;
                            display:flex; align-items:center;
                            justify-content:center; font-size:48px;">
                    <img src="/uploads/<?= htmlspecialchars($item->photo) ?>" style="max-width:100%; max-height:100%;">
           
                </div>
                <div class="menu-card-body">
                    <div style="margin-bottom:6px;">
                        <span class="badge badge-success"
                              style="font-size:10px;">
                            <?= htmlspecialchars($item->category_name) ?>
                        </span>
                        <?php if (!$item->is_available): ?>
                            <span class="badge badge-danger"
                                  style="font-size:10px;">
                                Unavailable
                            </span>
                        <?php endif; ?>
                    </div>
                    <h4><?= htmlspecialchars($item->name) ?></h4>
                    <p><?= htmlspecialchars($item->description) ?></p>
                    <div style="display:flex; justify-content:space-between;
                                align-items:center; margin-top:10px;">
                        <span class="price">
                            RM <?= number_format($item->price, 2) ?>
                        </span>
                        <a href="/menu.php?category_id=<?= $item->id ?>"class="btn btn-danger"
                style="width:30%; display:block; margin:8px auto;">Detail</a>
                    </div><br>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card" style="text-align:center; padding:60px;">
        <div style="font-size:48px; margin-bottom:16px;">🔍</div>
        <h3 style="color:#999;">No items found</h3>
        <p style="color:#bbb; margin-top:8px;">
            Try a different search or category
        </p>
    </div>
<?php endif; ?>




<div class="footer">© 2026 SitNEat — All rights reserved.</div>




</div></body></html>






