<?php
require '../_base.php';
auth('Admin','Owner');




$user = $pdo->query('SELECT * FROM user WHERE role="Admin" OR role="Owner" ORDER BY created_at ASC')->fetchAll();




require '../_head.php';
?>




<div class="page-header">
    <h2>👑 Admin Management</h2>
    <a href="insert.php" class="btn btn-primary">+ Add Admin</a>
</div>




<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($user as $i => $users): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <strong><?= htmlspecialchars($users->name) ?></strong>
                    <?php if ($users->id === $_user->id): ?>
                        <span class="badge badge-success"
                              style="margin-left:6px;">(You)</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($users->role) ?></td>
                <td><?= htmlspecialchars($users->email) ?></td>
                <td><?= date('d M Y', strtotime($users->created_at)) ?></td>
                <td>
                    <?php if ($users->id !== $_user->id && count($user) > 1 && $users->role !== 'Owner'): ?>
                        <button class="btn btn-danger btn-delete"
                                data-id="<?= $users->id ?>"
                                data-name="<?= htmlspecialchars($users->name) ?>"
                                data-msg="Delete admin '<?= htmlspecialchars($users->name) ?>'? This cannot be undone!"
                                style="padding:6px 14px; font-size:12px;">
                            🗑️ Delete
                        </button>
                    <?php else: ?>
                        <span style="color:#bbb; font-size:12px;">
                                <?= $users->id === $_user->id
                                    ? 'Cannot delete yourself'
                                : ($users->role === 'Owner' ? 'Owner — protected' : 'Last admin — protected') ?>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>




<div class="card" style="background:#fff8f8;
                          border-left:4px solid #c0392b;">
    <p style="font-size:13px; color:#666; margin:0;">
        ⚠️ <strong>Note:</strong>
        You cannot delete yourself or the last remaining admin
        to ensure the system always has at least one admin.
    </p>
</div>




<form id="deleteForm" method="POST" action="delete.php">
    <input type="hidden" id="delete_id" name="id">
</form>




<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="/js/app.js"></script>




</div></body></html>







