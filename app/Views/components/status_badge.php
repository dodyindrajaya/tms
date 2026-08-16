<?php
$status = strtolower((string) ($status ?? ''));
$map = [
    'active' => ['class' => 'badge-success', 'label' => 'Active'],
    'inactive' => ['class' => 'badge-muted', 'label' => 'Inactive'],
    'confirmed' => ['class' => 'badge-success', 'label' => 'Confirmed'],
    'pending' => ['class' => 'badge-warning', 'label' => 'Pending'],
    'cancelled' => ['class' => 'badge-danger', 'label' => 'Cancelled'],
    'paid' => ['class' => 'badge-success', 'label' => 'Paid'],
    'partial' => ['class' => 'badge-warning', 'label' => 'Partial'],
    'unpaid' => ['class' => 'badge-danger', 'label' => 'Unpaid'],
];
$item = $map[$status] ?? ['class' => 'badge-info', 'label' => ucfirst($status ?: 'Unknown')];
?>
<span class="badge <?= esc($item['class']) ?>"><?= esc($item['label']) ?></span>
