<?php $page_title = 'Login - Hospital Management System'; ?>
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
    <div class="auth-card">
        <div class="logo"><i class="fas fa-hospital"></i></div>
        <h1>Hospital Management</h1>
        <p class="subtitle">Sign in to your account</p>
        
        <form method="POST" action="index.php?page=login" data-validate>
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember" style="margin:0;">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <p style="text-align:center;margin-top:1.25rem;font-size:0.9rem;">
            New patient? <a href="index.php?page=register">Register here</a>
        </p>
        
        <div style="margin-top:1.5rem;padding:0.75rem;background:#f8f9fa;border-radius:8px;font-size:0.8rem;color:#6c757d;">
            <strong>Demo accounts</strong> (password: <code>password</code>)<br>
            Admin: admin &nbsp;|&nbsp; Doctor: dr.smith<br>
            Reception: reception &nbsp;|&nbsp; Patient: patient1
        </div>
    </div>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>
