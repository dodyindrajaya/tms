<?php
$product = $product ?? [];
$isEdit = !empty($product['id']);

$value = static function (string $field, $default = '') use ($product) {
    return old($field, $product[$field] ?? $default);
};
?>

<div class="card form-card">
    <div class="form-section-title">Basic Information</div>

    <div class="form-grid">
        <div class="form-group">
            <label for="product_code">Product Code *</label>
            <input type="text" id="product_code" name="product_code"
                   value="<?= esc($value('product_code')) ?>"
                   maxlength="50" required
                   placeholder="e.g. FLT-GA-JKT-DPS">
        </div>

        <div class="form-group">
            <label for="category">Product Category *</label>
            <select id="category" name="category" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= esc($key) ?>"
                        <?= $value('category') === $key ? 'selected' : '' ?>>
                        <?= esc($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group full">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name"
                   value="<?= esc($value('name')) ?>"
                   maxlength="190" required
                   placeholder="e.g. Garuda Indonesia Jakarta - Denpasar">
        </div>

        <div class="form-group">
            <label for="unit">Unit *</label>
            <input type="text" id="unit" name="unit"
                   value="<?= esc($value('unit', 'pcs')) ?>"
                   maxlength="30" required
                   placeholder="pcs, pax, night, package...">
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1"
                    <?= $value('is_active', 1) ? 'checked' : '' ?>>
                Active product
            </label>
        </div>
    </div>

    <div class="form-section-title">Pricing</div>

    <div class="form-grid">
        <div class="form-group">
            <label for="default_sale_price">Default Sale Price *</label>
            <input type="number" id="default_sale_price" name="default_sale_price"
                   value="<?= esc($value('default_sale_price', 0)) ?>"
                   min="0" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="default_cost_price">Default Cost Price *</label>
            <input type="number" id="default_cost_price" name="default_cost_price"
                   value="<?= esc($value('default_cost_price', 0)) ?>"
                   min="0" step="0.01" required>
        </div>
    </div>

    <div class="form-section-title">Accounting Mapping <span class="section-hint">optional for MVP</span></div>

    <div class="form-grid">
        <div class="form-group">
            <label for="revenue_account_id">Revenue Account ID</label>
            <input type="number" id="revenue_account_id" name="revenue_account_id"
                   value="<?= esc($value('revenue_account_id')) ?>"
                   min="1" placeholder="e.g. 4100">
            <div class="field-help">Can be connected to Chart of Accounts later.</div>
        </div>

        <div class="form-group">
            <label for="cost_account_id">Cost Account ID</label>
            <input type="number" id="cost_account_id" name="cost_account_id"
                   value="<?= esc($value('cost_account_id')) ?>"
                   min="1" placeholder="e.g. 5100">
            <div class="field-help">Can be connected to Chart of Accounts later.</div>
        </div>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="<?= site_url('products') ?>">Cancel</a>
        <button class="btn btn-primary" type="submit">
            <?= $isEdit ? 'Save Changes' : 'Create Product' ?>
        </button>
    </div>
</div>
