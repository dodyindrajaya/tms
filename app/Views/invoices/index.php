<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Invoices</h1><table><tr><th>Invoice</th><th>Booking</th><th>Customer</th><th>Status</th><th>Total</th><th>Outstanding</th></tr>
<?php foreach($invoices as $i): ?><tr><td><a href="<?= site_url('invoices/'.$i['id']) ?>"><?= esc($i['invoice_no']) ?></a></td><td><?= esc($i['booking_no']) ?></td><td><?= esc($i['customer_name']) ?></td><td><?= esc($i['status']) ?></td><td class="money"><?= number_format($i['total_amount'],2) ?></td><td class="money"><?= number_format($i['outstanding_amount'],2) ?></td></tr><?php endforeach ?>
</table><?= $this->endSection() ?>
