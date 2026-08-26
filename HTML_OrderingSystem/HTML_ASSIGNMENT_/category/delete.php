<?php
require '../_base.php';
auth('Admin', 'Owner');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}


$id = $_POST['id'] ?? null;
if (!$id) redirect('index.php');


$stmt = $pdo->prepare('SELECT * FROM category WHERE id = ?');
$stmt->execute([$id]);
$cat = $stmt->fetch();


if (!$cat) {
    set_flash('Category not found.', 'danger');
    redirect('index.php');
}


$pdo->prepare('DELETE FROM category WHERE id = ?')->execute([$id]);
set_flash('Category "' . $cat->name . '" deleted successfully!', 'success');
redirect('index.php');
?>



