<?php
require '../_base.php';
auth('Admin', 'Owner');

$errors = [];
$name = $desc = $price = '';
$category_id  = '';
$is_available = 1;

$categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $desc         = trim($_POST['desc'] ?? '');
    $price        = trim($_POST['price'] ?? '');
    $category_id  = trim($_POST['category_id'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if ($name === '')        $errors['name'] = 'Item name is required.';
    if ($category_id === '') $errors['category_id'] = 'Please select a category.';
    if ($price === '' || !is_numeric($price) || $price < 0)
        $errors['price'] = 'Please enter a valid price.';

    $photo_filename = 'default.png';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
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
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $photo_filename = 'item_' . uniqid() . '.' . $ext;
            $dest = '../uploads/' . $photo_filename;
            if (!move_uploaded_file($file_tmp, $dest)) {
                $errors['photo'] = 'Failed to save image. Check uploads/ folder permissions.';
                $photo_filename = 'default.png';
            }
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO menu_item (name, description, price, photo, category_id, is_available) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $desc, $price, $photo_filename, $category_id, $is_available]);
        header('Location: /product/');
        exit;
    }
}
?>
<?php include '../_head.php'; ?>

<div class="page-container-md">
    <div class="page-header-left">
        <span style="font-size:1.5rem; color:#7c3aed;">＋</span>
        <h1 style="font-size:1.6rem; font-weight:700; color:#1a1a1a; margin:0;">Add Menu Item</h1>
    </div>
    <a href="/product/" class="btn-back">← Back</a>

    <div class="form-card">
        <form method="post" enctype="multipart/form-data" novalidate>

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
                        <img id="previewImg" src="" alt="Preview" style="display:none;">
                        <div class="upload-placeholder" id="uploadPlaceholder">
                            <span class="upload-icon">🍽️</span>
                            <p>Click or drag a photo here</p>
                            <small>JPG, PNG, GIF, WEBP — max 5MB</small>
                        </div>
                    </div>
                    <input type="file" id="photo" name="photo"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           style="display:none;">
                    <button type="button" class="btn-upload" id="chooseBtn">📁 Choose Photo</button>
                    <button type="button" class="btn-remove" id="removeBtn" style="display:none;">✕ Remove</button>
                </div>
                <?php if (isset($errors['photo'])): ?>
                    <span class="field-error"><?= $errors['photo'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">＋ Add Item</button>
                <a href="/product/" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
$(function () {
    const $input   = $('#photo');
    const $preview = $('#previewImg');
    const $holder  = $('#uploadPlaceholder');
    const $remove  = $('#removeBtn');
    const $area    = $('#uploadArea');

    $('#chooseBtn').on('click', function () { $input.trigger('click'); });

    $input.on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $preview.attr('src', e.target.result).show();
            $holder.hide(); $remove.show();
        };
        reader.readAsDataURL(file);
    });

    $remove.on('click', function () {
        $input.val('');
        $preview.hide().attr('src', '');
        $holder.show(); $remove.hide();
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