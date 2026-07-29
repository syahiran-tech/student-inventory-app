<?php
require '../_base.php';
auth('Admin', 'Owner');

$sort   = $_GET['sort'] ?? 'price';
$dir    = $_GET['dir']  ?? 'asc';
$search = $_GET['search'] ?? '';

$allowed_sorts = ['price', 'category', 'is_available', 'name'];
if (!in_array($sort, $allowed_sorts)) $sort = 'price';
if (!in_array($dir, ['asc', 'desc'])) $dir = 'asc';

$order = match($sort) {
    'category'     => "c.name $dir",
    'is_available' => "m.is_available $dir",
    default        => "m.$sort $dir",
};

$params = [];
$sql = "SELECT m.*, c.name AS category_name
        FROM menu_item m
        JOIN category c ON m.category_id = c.id
        WHERE 1";

if ($search !== '') {
    $sql .= " AND (m.name LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

function sort_link($label, $col, $sort, $dir, $search) {
    $new_dir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
    $arrow   = $sort === $col ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
    return "<a href=\"?search=" . urlencode($search) . "&sort=$col&dir=$new_dir\"
               style=\"color:white; text-decoration:none;\">$label$arrow</a>";
}
?>
<?php include '../_head.php'; ?>

<div class="page-container-lg">
    <div class="page-header">
        <h1>🍔 Menu Items</h1>
        <a href="/product/insert.php" class="btn-add">＋ Add Item</a>
    </div>

    <form method="GET" class="search-bar" style="margin-bottom:8px;">
        <input type="text" name="search"
               value="<?= htmlspecialchars($search) ?>"
               placeholder="🔍 Search menu...">
        <input type="hidden" name="sort" value="<?= $sort ?>">
        <input type="hidden" name="dir"  value="<?= $dir ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search): ?>
            <a href="/product/" class="btn-clear">Clear</a>
        <?php endif; ?>
    </form>
    <p style="color:#888; font-size:14px; margin-bottom:16px;">Total: <?= count($items) ?> product(s)</p>

    <?php if (!$items): ?>
        <div class="empty-state">
            <span>📭</span>
            <p>No menu items yet. <a href="/product/insert.php">Add one now</a>.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th style="width:70px">Photo</th>
                        <th><?= sort_link('Name', 'name', $sort, $dir, $search) ?></th>
                        <th><?= sort_link('Category', 'category', $sort, $dir, $search) ?></th>
                        <th><?= sort_link('Price', 'price', $sort, $dir, $search) ?></th>
                        <th><?= sort_link('Available', 'is_available', $sort, $dir, $search) ?></th>
                        <th style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php
                            $src = ($item->photo && file_exists('../uploads/' . $item->photo))
                                ? '/uploads/' . htmlspecialchars($item->photo)
                                : 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="52" height="52"><rect width="52" height="52" fill="#f3f4f6"/><text x="26" y="34" font-size="24" text-anchor="middle">🍔</text></svg>');
                            ?>
                            <img src="<?= $src ?>" class="thumb"
                                 alt="<?= htmlspecialchars($item->name) ?>">
                        </td>
                        <td style="font-weight:600; color:#1a1a1a;"><?= htmlspecialchars($item->name) ?></td>
                        <td style="color:#6b7280; font-size:.88rem;"><?= htmlspecialchars($item->category_name) ?></td>
                        <td style="font-weight:600; color:#c0392b;">RM <?= number_format($item->price, 2) ?></td>
                        <td>
                            <span class="badge <?= $item->is_available ? 'badge-on' : 'badge-off' ?>">
                                <?= $item->is_available ? 'Yes' : 'No' ?>
                            </span>
                        </td>
                        <td>
                            <a href="/product/update.php?id=<?= $item->id ?>" class="btn-edit">✏️ Edit</a>
                            <form method="post" action="/product/delete.php" style="display:inline;"
                                  onsubmit="return confirm('Delete <?= htmlspecialchars($item->name, ENT_QUOTES) ?>?')">
                                <input type="hidden" name="id" value="<?= $item->id ?>">
                                <button type="submit" class="btn-delete">🗑️ Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../_foot.php'; ?>