<?php
require '../_base.php';
auth();
global $pdo;

$id = $_user->id;
$errors  = [];
$success = '';
$current = $new = $confirm = '';

if (isset($_POST['btn_change'])) {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE id=? AND password=SHA1(?)");
    $stmt->execute([$id, $current]);
    if ($stmt->fetchColumn() == 0)
        $errors['current_password'] = 'Current password is incorrect.';

    if (strlen($new) < 6)
        $errors['new_password'] = 'Must be at least 6 characters.';

    if ($new !== $confirm)
        $errors['confirm_password'] = 'Passwords do not match.';

    if (empty($errors)) {
        $pdo->prepare("UPDATE user SET password=SHA1(?) WHERE id=?")->execute([$new, $id]);
        $success = 'Password changed successfully!';
        $current = $new = $confirm = '';
    }
}

$_title = 'Change Password';
include '../_head.php';
?>

<div class="pw-wrap">
    <div class="pw-header">
        <h1>Change Password</h1>
        <p>Keep your account safe and secure</p>
    </div>

    <?php if ($success): ?>
        <div class="pw-success">✅ <?= $success ?></div>
    <?php endif; ?>

    <div class="pw-tip">🔐 Your password must be at least 6 characters long.</div>

    <form method="post">
        <div class="pw-card">
            <div class="pw-field">
                <label>Current Password</label>
                <input type="password" name="current_password"
                       value="<?= htmlspecialchars($current) ?>"
                       placeholder="Enter your current password"
                       class="<?= !empty($errors['current_password']) ? 'has-error' : '' ?>">
                <?php if (!empty($errors['current_password'])): ?>
                    <span class="err">⚠️ <?= $errors['current_password'] ?></span>
                <?php endif; ?>
            </div>
            <div class="pw-field">
                <label>New Password</label>
                <input type="password" name="new_password"
                       value="<?= htmlspecialchars($new) ?>"
                       placeholder="At least 6 characters"
                       class="<?= !empty($errors['new_password']) ? 'has-error' : '' ?>">
                <?php if (!empty($errors['new_password'])): ?>
                    <span class="err">⚠️ <?= $errors['new_password'] ?></span>
                <?php endif; ?>
            </div>
            <div class="pw-field">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password"
                       value="<?= htmlspecialchars($confirm) ?>"
                       placeholder="Re-enter new password"
                       class="<?= !empty($errors['confirm_password']) ? 'has-error' : '' ?>">
                <?php if (!empty($errors['confirm_password'])): ?>
                    <span class="err">⚠️ <?= $errors['confirm_password'] ?></span>
                <?php endif; ?>
            </div>
        </div>
        <button type="submit" name="btn_change" class="pw-btn">Update Password</button>
    </form>

    <a href="/user/profile.php" class="pw-back">← Back to My Profile</a>
</div>

<?php include '../_foot.php'; ?>