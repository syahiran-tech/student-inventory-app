<?php
require '../_base.php';
auth('Admin','Owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('detail.php');
}

$id = $_POST['id'] ?? null;
if (!$id) redirect('detail.php');

// Cannot delete Owner
$target = $pdo->prepare('SELECT * FROM user WHERE id = ?');
$target->execute([$id]);
$target_user = $target->fetch();
if ($target_user && $target_user->role === 'Owner') {
    set_flash('Cannot delete the Owner account!', 'danger');
    redirect('detail.php');
}

// Cannot delete yourself
if ($id == $_user->id) {
    set_flash('You cannot delete your own account!', 'danger');
    redirect('detail.php');
}

// Count total admins — must keep at least 1
$count = $pdo->query('SELECT COUNT(*) FROM user WHERE role = "Admin"')
             ->fetchColumn();

if ($count <= 1) {
    set_flash('Cannot delete the last admin account!', 'danger');
    redirect('detail.php');
}

// Get admin info
$stmt = $pdo->prepare('SELECT * FROM user WHERE id = ? AND role = "Admin"');
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    set_flash('Admin not found.', 'danger');
    redirect('detail.php');
}

// Delete admin
$pdo->prepare('DELETE FROM user WHERE id = ?')->execute([$id]);
set_flash('Admin "' . $admin->name . '" deleted successfully!', 'success');
redirect('detail.php');
?>