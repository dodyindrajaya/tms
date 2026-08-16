<div class="toolbar-card">
    <form method="get" action="<?= esc($action ?? current_url()) ?>" class="search-toolbar">
        <div class="search-input">
            <span>⌕</span>
            <input type="search"
                   name="<?= esc($name ?? 'q') ?>"
                   value="<?= esc($value ?? ($_GET[$name ?? 'q'] ?? '')) ?>"
                   placeholder="<?= esc($placeholder ?? 'Search...') ?>">
        </div>

        <?php if (!empty($filters)): ?>
            <?php foreach ($filters as $filter): ?>
                <select name="<?= esc($filter['name']) ?>" class="filter-select">
                    <option value=""><?= esc($filter['label']) ?></option>
                    <?php foreach ($filter['options'] as $value => $label): ?>
                        <option value="<?= esc($value) ?>"
                            <?= (($filter['value'] ?? '') == $value) ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endforeach; ?>
        <?php endif; ?>

        <button class="btn btn-secondary" type="submit">Search</button>
    </form>
</div>
