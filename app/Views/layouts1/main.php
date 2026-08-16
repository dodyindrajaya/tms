<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= esc($title ?? 'TMS v1') ?></title>
<style>
body{font-family:Arial,sans-serif;margin:0;background:#f5f6f8;color:#222}
nav{background:#172033;color:white;padding:14px 22px} nav a{color:white;margin-right:18px;text-decoration:none}
main{max-width:1200px;margin:25px auto;padding:0 18px}
.card{background:white;border-radius:8px;padding:18px;margin-bottom:18px;box-shadow:0 1px 4px #ddd}
table{width:100%;border-collapse:collapse;background:white}th,td{padding:9px;border-bottom:1px solid #eee;text-align:left}
input,select,textarea{width:100%;padding:8px;box-sizing:border-box;margin:5px 0 12px}
button,.btn{display:inline-block;background:#172033;color:#fff;padding:9px 14px;border:0;border-radius:5px;text-decoration:none;cursor:pointer}
.success{background:#e8f7e8;padding:10px;margin-bottom:15px}.error{background:#ffe8e8;padding:10px;margin-bottom:15px}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:15px}
.right{text-align:right}.money{text-align:right}
</style>
</head>
<body>
<nav>
<strong>TMS v1</strong>
<a href="<?= site_url('/') ?>">Dashboard</a>
<a href="<?= site_url('customers') ?>">Customers</a>
<a href="<?= site_url('bookings') ?>">Bookings</a>
<a href="<?= site_url('invoices') ?>">Invoices</a>
<a href="<?= site_url('payments') ?>">Payments</a>
<a href="<?= site_url('accounting/journal') ?>">Journal</a>
<a href="<?= site_url('accounting/gl') ?>">GL</a>
</nav>
<main>
<?php if (session()->getFlashdata('success')): ?><div class="success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif ?>
<?php if (session()->getFlashdata('error')): ?><div class="error"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<?= $this->renderSection('content') ?>
</main>
</body>
</html>
