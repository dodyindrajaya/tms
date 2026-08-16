<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= view('components/page_header', [
    'eyebrow' => 'Master Data / Products',
    'title' => 'Edit Product',
    'subtitle' => 'Update product information and default pricing.'
]) ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">
        <span class="alert-icon">!</span>
        <div>
            <strong>Please fix the following:</strong>
            <ul class="form-errors">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<form method="post" action="<?= site_url('products/update/' . $product['id']) ?>">
    <?= csrf_field() ?>
    <?= view('products/_form', ['product' => $product, 'categories' => $categories]) ?>
</form>

<?= $this->endSection() ?>
