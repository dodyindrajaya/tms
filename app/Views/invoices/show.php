<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1><?= esc($invoice['invoice_no']) ?></h1>
<div class="card"><strong>Booking:</strong> <?= esc($invoice['booking_no']) ?><br><strong>Customer:</strong> <?= esc($invoice['customer_name']) ?><br><strong>Status:</strong> <?= esc($invoice['status']) ?><br><strong>Total:</strong> <?= number_format($invoice['total_amount'],2) ?><br><strong>Outstanding:</strong> <?= number_format($invoice['outstanding_amount'],2) ?></div>
<table><tr><th>Description</th><th>Qty</th><th>Unit</th><th>Total</th></tr>
<?php foreach($items as $i): ?><tr><td><?= esc($i['description']) ?></td><td><?= $i['quantity'] ?></td><td><?= number_format($i['unit_price'],2) ?></td><td><?= number_format($i['line_total'],2) ?></td></tr><?php endforeach ?>
</table>
<?php if($invoice['status']==='draft'): ?><p><a class="btn" href="<?= site_url('invoices/'.$invoice['id'].'/post') ?>">Post Invoice & Create Journal</a></p><?php endif ?>
<?= $this->endSection() ?>
