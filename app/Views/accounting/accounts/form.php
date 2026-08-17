<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('css/chart-of-accounts.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$edit = ($mode ?? 'create') === 'edit';
$account = is_array($account ?? null) ? $account : [];

// Normalize option lists defensively. Some older controllers/providers may return
// associative maps (id => label) instead of arrays of database rows. The form
// must never assume that every item is an array, otherwise PHP 8 raises:
// "Cannot access offset of type string on string".
$normalizeOptions = static function ($items, string $kind): array {
    if (!is_array($items)) {
        return [];
    }

    $out = [];
    foreach ($items as $key => $item) {
        if (is_array($item)) {
            $id = $item['id'] ?? $key;
            if ($id === null || $id === '') {
                continue;
            }
            $code = (string) ($item['code'] ?? '');
            $name = (string) ($item['name'] ?? ($item['label'] ?? ''));
            if ($name === '' && is_string($item['value'] ?? null)) {
                $name = $item['value'];
            }
            $out[] = [
                'id' => $id,
                'code' => $code,
                'name' => $name,
            ];
            continue;
        }

        // Handle legacy associative option maps such as [1 => 'Assets'].
        if (is_scalar($item) && (is_int($key) || ctype_digit((string) $key))) {
            $out[] = [
                'id' => $key,
                'code' => '',
                'name' => (string) $item,
            ];
        }
    }

    return $out;
};

$groups = $normalizeOptions($groups ?? [], 'group');
$parents = $normalizeOptions($parents ?? [], 'parent');
?>
<div class="page-header">
    <div>
        <div class="eyebrow">FINANCE / ACCOUNTING</div>
        <h1><?= $edit ? 'Edit Account' : 'New Account' ?></h1>
        <p><?= $edit ? 'Update this account without changing its accounting identity.' : 'Create an account for journals and financial reports.' ?></p>
    </div>
    <div class="page-header-actions">
        <a class="btn btn-secondary" href="<?= site_url('accounting/accounts') ?>">Back</a>
    </div>
</div>

<div class="card coa-form-card">
    <?php if ($errors = session('errors')): ?>
        <div class="alert alert-danger"><div class="alert-icon">!</div><div>Please correct the highlighted account information.</div></div>
    <?php endif; ?>

    <div class="coa-form-section">
        <div><h2>Account Information</h2><p>Basic identity and reporting classification.</p></div>
    </div>

    <form method="post" action="<?= $edit ? site_url('accounting/accounts/update/'.$account['id']) : site_url('accounting/accounts/store') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="form-group">
                <label>Account Code *</label>
                <input name="code" required maxlength="20" value="<?= esc(old('code', $account['code'] ?? '')) ?>" placeholder="e.g. 1100">
                <small class="field-help">Use a unique code that fits your existing numbering structure.</small>
            </div>
            <div class="form-group">
                <label>Account Name *</label>
                <input name="name" required maxlength="190" value="<?= esc(old('name', $account['name'] ?? '')) ?>" placeholder="e.g. Cash">
            </div>
            <div class="form-group">
                <label>Account Type *</label>
                <select name="account_type" required>
                    <option value="">-- Select Type --</option>
                    <?php foreach (($accountTypes ?? []) as $k => $label): ?>
                        <option value="<?= esc($k) ?>" <?= old('account_type', $account['account_type'] ?? '') === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Account Group</label>
                <select name="account_group_id">
                    <option value="">-- No Group --</option>
                    <?php foreach (($groups ?? []) as $p): ?>
                        <option value="<?= esc((string) ($p['id'] ?? '')) ?>" <?= (string)old('account_group_id', $account['account_group_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>><?= esc(trim((string)($p['code'] ?? '') . ((($p['code'] ?? '') !== '' && ($p['name'] ?? '') !== '') ? ' — ' : '') . (string)($p['name'] ?? ''))) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-help">Used for financial report grouping.</small>
            </div>
            <div class="form-group full">
                <label>Parent Account</label>
                <select name="parent_id">
                    <option value="">-- No Parent --</option>
                    <?php foreach (($parents ?? []) as $p): ?>
                        <option value="<?= esc((string) ($p['id'] ?? '')) ?>" <?= (string)old('parent_id', $account['parent_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>><?= esc(trim((string)($p['code'] ?? '') . ((($p['code'] ?? '') !== '' && ($p['name'] ?? '') !== '') ? ' — ' : '') . (string)($p['name'] ?? ''))) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="field-help">Optional hierarchy. Parent and child accounts must use the same account type.</small>
            </div>
        </div>

        <div class="coa-form-section coa-form-section-spaced">
            <div><h2>Account Options</h2><p>Control how the account behaves in transactions.</p></div>
        </div>
        <div class="coa-options-grid">
            <label class="coa-option"><input type="checkbox" name="is_control_account" value="1" <?= old('is_control_account', $account['is_control_account'] ?? 0) ? 'checked' : '' ?>><span><strong>Control account</strong><small>Used by system-generated accounting transactions.</small></span></label>
            <label class="coa-option"><input type="checkbox" name="allow_manual_posting" value="1" <?= old('allow_manual_posting', $account['allow_manual_posting'] ?? 1) ? 'checked' : '' ?>><span><strong>Allow manual posting</strong><small>Permit direct journal entries to this account.</small></span></label>
            <label class="coa-option"><input type="checkbox" name="is_active" value="1" <?= old('is_active', $account['is_active'] ?? 1) ? 'checked' : '' ?>><span><strong>Active</strong><small>Inactive accounts remain visible but cannot be selected for new use.</small></span></label>
        </div>

        <div class="form-actions">
            <a class="btn btn-secondary" href="<?= site_url('accounting/accounts') ?>">Cancel</a>
            <button class="btn btn-primary" type="submit"><?= $edit ? 'Save Changes' : 'Create Account' ?></button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
