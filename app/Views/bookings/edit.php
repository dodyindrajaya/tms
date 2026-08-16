<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= view('components/page_header', [
    'eyebrow' => 'Transactions / Bookings',
    'title' => 'Edit Booking',
    'subtitle' => 'Update booking and its first commercial line.',
    'action' => [
        'label' => 'View Booking',
        'url' => site_url('bookings/show/' . $booking['id'])
    ]
]) ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">Please check the form.</div>
<?php endif; ?>

<form method="post" action="<?= site_url('bookings/update/' . $booking['id']) ?>">
    <?= csrf_field() ?>

    <?= view('bookings/_form', [
        'booking' => $booking,
        'item' => $item,
        'customers' => $customers,
        'products' => $products,
        'sources' => $sources,
        'statuses' => $statuses
    ]) ?>
</form>

<?= $this->endSection() ?>
