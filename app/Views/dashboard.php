<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>TMS v1 Dashboard</h1>
<?php
function formatRp($v, $dec = 0) {
	return 'Rp ' . number_format((float)$v, $dec, ',', '.');
}
?>

<div class="grid stats-grid">
	<div class="card stat-card">
		<h4>Bookings</h4>
		<div class="stat-number"><?= number_format($totalBookings) ?></div>
	</div>
	<div class="card stat-card">
		<h4>Customers</h4>
		<div class="stat-number"><?= number_format($totalCustomers) ?></div>
	</div>
	<div class="card stat-card">
		<h4>Total Invoiced</h4>
		<div class="stat-number" title="<?= esc(formatRp($totalInvoiced,2)) ?>"><?= formatRp($totalInvoiced,0) ?></div>
	</div>
	<div class="card stat-card">
		<h4>Revenue (Paid)</h4>
		<div class="stat-number" title="<?= esc(formatRp($totalPaid,2)) ?>"><?= formatRp($totalPaid,0) ?></div>
	</div>
	<div class="card stat-card">
		<h4>Outstanding</h4>
		<div class="stat-number" title="<?= esc(formatRp($outstanding,2)) ?>"><?= formatRp($outstanding,0) ?></div>
	</div>
</div>

<div class="charts-row">
	<div class="card chart-card">
		<h3>Monthly Revenue (last 6 months)</h3>
		<div class="chart-container">
			<canvas id="revenueBar"></canvas>
		</div>
	</div>
	<div class="card chart-card">
		<h3>Invoiced vs Paid</h3>
		<div class="chart-container chart-container-center">
			<canvas id="invoicesPie"></canvas>
		</div>
	</div>
</div>

<style>
	.stats-grid { display:flex; flex-wrap:wrap; gap:12px; }
	.stat-card { flex:1 1 180px; text-align:center; padding:18px; }
	.stat-number { font-size:28px; font-weight:700; margin-top:8px; }
	.charts-row { display:flex; gap:16px; align-items:stretch; margin-top:12px; }
	.chart-card { flex:1; }
	.chart-container { position:relative; width:100%; height:320px; }
	.chart-container-center { display:flex; align-items:center; justify-content:center; }

	@media (max-width:1000px) {
		.chart-container { height:260px; }
	}
	@media (max-width:700px) {
		.charts-row { flex-direction:column; }
		.chart-container { height:220px; }
	}

	@media (max-width:800px) {
		.card.chart-large .chart-container { height:240px; }
		.card.chart-small .chart-container { height:200px; }
		.stats-grid { gap:8px; }
		.stat-number { font-size:22px; }
	}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	const months = <?= json_encode($chartMonths) ?> || [];
	const revenue = <?= json_encode($chartRevenue) ?> || [];

	const barCtx = document.getElementById('revenueBar').getContext('2d');
	new Chart(barCtx, {
		type: 'bar',
		data: {
			labels: months,
			datasets: [{
				label: 'Revenue',
				data: revenue,
				backgroundColor: 'rgba(54,162,235,0.6)'
			}]
		},
		options: {
			responsive:true,
			maintainAspectRatio:false,
			scales: {
				y: {
					ticks: {
						callback: function(value) {
							return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
						}
					}
				}
			},
			plugins: {
				tooltip: {
					callbacks: {
						label: function(context) {
							const v = context.parsed.y ?? context.parsed;
							return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(v);
						}
					}
				}
			}
		}
	});

	const pieCtx = document.getElementById('invoicesPie').getContext('2d');
	const invoiced = <?= json_encode($totalInvoiced) ?> || 0;
	const paid = <?= json_encode($totalPaid) ?> || 0;
	const outstanding = <?= json_encode($outstanding) ?> || 0;
	new Chart(pieCtx, {
		type: 'pie',
		data: {
			labels: ['Paid','Outstanding'],
			datasets: [{ data: [paid, outstanding], backgroundColor: ['#4caf50','#ff9800'] }]
		},
		options: {
			responsive:true,
			maintainAspectRatio:false,
			plugins: {
				tooltip: {
					callbacks: {
						label: function(context) {
							const v = context.parsed || 0;
							return context.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(v);
						}
					}
				}
			}
		}
	});
</script>
<?= $this->endSection() ?>
