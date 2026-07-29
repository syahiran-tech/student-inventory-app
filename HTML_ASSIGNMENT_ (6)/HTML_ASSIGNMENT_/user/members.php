<?php
require '../_base.php';
auth('Admin', 'Owner');


// Search
$search = trim($_GET['search'] ?? '');


$params = [];
$where = '';

if ($search) {
    $where = "WHERE name LIKE ? OR email LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$allowed = ['name', 'email', 'address', 'status', 'created_at'];
$sort = $_GET['sort'] ?? 'name';
$dir  = $_GET['dir'] ?? 'asc';

if (!in_array($sort, $allowed)) $sort = 'name';
if (!in_array($dir, ['asc','desc'])) $dir = 'asc';

$order = "$sort $dir";

$sql = "
SELECT *,
CASE
    WHEN blocked_until IS NOT NULL AND blocked_until > NOW() THEN 2
    WHEN is_verified = 1 THEN 1
    ELSE 0
END AS status
FROM user
$where
ORDER BY $order
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();


require '../_head.php';
?>


<div class="page-header">
    <h2>👥 Member Management</h2>
    <span style="color:#888; font-size:14px;">
        Total: <?= count($members) ?> member(s)
    </span>
</div>


<!-- Search Bar -->
<div class="card" style="padding:16px 24px; margin-bottom:16px;">
    <div style="display:flex; gap:10px;">
        <input type="text" id="searchInput"
               value="<?= htmlspecialchars($search) ?>"
               placeholder="🔍 Search by name or email..."
               style="flex:1; padding:10px 14px; border:1.5px solid #ddd;
                      border-radius:6px; font-size:14px;">
        <button class="btn btn-primary" id="searchBtn">Search</button>
        <?php if ($search): ?>
            <a href="members.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </div>
</div>


<!-- Members Table -->
<div class="card">
    <?php if ($members): ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>
                <a href="?search=<?= urlencode($search) ?>&sort=name&dir=<?= ($sort=='name' && $dir=='asc') ? 'desc' : 'asc' ?>"style="color:white; text-decoration:none;">
                    Name <?= $sort=='name' ? ($dir=='asc'?'▲':'▼') : '' ?>
                </a>
                </th>

                <th>
                <a href="?search=<?= urlencode($search) ?>&sort=email&dir=<?= ($sort=='email' && $dir=='asc') ? 'desc' : 'asc' ?>"style="color:white; text-decoration:none;">
                    Email <?= $sort=='email' ? ($dir=='asc'?'▲':'▼') : '' ?>
                </a>
                </th>

                <th>
                <a href="?search=<?= urlencode($search) ?>&sort=address&dir=<?= ($sort=='address' && $dir=='asc') ? 'desc' : 'asc' ?>"style="color:white; text-decoration:none;">
                    Address <?= $sort=='address' ? ($dir=='asc'?'▲':'▼') : '' ?>
                </a>
                </th>

                <th>
                <a href="?search=<?= urlencode($search) ?>&sort=status&dir=<?= ($sort=='status' && $dir=='asc') ? 'desc' : 'asc' ?>"style="color:white; text-decoration:none;">
                    Status <?= $sort=='status' ? ($dir=='asc'?'▲':'▼') : '' ?>
                </a>
                </th>

                <th>
                <a href="?search=<?= urlencode($search) ?>&sort=created_at&dir=<?= ($sort=='created_at' && $dir=='asc') ? 'desc' : 'asc' ?>"style="color:white; text-decoration:none;">
                    Registered <?= $sort=='created_at' ? ($dir=='asc'?'▲':'▼') : '' ?>
                </a>
                </th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $i => $m): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <strong><?= htmlspecialchars($m->name) ?></strong>
                </td>
                <td><?= htmlspecialchars($m->email) ?></td>
                <td><?= htmlspecialchars($m->address ?? '-') ?></td>
                <td>
                    <?php if ($m->blocked_until &&
                              new DateTime() <
                              new DateTime($m->blocked_until)): ?>
                        <span class="badge badge-danger">🔒 Blocked</span>
                    <?php elseif ($m->is_verified): ?>
                        <span class="badge badge-success">✅ Verified</span>
                    <?php else: ?>
                        <span class="badge"
                              style="background:#fff3cd; color:#856404;">
                            ⏳ Unverified
                        </span>
                    <?php endif; ?>
                </td>
                <td><?= date('d M Y', strtotime($m->created_at)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align:center; padding:40px; color:#999;">
        <div style="font-size:40px; margin-bottom:12px;">👥</div>
        <p>
            <?= $search
                ? 'No members found for "' . htmlspecialchars($search) . '"'
                : 'No members registered yet.' ?>
        </p>
    </div>
    <?php endif; ?>
</div>


<!-- Search Form (hidden, submitted by jQuery) -->
<form id="searchForm" method="GET" action="">
    <input type="hidden" id="f_search" name="search">
</form>


<script>
// Add jQuery search to app.js won't work here since
// it's a different button ID — so we add it inline
// BUT using jQuery properly (not onclick)
$(document).ready(function() {
    $('#searchBtn').on('click', function() {
        const search = $('#searchInput').val().trim();
        $('#f_search').val(search);
        $('#searchForm').submit();
    });


    // Enter key support
    $('#searchInput').on('keypress', function(e) {
        if (e.key === 'Enter') {
            $('#searchBtn').trigger('click');
        }
    });
});
</script>


</div></body></html>



