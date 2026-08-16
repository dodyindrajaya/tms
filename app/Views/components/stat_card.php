<div class="stat-card">
    <div class="stat-top">
        <div>
            <div class="stat-label"><?= esc($label ?? '') ?></div>
            <div class="stat-value"><?= esc($value ?? '0') ?></div>
        </div>
        <div class="stat-icon"><?= $icon ?? '•' ?></div>
    </div>

    <?php if (isset($meta)): ?>
        <div class="stat-meta"><?= esc($meta) ?></div>
    <?php endif; ?>
</div>
