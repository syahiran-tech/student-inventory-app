<?php
require '../_base.php';
auth();
global $pdo;

$id = $_user->id;
$stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$id]);
$u = $stmt->fetch();

$name     = $u->name;
$email    = $u->email;
$address  = $u->address  ?? '';
$city     = $u->city     ?? '';
$postcode = $u->postcode ?? '';
$state    = $u->state    ?? '';
$theme    = $u->theme    ?? 'light';
$errors   = [];
$success  = '';

if (isset($_POST['btn_update'])) {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $address  = trim($_POST['address']  ?? '');
    $city     = trim($_POST['city']     ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    $state    = trim($_POST['state']    ?? '');
    $theme    = $_POST['theme']         ?? 'light';

    if ($name === '') $errors['name'] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Invalid email format.';

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetchColumn() > 0)
        $errors['email'] = 'Email already used by someone else.';

    $photo = $u->photo ?? 'default.png';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $file = $_FILES['photo'];
        if (!str_starts_with($file['type'], 'image/'))
            $errors['photo'] = 'Please upload an image file.';
        elseif ($file['size'] > 5 * 1024 * 1024)
            $errors['photo'] = 'Photo must be under 5MB.';
        else {
            $ext   = pathinfo($file['name'], PATHINFO_EXTENSION);
            $photo = uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], '../uploads/' . $photo);
            if (!empty($u->photo) && $u->photo !== 'default.png'
                && file_exists('../uploads/' . $u->photo))
                unlink('../uploads/' . $u->photo);
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE user
            SET name=?, email=?, photo=?,
                address=?, city=?, postcode=?, state=?, theme=?
            WHERE id=?");
        $stmt->execute([$name, $email, $photo, $address, $city, $postcode, $state, $theme, $id]);
        $_SESSION['user']->name  = $name;
        $_SESSION['user']->email = $email;
        $_SESSION['user']->photo = $photo;
        $_SESSION['user']->theme = $theme;
        $_user->name  = $name;
        $_user->email = $email;
        $_user->photo = $photo;
        $_user->theme = $theme;
        $success = 'Profile updated successfully!';
        $stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->execute([$id]);
        $u = $stmt->fetch();
        $theme = $u->theme ?? 'light';
    }
}

$_title = 'My Profile';
include '../_head.php';

$initials = strtoupper(implode('', array_map(
    fn($w) => $w[0],
    array_slice(explode(' ', trim($name)), 0, 2)
)));

$role_colors = [
    'Owner'  => ['bg' => '#fef3c7', 'color' => '#d97706'],
    'Admin'  => ['bg' => '#dbeafe', 'color' => '#2563eb'],
    'Member' => ['bg' => '#fdecea', 'color' => '#c0392b'],
];
$rc = $role_colors[$_user->role] ?? $role_colors['Member'];
?>

<div class="profile-wrap">

    <?php if ($success): ?>
        <div class="p-success">✅ <?= $success ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <!-- AVATAR HERO -->
        <div class="avatar-hero">
            <label for="photo" class="avatar-circle">
                <?php if (!empty($u->photo) && $u->photo !== 'default.png'): ?>
                    <img id="photo-preview"
                         src="/uploads/<?= htmlspecialchars($u->photo) ?>"
                         alt="photo">
                <?php else: ?>
                    <span id="initials-text"><?= $initials ?></span>
                    <img id="photo-preview" src="" alt="" style="display:none;">
                <?php endif; ?>
                <div class="avatar-overlay">📷 Change</div>
            </label>
            <input type="file" id="photo" name="photo"
                   accept="image/*" style="display:none;"
                   onchange="previewPhoto(this)">
            <div class="avatar-hero-name"><?= htmlspecialchars($u->name) ?></div>
            <div class="avatar-hero-email"><?= htmlspecialchars($u->email) ?></div>
            <span class="role-badge"
                  style="background:<?= $rc['bg'] ?>; color:<?= $rc['color'] ?>;">
                <?= htmlspecialchars($_user->role) ?>
            </span>
            <div class="photo-hint">JPG, PNG, GIF — max 5MB</div>
            <?php if (!empty($errors['photo'])): ?>
                <div style="color:#fca5a5; font-size:.82rem; margin-top:.5rem;">
                    ⚠️ <?= $errors['photo'] ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- BASIC INFO -->
        <div class="p-card">
            <div class="p-section-label">👤 Basic Info</div>
            <div class="p-field">
                <label>Full Name</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($name) ?>"
                       placeholder="Your full name">
                <?php if (!empty($errors['name'])): ?>
                    <span class="err">⚠️ <?= $errors['name'] ?></span>
                <?php endif; ?>
            </div>
            <div class="p-field">
                <label>Email Address</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($email) ?>"
                       placeholder="Your email">
                <?php if (!empty($errors['email'])): ?>
                    <span class="err">⚠️ <?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- SHIPPING ADDRESS -->
        <div class="p-card">
            <div class="p-section-label">📦 Shipping Address</div>
            <div class="p-field">
                <label>Street Address</label>
                <input type="text" name="address"
                       value="<?= htmlspecialchars($address) ?>"
                       placeholder="e.g. No 12, Jalan Bunga">
            </div>
            <div class="p-grid-2">
                <div class="p-field">
                    <label>City</label>
                    <input type="text" name="city"
                           value="<?= htmlspecialchars($city) ?>"
                           placeholder="e.g. Kuala Lumpur">
                </div>
                <div class="p-field">
                    <label>Postcode</label>
                    <input type="text" name="postcode"
                           value="<?= htmlspecialchars($postcode) ?>"
                           placeholder="e.g. 53300">
                </div>
            </div>
            <div class="p-field">
                <label>State</label>
                <input type="text" name="state"
                       value="<?= htmlspecialchars($state) ?>"
                       placeholder="e.g. Selangor">
            </div>
        </div>

        <!-- THEME PREFERENCE -->
        <div class="p-card">
            <div class="p-section-label">🎨 Theme Preference</div>
            <div class="theme-toggle">
                <span>🌙 Dark Mode</span>
                <label class="switch">
                    <input type="checkbox" name="theme" value="dark"
                           id="theme-toggle"
                           <?= ($theme === 'dark') ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
            </div>
            <p class="p-hint" style="margin-top:8px;">
                Your theme preference is saved and applied every time you log in.
            </p>
        </div>

        <button type="submit" name="btn_update" class="p-save-btn">
            💾 Save Changes
        </button>

    </form>

    <a href="/user/password.php" class="p-link">🔒 Change Password</a>

</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById('photo-preview');
            var txt = document.getElementById('initials-text');
            img.src = e.target.result;
            img.style.display = 'block';
            if (txt) txt.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('theme-toggle').addEventListener('change', function() {
    document.body.classList.toggle('dark-theme', this.checked);
});
</script>

<?php include '../_foot.php'; ?>