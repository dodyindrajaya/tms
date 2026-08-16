<div class="empty-state">
    <div class="empty-icon"><?= $icon ?? '□' ?></div>
    <h3><?= esc($title ?? 'No data found') ?></h3>
    <p><?= esc($message ?? 'There is no data to display yet.') ?></p>
    <?php if (!empty($action)): ?>
        <a class="btn btn-primary" href="<?= esc($action['url']) ?>">
            ＋ <?= esc($action['label']) ?>
        </a>
    <?php endif; ?>
</div>
