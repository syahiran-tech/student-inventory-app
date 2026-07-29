$(document).ready(function () {

    // ============================================================
    // LOGIN FORM
    // ============================================================
    $('#loginBtn').on('click', function () {
        const email    = $('#email').val().trim();
        const password = $('#password').val().trim();
        const remember = $('#remember').is(':checked') ? '1' : '';

        if (!email) {
            alert('Please enter your email.');
            return;
        }
        if (!password) {
            alert('Please enter your password.');
            return;
        }

        $('#f_email').val(email);
        $('#f_password').val(password);
        $('#f_remember').val(remember);
        $('#loginForm').submit();
    });

    // ============================================================
    // REGISTER FORM
    // ============================================================
    $('#registerBtn').on('click', function () {
        const name     = $('#name').val().trim();
        const email    = $('#email').val().trim();
        const password = $('#password').val().trim();
        const confirm  = $('#confirm').val().trim();

        if (!name)     { alert('Please enter your name.');     return; }
        if (!email)    { alert('Please enter your email.');    return; }
        if (!password) { alert('Please enter a password.');    return; }
        if (password.length < 6) {
            alert('Password must be at least 6 characters.');
            return;
        }
        if (password !== confirm) {
            alert('Passwords do not match!');
            return;
        }

        $('#f_name').val(name);
        $('#f_email').val(email);
        $('#f_password').val(password);
        $('#f_confirm').val(confirm);
        $('#regForm').submit();
    });

    // ============================================================
    // RESET PASSWORD FORM
    // ============================================================
    $('#resetBtn').on('click', function () {
        const email = $('#email').val().trim();
        if (!email) {
            alert('Please enter your email address.');
            return;
        }
        $('#f_email').val(email);
        $('#resetForm').submit();
    });

    // ============================================================
    // NEW PASSWORD FORM (token.php)
    // ============================================================
    $('#newPassBtn').on('click', function () {
        const password = $('#password').val().trim();
        const confirm  = $('#confirm').val().trim();

        if (!password) { alert('Please enter a password.'); return; }
        if (password.length < 6) {
            alert('Password must be at least 6 characters.');
            return;
        }
        if (password !== confirm) {
            alert('Passwords do not match!');
            return;
        }

        $('#f_password').val(password);
        $('#f_confirm').val(confirm);
        $('#tokenForm').submit();
    });

    // ============================================================
    // CATEGORY FORM (insert + update)
    // ============================================================
    $('#catBtn').on('click', function () {
        const name = $('#name').val().trim();
        if (!name) {
            alert('Please enter a category name.');
            return;
        }
        $('#f_name').val(name);
        $('#f_description').val($('#description').val().trim());
        $('#catForm').submit();
    });
    $('#menuBtn').on('click', function () {
        const name = $('#name').val().trim();
        if (!name) {
            alert('Please enter a Menu Item name.');
            return;
        }
        $('#f_name').val($('#name').val());
        $('#f_description').val($('#description').val());
        $('#f_price').val($('#price').val());
        $('#f_category_id').val($('#category_id').val());
        $('#catForm').submit();
    });

    // ============================================================
    // ADMIN FORM
    // ============================================================
    $('#adminBtn').on('click', function () {
        const name     = $('#name').val().trim();
        const email    = $('#email').val().trim();
        const password = $('#password').val().trim();
        const confirm  = $('#confirm').val().trim();

        if (!name)     { alert('Please enter a name.');     return; }
        if (!email)    { alert('Please enter an email.');   return; }
        if (!password) { alert('Please enter a password.'); return; }
        if (password.length < 6) {
            alert('Password must be at least 6 characters.');
            return;
        }
        if (password !== confirm) {
            alert('Passwords do not match!');
            return;
        }

        $('#f_name').val(name);
        $('#f_email').val(email);
        $('#f_password').val(password);
        $('#f_confirm').val(confirm);
        $('#adminForm').submit();
    });

    // ============================================================
    // DELETE CONFIRMATION (works for any delete button)
    // ============================================================
    $('.btn-delete').on('click', function () {
        const name = $(this).data('name');
        const id   = $(this).data('id');
        const msg  = $(this).data('msg') || 
                     'Are you sure you want to delete "' + name + '"?';

        if (confirm(msg)) {
            $('#delete_id').val(id);
            $('#deleteForm').submit();
        }
    });

    // ============================================================
    // ENTER KEY SUPPORT
    // ============================================================
    $(document).on('keypress', function (e) {
        if (e.key === 'Enter') {
            // Click whichever submit button is on the page
            $('.btn-submit-main').first().trigger('click');
        }
    });
    
    $('label.upload input[type=file]').on('change', e => {
        const f = e.target.files[0];
        const img = $(e.target).siblings('img')[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
        }
        else {
            img.src = img.dataset.src;
            e.target.value = '';
        }
    });

});