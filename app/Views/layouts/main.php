<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'TMS') ?> | Travel Management System</title>
    <link rel="stylesheet" href="<?= base_url('css/tms.css') ?>">
    <?= $this->renderSection('head') ?>
</head>
<body>
<div class="tms-app">
    <?= view('layouts/sidebar') ?>

    <div class="tms-shell">
        <?= view('layouts/topbar') ?>

        <main class="tms-main">
            <?= view('layouts/flash') ?>
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="tms-footer">
            <span>TMS v1.0</span>
            <span><?= date('Y') ?> Travel Management System</span>
        </footer>
    </div>
</div>

<script src="<?= base_url('js/tms.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
