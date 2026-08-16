<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>General Ledger</h1><table><tr><th>Date</th><th>Entry</th><th>Account</th><th>Description</th><th>Debit</th><th>Credit</th></tr>
<?php foreach($lines as $l): ?><tr><td><?= esc($l['entry_date']) ?></td><td><?= esc($l['entry_no']) ?></td><td><?= esc($l['account_code'].' - '.$l['account_name']) ?></td><td><?= esc($l['description']) ?></td><td class="money"><?= number_format($l['debit'],2) ?></td><td class="money"><?= number_format($l['credit'],2) ?></td></tr><?php endforeach ?>
</table><?= $this->endSection() ?>
