<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Payments</h1><p><a class="btn" href="<?= site_url('payments/create') ?>">+ Receive Payment</a></p>
<table><tr><th>Payment</th><th>Date</th><th>Booking</th><th>Customer</th><th>Amount</th><th>Reference</th></tr>
<?php foreach($payments as $p): ?><tr><td><?= esc($p['payment_no']) ?></td><td><?= esc($p['payment_date']) ?></td><td><?= esc($p['booking_no']) ?></td><td><?= esc($p['customer_name']) ?></td><td class="money"><?= number_format($p['amount'],2) ?></td><td><?= esc($p['reference_no']) ?></td></tr><?php endforeach ?>
</table><?= $this->endSection() ?>
