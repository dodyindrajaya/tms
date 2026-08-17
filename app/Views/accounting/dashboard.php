<?= $this->extend('layouts/main') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('css/finance-dashboard-v2.css') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
function financeV2Rp($v) { return 'Rp ' . number_format((float)$v, 0, ',', '.'); }
$netFlow = (float)($cashflow['net'] ?? ((float)($cashflow['inflow'] ?? 0) - (float)($cashflow['outflow'] ?? 0)));
$inflow = (float)($cashflow['inflow'] ?? 0);
$outflow = (float)($cashflow['outflow'] ?? 0);
$maxFlow = 1;
foreach ($monthlyCashflow as $m) {
    $maxFlow = max($maxFlow, (float)$m['inflow'], (float)$m['outflow']);
}
?>

<div class="page-header finance-v2-header">
    <div>
        <div class="eyebrow">FINANCE / ACCOUNTING</div>
        <h1>Finance Dashboard</h1>
        <p>Financial position, collection, supplier obligations and cash movement for <?= esc($monthLabel) ?>.</p>
    </div>
    <div class="head-actions">
        <a class="btn btn-secondary" href="<?= site_url('accounting/journal') ?>">Journal Entries</a>
        <a class="btn btn-primary" href="<?= site_url('accounting/gl') ?>">General Ledger</a>
    </div>
</div>

<!-- KPI widgets -->
<div class="finance-v2-kpis">
    <a class="fin-kpi fin-kpi-ar" href="<?= site_url('accounting/ar') ?>">
        <div class="fin-kpi-top"><span>Accounts Receivable</span><b>AR</b></div>
        <strong><?= financeV2Rp($ar) ?></strong>
        <div class="fin-kpi-meta"><span><?= number_format($arCount) ?> open invoice(s)</span><span class="fin-danger"><?= number_format($arOverdueCount) ?> overdue</span></div>
    </a>
    <a class="fin-kpi fin-kpi-ap" href="<?= site_url('accounting/ap') ?>">
        <div class="fin-kpi-top"><span>Accounts Payable</span><b>AP</b></div>
        <strong><?= financeV2Rp($ap) ?></strong>
        <div class="fin-kpi-meta"><span><?= number_format($apCount) ?> open bill(s)</span><span class="fin-danger"><?= number_format($apOverdueCount) ?> overdue</span></div>
    </a>
    <div class="fin-kpi fin-kpi-cash">
        <div class="fin-kpi-top"><span>Cash</span><b>₣</b></div>
        <strong><?= financeV2Rp($cashBalance) ?></strong>
        <small>1100 · posted journal balance</small>
    </div>
    <div class="fin-kpi fin-kpi-bank">
        <div class="fin-kpi-top"><span>Bank</span><b>BK</b></div>
        <strong><?= financeV2Rp($bankBalance) ?></strong>
        <small>1200 · posted journal balance</small>
    </div>
    <div class="fin-kpi fin-kpi-net">
        <div class="fin-kpi-top"><span>Net Liquidity</span><b>NL</b></div>
        <strong><?= financeV2Rp($netLiquidity) ?></strong>
        <small>Cash + Bank + AR − AP</small>
    </div>
</div>

<div class="finance-v2-grid finance-v2-main-grid">
    <!-- Cashflow -->
    <section class="card fin-panel fin-cashflow-panel">
        <div class="fin-panel-head">
            <div>
                <h2>Cashflow</h2>
                <p>Cash and bank movement for <?= esc($monthLabel) ?> and the last six months.</p>
            </div>
            <span class="fin-net-pill <?= $netFlow >= 0 ? 'positive' : 'negative' ?>">
                <?= $netFlow >= 0 ? '+' : '' ?><?= financeV2Rp($netFlow) ?> net
            </span>
        </div>
        <div class="fin-flow-kpis">
            <div><span>Money In</span><strong class="fin-positive">+ <?= financeV2Rp($inflow) ?></strong></div>
            <div><span>Money Out</span><strong class="fin-negative">− <?= financeV2Rp($outflow) ?></strong></div>
            <div><span>Liquid Balance</span><strong><?= financeV2Rp($liquidBalance) ?></strong></div>
        </div>
        <div class="fin-chart-wrap">
            <?php foreach ($monthlyCashflow as $m):
                $mi = (float)$m['inflow'];
                $mo = (float)$m['outflow'];
                $hi = max(3, (int)round(($mi / $maxFlow) * 100));
                $ho = max(3, (int)round(($mo / $maxFlow) * 100));
                $label = date('M', strtotime($m['month'].'-01'));
            ?>
                <div class="fin-chart-col">
                    <div class="fin-bars">
                        <i class="fin-bar-in" style="height:<?= $hi ?>%" title="In <?= esc(financeV2Rp($mi)) ?>"></i>
                        <i class="fin-bar-out" style="height:<?= $ho ?>%" title="Out <?= esc(financeV2Rp($mo)) ?>"></i>
                    </div>
                    <small><?= esc($label) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="fin-legend"><span><i class="legend-in"></i> Inflow</span><span><i class="legend-out"></i> Outflow</span></div>
    </section>

    <!-- Current month journal -->
    <section class="card fin-panel fin-journal-panel">
        <div class="fin-panel-head">
            <div><h2>Journal — <?= esc($monthLabel) ?></h2><p>Posted accounting entries this month.</p></div>
            <a class="text-link" href="<?= site_url('accounting/journal') ?>">View all →</a>
        </div>
        <div class="fin-journal-kpis">
            <div><span>Entries</span><strong><?= number_format((int)$monthJournal['entries']) ?></strong></div>
            <div><span>Debit</span><strong><?= financeV2Rp($monthJournal['debit']) ?></strong></div>
            <div><span>Credit</span><strong><?= financeV2Rp($monthJournal['credit']) ?></strong></div>
        </div>
        <div class="fin-recent-list">
            <?php foreach ($recent as $r): ?>
                <a class="fin-recent-row" href="<?= site_url('accounting/journal/'.$r['id']) ?>">
                    <div class="fin-date-chip"><?= esc(date('d M', strtotime($r['entry_date']))) ?></div>
                    <div class="fin-recent-main">
                        <strong><?= esc($r['entry_no']) ?></strong>
                        <small><?= esc($r['description'] ?: $r['journal_code']) ?></small>
                    </div>
                    <div class="fin-recent-amount"><?= financeV2Rp($r['debit']) ?></div>
                </a>
            <?php endforeach; ?>
            <?php if (!$recent): ?><div class="fin-empty">No posted journals yet.</div><?php endif; ?>
        </div>
    </section>
</div>

<div class="finance-v2-grid finance-v2-secondary-grid">
    <!-- AR -->
    <section class="card fin-panel fin-list-panel">
        <div class="fin-panel-head">
            <div><h2>Receivable</h2><p>Customer balances that still need collection.</p></div>
            <a class="text-link" href="<?= site_url('accounting/ar') ?>">View AR →</a>
        </div>
        <div class="fin-aging-strip">
            <div><span>Outstanding</span><strong><?= financeV2Rp($ar) ?></strong></div>
            <div><span>Overdue</span><strong class="fin-negative"><?= financeV2Rp($arOverdueAmount) ?></strong></div>
        </div>
        <div class="fin-doc-list">
            <?php foreach ($arItems as $r): ?>
                <?php $isOverdue = !empty($r['due_date']) && $r['due_date'] < date('Y-m-d'); ?>
                <div class="fin-doc-row">
                    <div><strong><?= esc($r['invoice_no']) ?></strong><small><?= esc($r['customer_name'] ?: '-') ?> · Due <?= esc($r['due_date'] ?: '-') ?></small></div>
                    <div class="fin-doc-right"><b><?= financeV2Rp($r['outstanding_amount']) ?></b><?php if ($isOverdue): ?><span class="fin-mini-badge danger">OVERDUE</span><?php endif; ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (!$arItems): ?><div class="fin-empty">No outstanding receivable.</div><?php endif; ?>
        </div>
    </section>

    <!-- AP -->
    <section class="card fin-panel fin-list-panel">
        <div class="fin-panel-head">
            <div><h2>Payable</h2><p>Supplier obligations that still need payment.</p></div>
            <a class="text-link" href="<?= site_url('accounting/ap') ?>">View AP →</a>
        </div>
        <div class="fin-aging-strip">
            <div><span>Outstanding</span><strong><?= financeV2Rp($ap) ?></strong></div>
            <div><span>Overdue</span><strong class="fin-negative"><?= financeV2Rp($apOverdueAmount) ?></strong></div>
        </div>
        <div class="fin-doc-list">
            <?php foreach ($apItems as $r): ?>
                <?php $isOverdue = !empty($r['due_date']) && $r['due_date'] < date('Y-m-d'); ?>
                <div class="fin-doc-row">
                    <div><strong><?= esc($r['bill_no']) ?></strong><small><?= esc($r['supplier_name'] ?: '-') ?> · Due <?= esc($r['due_date'] ?: '-') ?></small></div>
                    <div class="fin-doc-right"><b><?= financeV2Rp($r['outstanding_amount']) ?></b><?php if ($isOverdue): ?><span class="fin-mini-badge danger">OVERDUE</span><?php endif; ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (!$apItems): ?><div class="fin-empty">No outstanding payable.</div><?php endif; ?>
        </div>
    </section>
</div>

<div class="finance-v2-note">
    <div class="fin-note-icon">i</div>
    <div><strong>Accounting basis</strong><span>Cash and Bank are calculated from posted journal lines for accounts 1100 and 1200. AR/AP are calculated from open documents with an outstanding balance. No opening-balance mechanism is added by this dashboard.</span></div>
</div>

<?= $this->endSection() ?>
