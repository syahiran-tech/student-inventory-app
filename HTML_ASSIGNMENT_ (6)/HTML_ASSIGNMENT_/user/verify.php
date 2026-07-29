<?php
require '../_base.php';


$id = trim($_GET['id'] ?? '');


if (!$id) redirect('/login.php');


// Delete expired tokens first
$pdo->query('DELETE FROM token WHERE expire < NOW()');


// Find the token
$stmt = $pdo->prepare('SELECT * FROM token WHERE id = ? AND type = "verify"');
$stmt->execute([$id]);
$token = $stmt->fetch();


if (!$token) {
    set_flash('Invalid or expired verification link.', 'danger');
    redirect('/login.php');
}


// Mark user as verified
$pdo->prepare('UPDATE user SET is_verified = 1 WHERE id = ?')
    ->execute([$token->user_id]);


// Delete the token
$pdo->prepare('DELETE FROM token WHERE id = ?')
    ->execute([$id]);


set_flash('Email verified! You can now login.', 'success');
redirect('/login.php');
?>



