<?php
$page_title = 'Appointments';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<h1 class="page-title"><i class="fas fa-calendar-plus"></i> Appointment Management</h1>

<div class="form-panel">
    <h3><?= $edit ? 'Edit Appointment' : 'Create Appointment' ?></h3>
    <form method="POST" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="appointment_id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Patient *</label>
                <select name="patient_id" class="form-control" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= (int)$p['id'] ?>" <?= ($edit['patient_id']??'')==$p['id']?'selected':'' ?>><?= e($p['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Doctor *</label>
                <select name="doctor_id" class="form-control" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= ($edit['doctor_id']??'')==$d['id']?'selected':'' ?>><?= e($d['full_name']) ?> (<?= e($d['specialization']??'') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="appointment_date" class="form-control" required value="<?= e($edit['appointment_date']??'') ?>">
            </div>
            <div class="form-group">
                <label>Time *</label>
                <input type="time" name="appointment_time" class="form-control" required value="<?= e(isset($edit['appointment_time'])?substr($edit['appointment_time'],0,5):'') ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <?php foreach (['pending','confirmed','completed','cancelled','no-show'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($edit['status']??'confirmed')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Reason</label>
            <textarea name="reason" class="form-control" rows="2"><?= e($edit['reason']??'') ?></textarea>
        </div>
        <?php if ($edit): ?>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="2"><?= e($edit['notes']??'') ?></textarea>
        </div>
        <?php endif; ?>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            <?php if ($edit): ?><a href="index.php?page=receptionist_appointments" class="btn btn-secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>All Appointments</h3>
        <form class="search-bar" method="GET">
            <input type="hidden" name="page" value="receptionist_appointments">
            <input type="text" name="q" placeholder="Search..." value="<?= e($_GET['q']??'') ?>">
            <select name="status">
                <option value="">All Status</option>
                <?php foreach (['pending','confirmed','completed','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($_GET['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" value="<?= e($_GET['date']??'') ?>">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Date</th><th>Time</th><th>Patient</th><th>Doctor</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($appointments)): ?>
                <tr><td colspan="6" class="empty-state">No appointments.</td></tr>
            <?php else: foreach ($appointments as $a): ?>
                <tr>
                    <td><?= e($a['appointment_date']) ?></td>
                    <td><?= e(substr($a['appointment_time'],0,5)) ?></td>
                    <td><?= e($a['patient_name']) ?></td>
                    <td><?= e($a['doctor_name']) ?></td>
                    <td><span class="badge badge-<?= e($a['status']) ?>"><?= e($a['status']) ?></span></td>
                    <td class="actions">
                        <a href="index.php?page=receptionist_appointments&id=<?= (int)$a['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" style="display:inline;" data-confirm="Delete appointment?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_action" value="delete">
                            <input type="hidden" name="appointment_id" value="<?= (int)$a['id'] ?>">
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
