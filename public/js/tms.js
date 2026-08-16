document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('tmsSidebar');

    document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            sidebar?.classList.toggle('open');
        });
    });

    document.querySelectorAll('[data-dismiss]').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.alert')?.remove();
        });
    });

    document.querySelectorAll('[data-auto-dismiss]').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity .25s ease';
            setTimeout(() => alert.remove(), 250);
        }, 5000);
    });

    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('[data-modal-backdrop]')?.setAttribute('hidden', '');
        });
    });

    document.querySelectorAll('[data-modal-backdrop]').forEach(backdrop => {
        backdrop.addEventListener('click', e => {
            if (e.target === backdrop) backdrop.setAttribute('hidden', '');
        });
    });

    // Mark current sidebar link active.
    const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(link => {
        try {
            const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
            if (linkPath === currentPath || (linkPath !== '/' && currentPath.startsWith(linkPath + '/'))) {
                link.classList.add('active');
            }
        } catch (_) {}
    });
});
