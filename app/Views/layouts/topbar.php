<header class="tms-topbar">
    <div class="topbar-left">
        <button class="icon-button mobile-menu" type="button" data-sidebar-toggle aria-label="Toggle menu">☰</button>
        <div class="breadcrumb">
            <span class="breadcrumb-muted">TMS</span>
            <span>/</span>
            <strong><?= esc($title ?? 'Dashboard') ?></strong>
        </div>
    </div>

    <div class="topbar-actions">
        <a class="quick-action" href="<?= site_url('bookings/create') ?>">
            <span>＋</span> New Booking
        </a>
        <button class="icon-button" type="button" title="Notifications">♢</button>
        <button class="user-menu" type="button">
            <span class="avatar">A</span>
            <span class="user-meta">
                <strong>Administrator</strong>
                <small>System Admin</small>
            </span>
            <span>⌄</span>
        </button>
    </div>
</header>
