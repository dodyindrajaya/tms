<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('css/chart-of-accounts.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="eyebrow">FINANCE / ACCOUNTING</div>
        <h1>Chart of Accounts</h1>
        <p>Manage accounts used by journals and financial reports.</p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-primary" href="<?= site_url('accounting/accounts/create') ?>">+ New Account</a>
    </div>
</div>

<div class="stat-grid coa-stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div><div class="stat-label">Total Accounts</div><div class="stat-value"><?= (int) ($stats['total'] ?? 0) ?></div></div><div class="stat-icon">Σ</div></div>
        <div class="stat-meta">All chart of account records</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div><div class="stat-label">Active</div><div class="stat-value"><?= (int) ($stats['active'] ?? 0) ?></div></div><div class="stat-icon">✓</div></div>
        <div class="stat-meta">Available for accounting use</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div><div class="stat-label">Control Accounts</div><div class="stat-value"><?= (int) ($stats['control'] ?? 0) ?></div></div><div class="stat-icon">◉</div></div>
        <div class="stat-meta">System-managed accounts</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div><div class="stat-label">Account Groups</div><div class="stat-value"><?= (int) ($stats['groups'] ?? 0) ?></div></div><div class="stat-icon">▦</div></div>
        <div class="stat-meta">Reporting groups</div>
    </div>
</div>

<form class="toolbar-card coa-toolbar" method="get" action="<?= site_url('accounting/accounts') ?>">
    <div class="search-toolbar">
        <div class="search-input">
            <span>⌕</span>
            <input name="q" value="<?= esc($q ?? '') ?>" placeholder="Search account code, name or parent...">
        </div>
        <select class="filter-select" name="type">
            <option value="">All Types</option>
            <?php foreach (($accountTypes ?? []) as $k => $label): ?>
                <option value="<?= esc($k) ?>" <?= ($type ?? '') === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="filter-select" name="status">
            <option value="">All Status</option>
            <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
        <select class="filter-select" name="control">
            <option value="">All Accounts</option>
            <option value="yes" <?= ($control ?? '') === 'yes' ? 'selected' : '' ?>>Control Accounts</option>
            <option value="no" <?= ($control ?? '') === 'no' ? 'selected' : '' ?>>Posting Accounts</option>
        </select>
        <button class="btn btn-secondary" type="submit">Search</button>
        <?php if (($q ?? '') !== '' || ($type ?? '') !== '' || ($status ?? '') !== '' || ($control ?? '') !== ''): ?>
            <a class="btn btn-secondary" href="<?= site_url('accounting/accounts') ?>">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card coa-table-card">
    <div class="coa-table-head">
        <div>
            <h2>Account Records</h2>
            <p><?= is_countable($accounts ?? []) ? count($accounts) : 0 ?> record(s) shown</p>
        </div>
        <div class="coa-legend">
            <span><i class="coa-dot coa-dot-control"></i> Control</span>
            <span><i class="coa-dot coa-dot-posting"></i> Posting</span>
        </div>
    </div>
    <div class="table-wrap">
        <table class="coa-table">
            <thead>
                <tr>
                    <th>Code</th><th>Account</th><th>Type</th><th>Group</th><th>Parent</th>
                    <th>Posting</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($accounts)): ?>
                <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">Σ</div><h3>No accounts found</h3><p>Try changing the search or filter.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($accounts as $a): ?>
                    <?php $typeKey = (string) ($a['account_type'] ?? ''); ?>
                    <tr>
                        <td><strong class="coa-code"><?= esc($a['code']) ?></strong></td>
                        <td>
                            <div class="coa-account-name">
                                <i class="coa-kind-dot <?= (int)($a['is_control_account'] ?? 0) ? 'is-control' : 'is-posting' ?>"></i>
                                <div>
                                    <strong><?= esc($a['name']) ?></strong>
                                    <?php if ((int)($a['is_control_account'] ?? 0)): ?><small>Control account</small><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><span class="coa-type coa-type-<?= esc($typeKey) ?>"><?= esc($accountTypes[$typeKey] ?? $typeKey) ?></span></td>
                        <td><?= esc(($a['group_code'] ?? '') ? $a['group_code'].' — '.$a['group_name'] : '-') ?></td>
                        <td><?= esc(($a['parent_code'] ?? '') ? $a['parent_code'].' — '.$a['parent_name'] : '-') ?></td>
                        <td><span class="coa-posting <?= (int)($a['allow_manual_posting'] ?? 0) ? 'yes' : 'no' ?>"><?= (int)($a['allow_manual_posting'] ?? 0) ? 'Allowed' : 'Restricted' ?></span></td>
                        <td><?= (int)($a['is_active'] ?? 0) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-muted">Inactive</span>' ?></td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-small btn-secondary" href="<?= site_url('accounting/accounts/show/'.$a['id']) ?>">View</a>
                                <a class="btn btn-small btn-secondary" href="<?= site_url('accounting/accounts/edit/'.$a['id']) ?>">Edit</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?><div class="coa-pagination"><?= $pager->links() ?></div><?php endif; ?>
</div>
<?= $this->endSection() ?>
