<?php
$page_title = 'Payments';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<h1 class="page-title"><i class="fas fa-money-bill-wave"></i> Receive Payments</h1>

<div class="form-panel">
    <h3>Record Payment</h3>
    <form method="POST" data-validate>
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="create">
        <div class="form-row">
            <div class="form-group">
                <label>Invoice *</label>
                <select name="invoice_id" class="form-control" required>
                    <option value="">-- Select unpaid/partial invoice --</option>
                    <?php foreach ($open_invoices as $inv): ?>
                        <option value="<?= (int)$inv['id'] ?>">
                            <?= e($inv['invoice_number']) ?> - <?= e($inv['patient_name']) ?> (<?= format_money($inv['total_amount']) ?> - <?= e($inv['status']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Amount *</label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
            </div>
            <div class="form-group">
                <label>Method</label>
                <select name="payment_method" class="form-control">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="online">Online</option>
                    <option value="insurance">Insurance</option>
                </select>
            </div>
            <div class="form-group">
                <label>Transaction Ref</label>
                <input type="text" name="transaction_ref" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <input type="text" name="notes" class="form-control">
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Record Payment</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Payment History</h3>
        <form class="search-bar" method="GET">
            <input type="hidden" name="page" value="receptionist_payments">
            <input type="text" name="q" placeholder="Search..." value="<?= e($_GET['q']??'') ?>">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Date</th><th>Invoice</th><th>Patient</th><th>Amount</th><th>Method</th><th>Ref</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($payments)): ?>
                <tr><td colspan="7" class="empty-state">No payments recorded.</td></tr>
            <?php else: foreach ($payments as $py): ?>
                <tr>
                    <td><?= e(date('d M Y H:i', strtotime($py['payment_date']))) ?></td>
                    <td><?= e($py['invoice_number']) ?></td>
                    <td><?= e($py['patient_name']) ?></td>
                    <td><?= format_money($py['amount']) ?></td>
                    <td><?= e(ucfirst($py['payment_method'])) ?></td>
                    <td><?= e($py['transaction_ref']??'-') ?></td>
                    <td>
                        <form method="POST" style="display:inline;" data-confirm="Delete this payment?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_action" value="delete">
                            <input type="hidden" name="payment_id" value="<?= (int)$py['id'] ?>">
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
