<?php
$page_title = 'Invoices';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<h1 class="page-title"><i class="fas fa-file-invoice-dollar"></i> Invoice Management</h1>

<div class="form-panel">
    <h3><?= $edit ? 'Edit Invoice' : 'Create Invoice' ?></h3>
    <form method="POST" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="invoice_id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Patient *</label>
                <select name="patient_id" class="form-control" required <?= $edit?'disabled':'' ?>>
                    <option value="">-- Select --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= (int)$p['id'] ?>" <?= ($edit['patient_id']??'')==$p['id']?'selected':'' ?>><?= e($p['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Amount *</label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0" required value="<?= e($edit['amount']??'') ?>">
            </div>
            <div class="form-group">
                <label>Tax</label>
                <input type="number" name="tax" class="form-control" step="0.01" min="0" value="<?= e($edit['tax']??'0') ?>">
            </div>
            <div class="form-group">
                <label>Discount</label>
                <input type="number" name="discount" class="form-control" step="0.01" min="0" value="<?= e($edit['discount']??'0') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control" value="<?= e($edit['due_date']??'') ?>">
            </div>
            <?php if ($edit): ?>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <?php foreach (['unpaid','partial','paid','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($edit['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="2"><?= e($edit['description']??'') ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            <?php if ($edit): ?><a href="index.php?page=receptionist_invoices" class="btn btn-secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Invoices</h3>
        <form class="search-bar" method="GET">
            <input type="hidden" name="page" value="receptionist_invoices">
            <input type="text" name="q" placeholder="Search..." value="<?= e($_GET['q']??'') ?>">
            <select name="status">
                <option value="">All</option>
                <?php foreach (['unpaid','partial','paid','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($_GET['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Invoice #</th><th>Patient</th><th>Amount</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($invoices)): ?>
                <tr><td colspan="7" class="empty-state">No invoices.</td></tr>
            <?php else: foreach ($invoices as $inv): ?>
                <tr>
                    <td><?= e($inv['invoice_number']) ?></td>
                    <td><?= e($inv['patient_name']) ?></td>
                    <td><?= format_money($inv['amount']) ?></td>
                    <td><?= format_money($inv['total_amount']) ?></td>
                    <td><span class="badge badge-<?= e($inv['status']) ?>"><?= e($inv['status']) ?></span></td>
                    <td><?= e(date('d M Y', strtotime($inv['created_at']))) ?></td>
                    <td class="actions">
                        <a href="index.php?page=receptionist_invoices&id=<?= (int)$inv['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" style="display:inline;" data-confirm="Delete invoice and its payments?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_action" value="delete">
                            <input type="hidden" name="invoice_id" value="<?= (int)$inv['id'] ?>">
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
