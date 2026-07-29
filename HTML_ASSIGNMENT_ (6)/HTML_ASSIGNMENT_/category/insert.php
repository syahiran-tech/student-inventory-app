<?php
require '../_base.php';
auth('Admin', 'Owner');

$errors = [];
$name = $desc = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['desc'] ?? '');

    if ($name === '') {
        $errors['name'] = 'Category name is required.';
    } else {
        $dup = $pdo->prepare('SELECT COUNT(*) FROM category WHERE name = ?');
        $dup->execute([$name]);
        if ($dup->fetchColumn() > 0) {
            $errors['name'] = 'Category name already exists.';
        }
    }

    $photo_filename = 'default.png';
    if (!empty($_FILES['photo']['name'])) {
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
            $photo_filename = 'cat_' . uniqid() . '.' . $ext;
            $dest = '../uploads/' . $photo_filename;
            if (!move_uploaded_file($file_tmp, $dest)) {
                $errors['photo'] = 'Failed to save image. Check uploads/ folder permissions.';
                $photo_filename = 'default.png';
            }
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO category (name, description, photo) VALUES (?, ?, ?)');
        $stmt->execute([$name, $desc, $photo_filename]);
        header('Location: /category/');
        exit;
    }
}
?>
<?php include '../_head.php'; ?>

<div class="page-container-sm">
    <div class="page-header-left">
        <span style="font-size:1.5rem; color:#7c3aed;">＋</span>
        <h1 style="font-size:1.6rem; font-weight:700; color:#1a1a1a; margin:0;">Add Category</h1>
    </div>

    <a href="/category/" class="btn-back">← Back</a>

    <div class="form-card">
        <form method="post" enctype="multipart/form-data" id="categoryForm" novalidate>

            <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
                <label for="name">Category Name <span class="required">*</span></label>
                <input type="text" id="name" name="name"
                       value="<?= htmlspecialchars($name) ?>"
                       placeholder="e.g. Burgers, Drinks, Desserts">
                <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?= $errors['name'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="desc">Description</label>
                <textarea id="desc" name="desc" style="height:100px;"
                          placeholder="Brief description of this category"><?= htmlspecialchars($desc) ?></textarea>
            </div>

            <div class="form-group <?= isset($errors['photo']) ? 'has-error' : '' ?>">
                <label>Category Image</label>
                <div class="upload-area" id="uploadArea">
                    <div class="upload-preview" id="uploadPreview">
                        <img id="previewImg" src="" alt="Preview" style="display:none;">
                        <div class="upload-placeholder" id="uploadPlaceholder">
                            <span class="upload-icon">🖼️</span>
                            <p>Click or drag an image here</p>
                            <small>JPG, PNG, GIF, WEBP — max 2MB</small>
                        </div>
                    </div>
                    <input type="file" id="photo" name="photo"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           style="display:none;">
                    <button type="button" class="btn-upload" id="chooseBtn">📁 Choose Image</button>
                    <button type="button" class="btn-remove" id="removeBtn" style="display:none;">✕ Remove</button>
                </div>
                <?php if (isset($errors['photo'])): ?>
                    <span class="field-error"><?= $errors['photo'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">＋ Add Category</button>
                <a href="/category/" class="btn-cancel">Cancel</a>
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