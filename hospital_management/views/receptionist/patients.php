<?php
$page_title = 'Manage Patients';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<h1 class="page-title"><i class="fas fa-users"></i> Patient Registration & Management</h1>

<div class="form-panel">
    <h3><?= $edit ? 'Edit Patient' : 'Register New Patient' ?></h3>
    <form method="POST" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="user_id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" class="form-control" required value="<?= e($edit['full_name']??'') ?>">
            </div>
            <?php if (!$edit): ?>
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" class="form-control" required minlength="3">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="text" name="password" class="form-control" value="password123" placeholder="Default: password123">
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" required value="<?= e($edit['email']??'') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= e($edit['phone']??'') ?>">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">--</option>
                    <?php foreach (['male','female','other'] as $g): ?>
                        <option value="<?= $g ?>" <?= ($edit['gender']??'')===$g?'selected':'' ?>><?= ucfirst($g) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?= e($edit['date_of_birth']??'') ?>">
            </div>
            <div class="form-group">
                <label>Blood Group</label>
                <select name="blood_group" class="form-control">
                    <option value="">--</option>
                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                        <option <?= ($edit['blood_group']??'')===$bg?'selected':'' ?>><?= $bg ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Emergency Contact</label>
                <input type="text" name="emergency_contact" class="form-control" value="<?= e($edit['emergency_contact']??'') ?>">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" value="<?= e($edit['address']??'') ?>">
            </div>
            <?php if ($edit): ?>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?= ($edit['status']??'')==='active'?'selected':'' ?>>Active</option>
                    <option value="inactive" <?= ($edit['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Allergies</label>
            <textarea name="allergies" class="form-control" rows="2"><?= e($edit['allergies']??'') ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $edit?'Update':'Register' ?></button>
            <?php if ($edit): ?><a href="index.php?page=receptionist_patients" class="btn btn-secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Patients List</h3>
        <form class="search-bar" method="GET">
            <input type="hidden" name="page" value="receptionist_patients">
            <input type="text" name="q" placeholder="Search..." value="<?= e($_GET['q']??'') ?>">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Phone</th><th>Blood</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($patients)): ?>
                <tr><td colspan="7" class="empty-state">No patients found.</td></tr>
            <?php else: foreach ($patients as $p): ?>
                <tr>
                    <td><?= e($p['full_name']) ?></td>
                    <td><?= e($p['username']) ?></td>
                    <td><?= e($p['email']) ?></td>
                    <td><?= e($p['phone']??'-') ?></td>
                    <td><?= e($p['blood_group']??'-') ?></td>
                    <td><span class="badge badge-<?= e($p['status']) ?>"><?= e($p['status']) ?></span></td>
                    <td class="actions">
                        <a href="index.php?page=receptionist_patients&id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" style="display:inline;" data-confirm="Delete this patient?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_action" value="delete">
                            <input type="hidden" name="user_id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
