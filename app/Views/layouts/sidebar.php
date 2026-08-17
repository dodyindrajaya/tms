<aside class="tms-sidebar" id="tmsSidebar">
    <div class="brand">
        <div class="brand-mark">✈</div>
        <div>
            <div class="brand-name">TMS</div>
            <div class="brand-subtitle">Travel Management</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">MAIN</div>
        <a class="nav-item" href="<?= site_url('/') ?>">
            <span class="nav-icon">⌂</span><span>Dashboard</span>
        </a>

        <div class="nav-section">TRANSACTIONS</div>
        <a class="nav-item" href="<?= site_url('bookings') ?>">
            <span class="nav-icon">▣</span><span>Bookings</span>
        </a>
        <a class="nav-item" href="<?= site_url('ticketing') ?>">
            <span class="nav-icon">✈</span><span>Ticketing</span>
        </a>
        <a class="nav-item" href="<?= site_url('tours') ?>">
            <span class="nav-icon">◈</span><span>Tours</span>
        </a>
        <a class="nav-item" href="<?= site_url('invoices') ?>">
            <span class="nav-icon">▤</span><span>Invoices</span>
        </a>
        <a class="nav-item" href="<?= site_url('payments') ?>">
            <span class="nav-icon">Rp</span><span>Payments</span>
        </a>

        <div class="nav-section">MASTER DATA</div>
        <a class="nav-item" href="<?= site_url('customers') ?>">
            <span class="nav-icon">♙</span><span>Customers</span>
        </a>
        <a class="nav-item" href="<?= site_url('products') ?>">
            <span class="nav-icon">□</span><span>Products</span>
        </a>
        <a class="nav-item" href="<?= site_url('suppliers') ?>">
            <span class="nav-icon">⌂</span><span>Suppliers</span>
        </a>
        <a class="nav-item" href="<?= site_url('employees') ?>">
            <span class="nav-icon">♙</span><span>Employees</span>
        </a>

        <div class="nav-section">FINANCE</div>
        <a class="nav-item" href="<?= site_url('finance') ?>">
            <span class="nav-icon">◫</span><span>Finance Dashboard</span>
        </a>
        <a class="nav-item" href="<?= site_url('accounting/accounts') ?>">
            <span class="nav-icon">COA</span><span>Chart of Accounts</span>
        </a>
        <a class="nav-item" href="<?= site_url('accounting/journal') ?>">
            <span class="nav-icon">J</span><span>Journal Entries</span>
        </a>
        <a class="nav-item" href="<?= site_url('accounting/gl') ?>">
            <span class="nav-icon">Σ</span><span>General Ledger</span>
        </a>
        <a class="nav-item" href="<?= site_url('accounting/ar') ?>">
            <span class="nav-icon">AR</span><span>Receivable</span>
        </a>
        <a class="nav-item" href="<?= site_url('accounting/ap') ?>">
            <span class="nav-icon">AP</span><span>Payable</span>
        </a>
        <a class="nav-item" href="<?= site_url('reports') ?>">
            <span class="nav-icon">▥</span><span>Reports</span>
        </a>

        <div class="nav-section">SYSTEM</div>
        <a class="nav-item" href="<?= site_url('settings') ?>">
            <span class="nav-icon">⚙</span><span>Settings</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <div class="sidebar-help">
            <div class="help-icon">?</div>
            <div>
                <strong>Need help?</strong>
                <small>TMS support center</small>
            </div>
        </div>
    </div>
</aside>
