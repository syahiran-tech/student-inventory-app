<?php
require '../_base.php';
auth('Admin', 'Owner');

$categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll();
?>
<?php include '../_head.php'; ?>

<div class="page-container">
    <div class="page-header">
        <h1>🗂️ Categories</h1>
        <a href="/category/insert.php" class="btn-add">＋ Add Category</a>
    </div>

    <?php if (!$categories): ?>
        <div class="empty-state">
            <span>📭</span>
            <p>No categories yet. <a href="/category/insert.php">Add one now</a>.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th style="width:70px">Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th style="width:160px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td>
                            <?php
                            $img_src = ($cat->photo && file_exists('../uploads/' . $cat->photo))
                                ? '/uploads/' . htmlspecialchars($cat->photo)
                                : 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="52" height="52"><rect width="52" height="52" fill="%23f3f4f6"/>
                                <text x="26" y="32" font-size="20" text-anchor="middle">🍽️</text></svg>';
                            ?>
                            <img src="<?= $img_src ?>"
                                 alt="<?= htmlspecialchars($cat->name) ?>"
                                 class="thumb">
                        </td>
                        <td style="font-weight:600; color:#1a1a1a;"><?= htmlspecialchars($cat->name) ?></td>
                        <td style="color:#6b7280; font-size:.88rem; max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <?= htmlspecialchars($cat->description ?? '—') ?>
                        </td>
                        <td>
                            <a href="/category/update.php?id=<?= $cat->id ?>" class="btn-edit">✏️ Edit</a>
                            <form method="post" action="/category/delete.php" style="display:inline;"
                                  onsubmit="return confirm('Delete this category?')">
                                <input type="hidden" name="id" value="<?= $cat->id ?>">
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