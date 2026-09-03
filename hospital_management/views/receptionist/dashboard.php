<?php
$page_title = 'Receptionist Dashboard';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>
<h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Reception Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-info"><h4><?= (int)$stats['today_appointments'] ?></h4><p>Today's Appointments</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info"><h4><?= (int)$stats['pending'] ?></h4><p>Pending</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-list-ol"></i></div>
        <div class="stat-info"><h4><?= (int)$stats['waiting_queue'] ?></h4><p>Waiting in Queue</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-info"><h4><?= (int)$stats['unpaid_invoices'] ?></h4><p>Unpaid Invoices</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-coins"></i></div>
        <div class="stat-info"><h4><?= format_money($stats['today_collection']) ?></h4><p>Today's Collection</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal"><i class="fas fa-user-plus"></i></div>
        <div class="stat-info"><h4><?= (int)$stats['new_patients_today'] ?></h4><p>New Patients Today</p></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Today's Queue</h3>
        <a href="index.php?page=receptionist_queue" class="btn btn-sm btn-outline">Manage Queue</a>
    </div>
    <?php if (empty($today_queue)): ?>
        <div class="empty-state"><i class="fas fa-list-ol"></i><p>Queue is empty.</p></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>#</th><th>Patient</th><th>Doctor</th><th>Priority</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($today_queue,0,10) as $q): ?>
                    <tr>
                        <td><strong><?= (int)$q['queue_number'] ?></strong></td>
                        <td><?= e($q['patient_name']) ?></td>
                        <td><?= e($q['doctor_name']) ?></td>
                        <td><span class="badge badge-<?= e($q['priority']) ?>"><?= e($q['priority']) ?></span></td>
                        <td><span class="badge badge-<?= e($q['status']) ?>"><?= e($q['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
