<?php
$page_title = 'Queue Management';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<h1 class="page-title"><i class="fas fa-list-ol"></i> Patient Queue</h1>

<div class="form-panel">
    <h3>Add to Queue</h3>
    <form method="POST" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="add">
        <div class="form-row">
            <div class="form-group">
                <label>Patient *</label>
                <select name="patient_id" class="form-control" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= (int)$p['id'] ?>"><?= e($p['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Doctor *</label>
                <select name="doctor_id" class="form-control" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?= (int)$d['id'] ?>"><?= e($d['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="queue_date" class="form-control" value="<?= e($date) ?>">
            </div>
            <div class="form-group">
                <label>Priority</label>
                <select name="priority" class="form-control">
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add to Queue</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Current Queue</h3>
        <form class="search-bar" method="GET">
            <input type="hidden" name="page" value="receptionist_queue">
            <input type="date" name="date" value="<?= e($date) ?>">
            <select name="doctor_id">
                <option value="">All Doctors</option>
                <?php foreach ($doctors as $d): ?>
                    <option value="<?= (int)$d['id'] ?>" <?= ($doctor_id??'')==$d['id']?'selected':'' ?>><?= e($d['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="q" placeholder="Search patient..." value="<?= e($_GET['q']??'') ?>">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>#</th><th>Patient</th><th>Phone</th><th>Doctor</th><th>Priority</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($queue)): ?>
                <tr><td colspan="7" class="empty-state">Queue is empty for this date.</td></tr>
            <?php else: foreach ($queue as $q): ?>
                <tr>
                    <td><strong><?= (int)$q['queue_number'] ?></strong></td>
                    <td><?= e($q['patient_name']) ?></td>
                    <td><?= e($q['patient_phone']??'-') ?></td>
                    <td><?= e($q['doctor_name']) ?></td>
                    <td><span class="badge badge-<?= e($q['priority']) ?>"><?= e($q['priority']) ?></span></td>
                    <td><span class="badge badge-<?= e($q['status']) ?>"><?= e($q['status']) ?></span></td>
                    <td class="actions">
                        <form method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_action" value="update_status">
                            <input type="hidden" name="queue_id" value="<?= (int)$q['id'] ?>">
                            <select name="status" class="form-control" style="width:auto;display:inline-block;padding:2px 6px;font-size:0.8rem;" onchange="this.form.submit()">
                                <?php foreach (['waiting','called','in-progress','completed','skipped'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $q['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                        <form method="POST" style="display:inline;" data-confirm="Remove from queue?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_action" value="delete">
                            <input type="hidden" name="queue_id" value="<?= (int)$q['id'] ?>">
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
