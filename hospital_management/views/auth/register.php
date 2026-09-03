<?php $page_title = 'Register - Hospital Management System'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php $flash = get_flash(); if ($flash): ?>
<div class="flash-message flash-<?= e($flash['type']) ?>" id="flashMsg">
    <span><?= e($flash['message']) ?></span>
    <button onclick="this.parentElement.style.display='none'">&times;</button>
</div>
<?php endif; ?>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width:520px;">
        <div class="logo"><i class="fas fa-user-plus"></i></div>
        <h1>Patient Registration</h1>
        <p class="subtitle">Create your patient account</p>
        
        <form method="POST" action="index.php?page=register" data-validate>
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" required data-check minlength="3">
                    <small id="username-feedback"></small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">-- Select --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Blood Group</label>
                    <select name="blood_group" class="form-control">
                        <option value="">-- Select --</option>
                        <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                        <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" name="emergency_contact" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                <i class="fas fa-user-plus"></i> Register
            </button>
        </form>
        
        <p style="text-align:center;margin-top:1.25rem;font-size:0.9rem;">
            Already have an account? <a href="index.php?page=login">Login</a>
        </p>
    </div>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>
