<?php
require '../_base.php';
auth('Admin', 'Owner');
require '../_head.php';

$user = $_SESSION['user'];

if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$delete_id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$delete_id]);
    header("Location: order_detail.php"); exit;
}

if (isset($_POST['delete_all'])) {
    $pdo->prepare("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders)")->execute();
    $pdo->prepare("DELETE FROM orders")->execute();
    header("Location: order_detail.php"); exit;
}

$search = trim($_GET['search'] ?? '');
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
}
$orders = $stmt->fetchAll();
?>

<div class="history-wrap">
    <div class="history-header">
        <div>
            <h2>📜 All Orders</h2>
            <p class="history-sub">Admin view — all customer orders</p>
        </div>
        <?php if ($orders || $search): ?>
            <form method="POST" onsubmit="return confirm('Delete ALL orders? This cannot be undone!')">
                <button type="submit" name="delete_all" class="btn-delete-all">🗑️ Delete All Orders</button>
            </form>
        <?php endif; ?>
    </div>

    <form method="GET" action="" class="search-form">
        <input type="text" name="search" placeholder="🔍 Search by name or email..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <?php if ($search): ?>
            <a href="/admin/order_detail.php" class="btn-clear">✕ Clear</a>
        <?php endif; ?>
    </form>

    <?php if (!$orders): ?>
        <div class="empty-history">
            <div class="empty-icon">📦</div>
            <h3><?= $search ? 'No orders found for "' . htmlspecialchars($search) . '"' : 'No Orders Yet' ?></h3>
            <p><?= $search ? 'Try a different name or email.' : 'No customers have placed any orders yet.' ?></p>
        </div>
    <?php else: ?>
        <div class="order-list">
        <?php foreach ($orders as $order):
            $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt->execute([$order->id]);
            $items = $stmt->fetchAll();
        ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div class="order-badge">Order #<?= $order->id ?></div>
                    <div class="order-date">🕐 <?= date('d M Y, h:i A', strtotime($order->creat_at)) ?></div>
                </div>
                <div class="order-user-info">
                    <span>👤 <?= htmlspecialchars($order->name) ?></span>
                    <span>✉️ <?= htmlspecialchars($order->email) ?></span>
                    <span>🆔 User #<?= $order->user_id ?></span>
                </div>
                <div class="order-divider"></div>
                <div class="order-items">
                    <?php foreach ($items as $item): ?>
                        <div class="order-item-row">
                            <span class="order-item-name"><?= htmlspecialchars($item->name) ?></span>
                            <span class="order-item-qty">×<?= $item->qty ?></span>
                            <span class="order-item-price">RM <?= number_format($item->price * $item->qty, 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-card-footer">
                    <div class="order-total">Total: <strong>RM <?= number_format($order->total, 2) ?></strong></div>
                    <form method="POST" onsubmit="return confirm('Delete Order #<?= $order->id ?>?')">
                        <input type="hidden" name="delete_id" value="<?= $order->id ?>">
                        <button type="submit" class="btn-delete-order">🗑️ Delete Order</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../_foot.php'; ?>