<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= view('components/page_header', [
    'eyebrow' => 'Master Data',
    'title' => 'Products',
    'subtitle' => 'Manage travel products, prices and product categories.',
    'action' => [
        'label' => 'New Product',
        'url' => site_url('products/create')
    ]
]) ?>

<div class="stat-grid">
    <?= view('components/stat_card', [
        'label' => 'Product Records',
        'value' => number_format(count($products)),
        'icon' => '▣',
        'meta' => 'Showing current page'
    ]) ?>

    <?= view('components/stat_card', [
        'label' => 'Category',
        'value' => $category !== '' ? esc($categories[$category] ?? $category) : 'All',
        'icon' => '◆',
        'meta' => $category !== '' ? 'Filtered category' : 'No category filter'
    ]) ?>

    <?= view('components/stat_card', [
        'label' => 'Status',
        'value' => $status !== '' ? ucfirst($status) : 'All',
        'icon' => '●',
        'meta' => $status !== '' ? 'Filtered status' : 'Active + inactive'
    ]) ?>
</div>

<?= view('components/search_bar', [
    'action' => site_url('products'),
    'placeholder' => 'Search product code, name or unit...',
    'value' => $q,
    'filters' => [
        [
            'name' => 'category',
            'label' => 'All Categories',
            'value' => $category,
            'options' => $categories,
        ],
        [
            'name' => 'status',
            'label' => 'All Status',
            'value' => $status,
            'options' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
        ],
    ],
]) ?>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($products)): ?>
            <?= view('components/empty_state', [
                'title' => 'No products found',
                'message' => 'Create your first product or change the search/filter.',
                'action' => [
                    'label' => 'New Product',
                    'url' => site_url('products/create')
                ]
            ]) ?>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Sale Price</th>
                        <th>Cost Price</th>
                        <th>Margin</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <?php
                    $sale = (float) ($product['default_sale_price'] ?? 0);
                    $cost = (float) ($product['default_cost_price'] ?? 0);
                    $margin = $sale - $cost;
                    ?>
                    <tr>
                        <td><strong><?= esc($product['product_code']) ?></strong></td>
                        <td><strong><?= esc($product['name']) ?></strong></td>
                        <td><?= esc($categories[$product['category']] ?? ucfirst($product['category'])) ?></td>
                        <td><?= esc($product['unit']) ?></td>
                        <td><?= number_format($sale, 0, ',', '.') ?></td>
                        <td><?= number_format($cost, 0, ',', '.') ?></td>
                        <td>
                            <strong><?= number_format($margin, 0, ',', '.') ?></strong>
                            <?php if ($sale > 0): ?>
                                <div class="muted-text"><?= number_format(($margin / $sale) * 100, 1) ?>%</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= view('components/status_badge', [
                                'status' => !empty($product['is_active']) ? 'active' : 'inactive'
                            ]) ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-secondary" href="<?= site_url('products/edit/' . $product['id']) ?>">Edit</a>
                                <form method="post"
                                      action="<?= site_url('products/delete/' . $product['id']) ?>"
                                      onsubmit="return confirm('Delete this product?');"
                                      style="display:inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($pager->getPageCount() > 1): ?>
                <div class="pagination-wrap">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
