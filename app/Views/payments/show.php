<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= view('components/page_header', [
    'eyebrow' => 'FINANCE / PAYMENT',
    'title' => $payment['payment_no'],
    'subtitle' => 'Payment detail and accounting journal.',
    'action' => ['label' => 'Back to Payments', 'url' => site_url('payments')]
]) ?>

<div class="card">
    <div class="detail-info-grid">
        <div><span>Booking</span><strong><?= esc($payment['booking_no'] ?? '-') ?></strong></div>
        <div><span>Customer</span><strong><?= esc($payment['customer_name'] ?? '-') ?></strong></div>
        <div><span>Date</span><strong><?= esc(substr($payment['payment_date'],0,10)) ?></strong></div>
        <div><span>Method</span><strong><?= esc($payment['payment_method_name'] ?? '-') ?></strong></div>
        <div><span>Account</span><strong><?= esc(($payment['account_code'] ?? '-') . ' - ' . ($payment['account_name'] ?? '')) ?></strong></div>
        <div><span>Reference</span><strong><?= esc($payment['reference_no'] ?? '-') ?></strong></div>
        <div><span>Amount</span><strong>Rp <?= number_format((float)$payment['amount'],0,',','.') ?></strong></div>
        <div><span>Journal</span><strong><?= esc($payment['entry_no'] ?? '-') ?> (<?= esc($payment['journal_status'] ?? '-') ?>)</strong></div>
    </div>
</div>

<div class="card">
    <div class="detail-card-header"><div><h2>Journal Entry</h2><p>Customer receipt: debit cash/bank and credit receivable.</p></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Account</th><th>Description</th><th class="text-right">Debit</th><th class="text-right">Credit</th></tr></thead>
            <tbody>
            <?php foreach($lines as $line): ?>
                <tr>
                    <td><strong><?= esc($line['account_code'].' - '.$line['account_name']) ?></strong></td>
                    <td><?= esc($line['description']) ?></td>
                    <td class="text-right">Rp <?= number_format((float)$line['debit'],0,',','.') ?></td>
                    <td class="text-right">Rp <?= number_format((float)$line['credit'],0,',','.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
