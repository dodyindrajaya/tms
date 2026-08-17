<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('css/chart-of-accounts.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $account = is_array($account ?? null) ? $account : []; ?>
<div class="page-header">
    <div>
        <div class="eyebrow">FINANCE / ACCOUNTING</div>
        <h1><?= esc(($account['code'] ?? '').' — '.($account['name'] ?? 'Account')) ?></h1>
        <p>Chart of Accounts detail and current usage.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-secondary" href="<?= site_url('accounting/accounts') ?>">Back</a>
        <a class="btn btn-primary" href="<?= site_url('accounting/accounts/edit/'.$account['id']) ?>">Edit Account</a>
    </div>
</div>

<div class="coa-detail-grid">
    <div class="card coa-detail-card">
        <div class="coa-detail-title-row">
            <div>
                <span class="coa-code-large"><?= esc($account['code'] ?? '-') ?></span>
                <h2><?= esc($account['name'] ?? '-') ?></h2>
            </div>
            <span class="badge <?= (int)($account['is_active'] ?? 0) ? 'badge-success' : 'badge-muted' ?>"><?= (int)($account['is_active'] ?? 0) ? 'Active' : 'Inactive' ?></span>
        </div>
        <div class="coa-detail-fields">
            <div><span>Account Type</span><strong><?= esc(ucwords(str_replace('_', ' ', $account['account_type'] ?? '-'))) ?></strong></div>
            <div><span>Account Group</span><strong><?= esc(($account['group_code'] ?? '') ? $account['group_code'].' — '.$account['group_name'] : '-') ?></strong></div>
            <div><span>Parent Account</span><strong><?= esc(($account['parent_code'] ?? '') ? $account['parent_code'].' — '.$account['parent_name'] : '-') ?></strong></div>
            <div><span>Posting</span><strong><?= (int)($account['allow_manual_posting'] ?? 0) ? 'Allowed' : 'Restricted' ?></strong></div>
            <div><span>Control Account</span><strong><?= (int)($account['is_control_account'] ?? 0) ? 'Yes' : 'No' ?></strong></div>
            <div><span>Status</span><strong><?= (int)($account['is_active'] ?? 0) ? 'Active' : 'Inactive' ?></strong></div>
        </div>
    </div>

    <div class="coa-detail-side">
        <div class="stat-card"><div class="stat-label">Journal Lines</div><div class="stat-value"><?= (int)($usage['line_count'] ?? 0) ?></div><div class="stat-meta">Transactions posted to this account</div></div>
        <div class="stat-card"><div class="stat-label">Total Debit</div><div class="stat-value coa-money">Rp <?= number_format((float)($usage['total_debit'] ?? 0), 0, ',', '.') ?></div></div>
        <div class="stat-card"><div class="stat-label">Total Credit</div><div class="stat-value coa-money">Rp <?= number_format((float)($usage['total_credit'] ?? 0), 0, ',', '.') ?></div></div>
    </div>
</div>

<div class="card coa-children-card">
    <div class="coa-table-head"><div><h2>Child Accounts</h2><p>Accounts directly under this account.</p></div></div>
    <div class="table-wrap">
        <table class="coa-table">
            <thead><tr><th>Code</th><th>Account</th><th>Type</th><th>Posting</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php if (empty($children)): ?>
                <tr><td colspan="6"><div class="coa-empty-inline">No child accounts.</div></td></tr>
            <?php else: ?>
                <?php foreach ($children as $child): ?>
                    <tr>
                        <td><strong class="coa-code"><?= esc($child['code']) ?></strong></td>
                        <td><strong><?= esc($child['name']) ?></strong></td>
                        <td><?= esc(ucwords(str_replace('_', ' ', $child['account_type'] ?? '-'))) ?></td>
                        <td><?= (int)($child['allow_manual_posting'] ?? 0) ? 'Allowed' : 'Restricted' ?></td>
                        <td><?= (int)($child['is_active'] ?? 0) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-muted">Inactive</span>' ?></td>
                        <td><a class="btn btn-small btn-secondary" href="<?= site_url('accounting/accounts/show/'.$child['id']) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
