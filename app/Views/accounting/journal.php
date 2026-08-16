<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Journal Entries</h1><table><tr><th>Date</th><th>Entry</th><th>Journal</th><th>Reference</th><th>Status</th><th>Description</th></tr>
<?php foreach($entries as $e): ?><tr><td><?= esc($e['entry_date']) ?></td><td><?= esc($e['entry_no']) ?></td><td><?= esc($e['journal_code']) ?></td><td><?= esc($e['reference_type'].' #'.$e['reference_id']) ?></td><td><?= esc($e['status']) ?></td><td><?= esc($e['description']) ?></td></tr><?php endforeach ?>
</table><?= $this->endSection() ?>
