<?php
require '../_base.php';
auth('Admin', 'Owner');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /product/');
    exit;
}


$id = $_POST['id'] ?? null;
if (!$id) { header('Location: /product/'); exit; }


$stmt = $pdo->prepare('SELECT * FROM menu_item WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();


if (!$item) {
    header('Location: /product/');
    exit;
}


// Delete photo file if it was uploaded by us (starts with item_)
if ($item->photo && str_starts_with($item->photo, 'item_') && file_exists('../uploads/' . $item->photo)) {
    unlink('../uploads/' . $item->photo);
}

$pdo->prepare('DELETE FROM menu_item WHERE id = ?')->execute([$id]);
header('Location: /product/');
exit;
?>

