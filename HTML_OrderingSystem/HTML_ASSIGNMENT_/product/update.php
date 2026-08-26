<?php
require '../_base.php';
auth('Admin', 'Owner');

$errors = [];
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: /product/'); exit; }

$stmt = $pdo->prepare('SELECT * FROM menu_item WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) { header('Location: /product/'); exit; }

$categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll();

$name         = $item->name;
$desc         = $item->description;
$price        = $item->price;
$category_id  = $item->category_id;
$is_available = $item->is_available;
$photo        = $item->photo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $desc         = trim($_POST['desc'] ?? '');
    $price        = trim($_POST['price'] ?? '');
    $category_id  = trim($_POST['category_id'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $keep         = $_POST['keep_photo'] ?? '1';

    if ($name === '')        $errors['name'] = 'Item name is required.';
    if ($category_id === '') $errors['category_id'] = 'Please select a category.';
    if ($price === '' || !is_numeric($price) || $price < 0)
        $errors['price'] = 'Please enter a valid price.';

    $new_photo = null;
    $has_new   = !empty($_FILES['photo']['name']);

    if ($has_new) {
        $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size  = 5 * 1024 * 1024;
        $file_tmp  = $_FILES['photo']['tmp_name'];
        $file_name = $_FILES['photo']['name'];
        $file_size = $_FILES['photo']['size'];
        $file_type = mime_content_type($file_tmp);

        if (!in_array($file_type, $allowed)) {
            $errors['photo'] = 'Only JPG, PNG, GIF, or WEBP images are allowed.';
        } elseif ($file_size > $max_size) {
            $errors['photo'] = 'Image must be under 5MB.';
        } else {
            $ext       = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_photo = 'item_' . uniqid() . '.' . $ext;
            $dest      = '../uploads/' . $new_photo;
            if (!move_uploaded_file($file_tmp, $dest)) {
                $errors['photo'] = 'Failed to save image. Check uploads/ folder permissions.';
                $new_photo = null;
            }
        }
    }

    if (!$errors) {
        if ($has_new && $new_photo) {
            if ($item->photo && str_starts_with($item->photo, 'item_') && file_exists('../uploads/' . $item->photo)) {
                unlink('../uploads/' . $item->photo);
            }
            $final_photo = $new_photo;
        } elseif ($keep === '0') {
            if ($item->photo && str_starts_with($item->photo, 'item_') && file_exists('../uploads/' . $item->photo)) {
                unlink('../uploads/' . $item->photo);
            }
            $final_photo = null;
        } else {
            $final_photo = $item->photo;
        }

        $stmt = $pdo->prepare('UPDATE menu_item SET name=?, description=?, price=?, photo=?, category_id=?, is_available=? WHERE id=?');
        $stmt->execute([$name, $desc, $price, $final_photo, $category_id, $is_available, $id]);
        header('Location: /product/');
        exit;
    }

    $photo = $has_new && $new_photo ? $new_photo : $item->photo;
}

$has_photo   = $photo && file_exists('../uploads/' . $photo);
$preview_src = $has_photo ? '/uploads/' . htmlspecialchars($photo) : '';
?>
<?php include '../_head.php'; ?>

<div class="page-container-md">
    <div class="page-header-left">
        <span style="font-size:1.5rem;">✏️</span>
        <h1 style="font-size:1.6rem; font-weight:700; color:#1a1a1a; margin:0;">Edit Menu Item</h1>
    </div>
    <a href="/product/" class="btn-back">← Back</a>

    <div class="form-card">
        <form method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="keep_photo" id="keepPhoto" value="1">

            <div class="form-row">
                <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
                    <label for="name">Item Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?= htmlspecialchars($name) ?>"
                           placeholder="e.g. Classic Beef Burger">
                    <?php if (isset($errors['name'])): ?>
                        <span class="field-error"><?= $errors['name'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['category_id']) ? 'has-error' : '' ?>">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"
                                <?= $category_id == $cat->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category_id'])): ?>
                        <span class="field-error"><?= $errors['category_id'] ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="desc">Description</label>
                <textarea id="desc" name="desc" style="height:90px;"
                          placeholder="Brief description of this item"><?= htmlspecialchars($desc) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group <?= isset($errors['price']) ? 'has-error' : '' ?>">
                    <label for="price">Price (RM) <span class="required">*</span></label>
                    <input type="number" id="price" name="price" step="0.01" min="0"
                           value="<?= htmlspecialchars($price) ?>" placeholder="0.00">
                    <?php if (isset($errors['price'])): ?>
                        <span class="field-error"><?= $errors['price'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Availability</label>
                    <div class="toggle-wrap">
                        <input type="checkbox" id="is_available" name="is_available"
                               <?= $is_available ? 'checked' : '' ?>>
                        <label for="is_available" class="toggle-label">Available for order</label>
                    </div>
                </div>
            </div>

            <div class="form-group <?= isset($errors['photo']) ? 'has-error' : '' ?>">
                <label>Item Photo</label>
                <div class="upload-area" id="uploadArea">
                    <div class="upload-preview">
                        <img id="previewImg" src="<?= $preview_src ?>" alt="Preview"
                             style="<?= $has_photo ? '' : 'display:none;' ?>">
                        <div class="upload-placeholder" id="uploadPlaceholder"
                             style="<?= $has_photo ? 'display:none;' : '' ?>">
                            <span class="upload-icon">🍽️</span>
                            <p>Click or drag a photo here</p>
                            <small>JPG, PNG, GIF, WEBP — max 5MB</small>
                        </div>
                    </div>
                    <input type="file" id="photo" name="photo"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           style="display:none;">
                    <button type="button" class="btn-upload" id="chooseBtn">
                        📁 <?= $has_photo ? 'Change Photo' : 'Choose Photo' ?>
                    </button>
                    <button type="button" class="btn-remove" id="removeBtn"
                            style="<?= $has_photo ? '' : 'display:none;' ?>">✕ Remove</button>
                </div>
                <?php if (isset($errors['photo'])): ?>
                    <span class="field-error"><?= $errors['photo'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                <a href="/product/" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
$(function () {
    const $input    = $('#photo');
    const $preview  = $('#previewImg');
    const $holder   = $('#uploadPlaceholder');
    const $remove   = $('#removeBtn');
    const $area     = $('#uploadArea');
    const $keepPhoto = $('#keepPhoto');
    const $chooseBtn = $('#chooseBtn');

    $chooseBtn.on('click', function () { $input.trigger('click'); });

    $input.on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $preview.attr('src', e.target.result).show();
            $holder.hide(); $remove.show();
            $chooseBtn.text('📁 Change Photo');
            $keepPhoto.val('1');
        };
        reader.readAsDataURL(file);
    });

    $remove.on('click', function () {
        $input.val('');
        $preview.hide().attr('src', '');
        $holder.show(); $remove.hide();
        $chooseBtn.text('📁 Choose Photo');
        $keepPhoto.val('0');
    });

    $area.on('dragover', function (e) {
        e.preventDefault(); $(this).addClass('dragover');
    }).on('dragleave drop', function (e) {
        e.preventDefault(); $(this).removeClass('dragover');
        if (e.type === 'drop') {
            const file = e.originalEvent.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dt = new DataTransfer();
                dt.items.add(file);
                $input[0].files = dt.files;
                $input.trigger('change');
            }
        }
    });
});
</script>

<?php include '../_foot.php'; ?>