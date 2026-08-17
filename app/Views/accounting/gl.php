<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?><link rel="stylesheet" href="<?= base_url('css/finance.css') ?>"><?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="page-header"><div><div class="eyebrow">FINANCE / ACCOUNTING</div><h1>General Ledger</h1><p>Posted journal lines across the Chart of Accounts.</p></div></div>
<div class="card finance-table-card"><div class="table-wrap"><table><thead><tr><th>Date</th><th>Entry</th><th>Account</th><th>Description</th><th>Debit</th><th>Credit</th></tr></thead><tbody><?php if(empty($lines)): ?><tr><td colspan="6" class="empty-cell">No posted journal lines yet.</td></tr><?php endif ?><?php foreach($lines as $l): ?><tr><td><?= esc($l['entry_date']) ?></td><td><strong><?= esc($l['entry_no']) ?></strong></td><td><strong><?= esc($l['account_code'].' - '.$l['account_name']) ?></strong></td><td><?= esc($l['description'] ?: $l['entry_description']) ?></td><td class="money">Rp <?= number_format((float)$l['debit'],0,',','.') ?></td><td class="money">Rp <?= number_format((float)$l['credit'],0,',','.') ?></td></tr><?php endforeach ?></tbody></table></div></div>
<?= $this->endSection() ?>
