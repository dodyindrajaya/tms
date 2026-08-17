<?php
$activeMenu = $activeMenu ?? '';
$contentView = $contentView ?? '';
$contentData = $contentData ?? [];
$pageTitle = $pageTitle ?? 'TMS';
$username = session('username') ?: session('name') ?: 'Administrator';
$userRole = session('role_name') ?: 'System Admin';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?> · TMS</title>
    <link rel="stylesheet" href="<?= base_url('css/tms-ui-v1.css') ?>">
</head>
<body class="tms-body">
<div class="tms-shell">
    <aside class="tms-sidebar">
        <div class="tms-brand">
            <div class="tms-brand-mark">✈</div>
            <div><strong>TMS</strong><span>Travel Management</span></div>
        </div>

        <nav class="tms-nav">
            <div class="tms-nav-label">MAIN</div>
            <a class="tms-nav-item <?= $activeMenu==='dashboard'?'active':'' ?>" href="<?= site_url('/') ?>"><span class="nav-ico">⌂</span>Dashboard</a>

            <div class="tms-nav-label">TRANSACTIONS</div>
            <a class="tms-nav-item <?= $activeMenu==='bookings'?'active':'' ?>" href="<?= site_url('bookings') ?>"><span class="nav-ico">▣</span>Bookings</a>
            <a class="tms-nav-item <?= $activeMenu==='ticketing'?'active':'' ?>" href="<?= site_url('ticketing') ?>"><span class="nav-ico">✈</span>Ticketing</a>
            <a class="tms-nav-item <?= $activeMenu==='tours'?'active':'' ?>" href="<?= site_url('tours') ?>"><span class="nav-ico">◇</span>Tours</a>
            <a class="tms-nav-item <?= $activeMenu==='invoices'?'active':'' ?>" href="<?= site_url('invoices') ?>"><span class="nav-ico">≡</span>Invoices</a>
            <a class="tms-nav-item <?= $activeMenu==='payments'?'active':'' ?>" href="<?= site_url('payments') ?>"><span class="nav-ico">Rp</span>Payments</a>

            <div class="tms-nav-label">MASTER DATA</div>
            <a class="tms-nav-item <?= $activeMenu==='customers'?'active':'' ?>" href="<?= site_url('customers') ?>"><span class="nav-ico">♙</span>Customers</a>
            <a class="tms-nav-item <?= $activeMenu==='passengers'?'active':'' ?>" href="<?= site_url('passengers') ?>"><span class="nav-ico">□</span>Passengers</a>
            <a class="tms-nav-item <?= $activeMenu==='products'?'active':'' ?>" href="<?= site_url('products') ?>"><span class="nav-ico">□</span>Products</a>
            <a class="tms-nav-item <?= $activeMenu==='suppliers'?'active':'' ?>" href="<?= site_url('suppliers') ?>"><span class="nav-ico">⌂</span>Suppliers</a>
            <a class="tms-nav-item <?= $activeMenu==='employees'?'active':'' ?>" href="<?= site_url('employees') ?>"><span class="nav-ico">♟</span>Employees</a>

            <div class="tms-nav-label">FINANCE</div>
            <a class="tms-nav-item <?= $activeMenu==='finance'?'active':'' ?>" href="<?= site_url('finance') ?>"><span class="nav-ico">▥</span>Finance Dashboard</a>
            <a class="tms-nav-item <?= $activeMenu==='ledger'?'active':'' ?>" href="<?= site_url('ledger') ?>"><span class="nav-ico">Σ</span>General Ledger</a>
            <a class="tms-nav-item <?= $activeMenu==='receivable'?'active':'' ?>" href="<?= site_url('receivable') ?>"><span class="nav-ico">AR</span>Receivable</a>
            <a class="tms-nav-item <?= $activeMenu==='payable'?'active':'' ?>" href="<?= site_url('payable') ?>"><span class="nav-ico">AP</span>Payable</a>
            <a class="tms-nav-item <?= $activeMenu==='reports'?'active':'' ?>" href="<?= site_url('reports') ?>"><span class="nav-ico">▥</span>Reports</a>

            <div class="tms-nav-label">SYSTEM</div>
            <a class="tms-nav-item <?= $activeMenu==='settings'?'active':'' ?>" href="<?= site_url('settings') ?>"><span class="nav-ico">⚙</span>Settings</a>
        </nav>

        <div class="tms-help">
            <div class="help-icon">?</div>
            <div><strong>Need help?</strong><span>TMS support center</span></div>
        </div>
    </aside>

    <section class="tms-main">
        <header class="tms-topbar">
            <div class="tms-breadcrumb"><span>TMS</span><b>/</b><strong><?= esc(ucwords(str_replace(['-','_'],' ', $activeMenu ?: 'Page'))) ?></strong></div>
            <div class="tms-top-actions">
                <a class="top-booking" href="<?= site_url('bookings/new') ?>">+ New Booking</a>
                <button class="top-icon" type="button" aria-label="Notifications">♢</button>
                <div class="user-box">
                    <div class="avatar"><?= esc(strtoupper(substr($username,0,1))) ?></div>
                    <div><strong><?= esc($username) ?></strong><span><?= esc($userRole) ?></span></div>
                    <span class="user-chevron">⌄</span>
                </div>
            </div>
        </header>
        <main class="tms-content">
            <?= view($contentView, $contentData) ?>
        </main>
        <footer class="tms-footer"><span>TMS v1.0</span><span><?= date('Y') ?> Travel Management System</span></footer>
    </section>
</div>
</body>
</html>
