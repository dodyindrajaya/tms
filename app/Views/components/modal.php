<div class="modal-backdrop" data-modal-backdrop id="<?= esc($id ?? 'tmsModal') ?>" hidden>
    <div class="modal" role="dialog" aria-modal="true">
        <div class="modal-header">
            <h3><?= esc($title ?? 'Confirmation') ?></h3>
            <button type="button" class="icon-button" data-modal-close>×</button>
        </div>
        <div class="modal-body">
            <?= $body ?? '' ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
            <?php if (!empty($confirmText)): ?>
                <button type="button" class="btn btn-primary" data-modal-confirm><?= esc($confirmText) ?></button>
            <?php endif; ?>
        </div>
    </div>
</div>
