<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" data-auto-dismiss>
        <span class="alert-icon">✓</span>
        <div><?= esc(session()->getFlashdata('success')) ?></div>
        <button type="button" class="alert-close" data-dismiss>×</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" data-auto-dismiss>
        <span class="alert-icon">!</span>
        <div><?= esc(session()->getFlashdata('error')) ?></div>
        <button type="button" class="alert-close" data-dismiss>×</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning" data-auto-dismiss>
        <span class="alert-icon">!</span>
        <div><?= esc(session()->getFlashdata('warning')) ?></div>
        <button type="button" class="alert-close" data-dismiss>×</button>
    </div>
<?php endif; ?>
