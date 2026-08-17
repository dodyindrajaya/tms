<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('css/finance.css') ?>">
<style>
.ap-page .page-header{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;margin-bottom:18px}
.ap-page .page-header h1{margin:3px 0 5px}
.ap-page .page-header p{margin:0;color:var(--muted)}
.ap-page .header-total{min-width:230px;padding:16px 20px;border:1px solid var(--border);border-radius:12px;background:#fff;text-align:right;box-shadow:0 4px 14px rgba(15,45,60,.05)}
.ap-page .header-total span{display:block;font-size:12px;color:var(--muted);margin-bottom:5px}
.ap-page .header-total strong{font-size:22px;color:var(--text);font-variant-numeric:tabular-nums}
.ap-page .finance-table-card{overflow:hidden}
.ap-page .table-wrap{overflow:auto}
.ap-page table{min-width:1000px}
.ap-page .money{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums}
.ap-page .badge{display:inline-flex;align-items:center;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:700}
.ap-page .badge-warning{background:#fff4d6;color:#8a6200}
.ap-page .badge-danger{background:#fde7e7;color:#b4232d}
@media(max-width:800px){.ap-page .page-header{align-items:stretch;flex-direction:column}.ap-page .header-total{text-align:left}}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ap-page">
    <div class="page-header">
        <div>
            <div class="eyebrow">FINANCE / AP</div>
            <h1>Payable</h1>
            <p>Open supplier bills based on posted accounting transactions.</p>
        </div>
        <div class="header-total">
            <span>Total Outstanding</span>
            <strong>Rp <?= number_format((float) ($total ?? 0), 0, ',', '.') ?></strong>
        </div>
    </div>

    <div class="card finance-table-card">
        <div class="section-head">
            <div>
                <h2>Open Payables</h2>
                <p><?= count(is_array($rows ?? null) ? $rows : []) ?> bill(s)</p>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Bill</th><th>Supplier</th><th>Booking</th><th>Due Date</th>
                        <th>Status</th><th>Total</th><th>Paid</th><th>Outstanding</th>
                    </tr>
                </thead>
                <tbody>
                <?php $rows = is_array($rows ?? null) ? $rows : []; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="8" class="empty-cell">No outstanding payables.</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                        <?php $status = (string) ($r['status'] ?? 'posted'); ?>
                        <tr>
                            <td><strong><?= esc($r['bill_no'] ?? '-') ?></strong></td>
                            <td><?= esc($r['supplier_name'] ?? '-') ?><br><small><?= esc($r['supplier_code'] ?? '') ?></small></td>
                            <td><?= esc($r['booking_no'] ?? '-') ?></td>
                            <td><?= esc($r['due_date'] ?? '-') ?></td>
                            <td><span class="badge <?= $status === 'overdue' ? 'badge-danger' : 'badge-warning' ?>"><?= esc(ucfirst($status)) ?></span></td>
                            <td class="money">Rp <?= number_format((float) ($r['total_amount'] ?? 0), 0, ',', '.') ?></td>
                            <td class="money">Rp <?= number_format((float) ($r['paid_amount'] ?? 0), 0, ',', '.') ?></td>
                            <td class="money"><strong>Rp <?= number_format((float) ($r['outstanding_amount'] ?? 0), 0, ',', '.') ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
