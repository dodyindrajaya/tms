<div class="page-header">
    <div>
        <div class="eyebrow"><?= esc($eyebrow ?? '') ?></div>
        <h1><?= esc($title ?? '') ?></h1>
        <?php if (!empty($subtitle)): ?>
            <p><?= esc($subtitle) ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($action)): ?>
        <div class="page-header-actions">
            <a class="btn btn-primary" href="<?= esc($action['url']) ?>">
                <span>＋</span><?= esc($action['label']) ?>
            </a>
        </div>
    <?php endif; ?>
</div>
