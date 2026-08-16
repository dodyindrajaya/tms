<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Receive Customer Payment</h1>
<form method="post" action="<?= site_url('payments/store') ?>">
<label>Booking *</label><select name="booking_id" required><?php foreach($bookings as $b): ?><option value="<?= $b['id'] ?>"><?= esc($b['booking_no'].' - Rp '.number_format($b['outstanding_amount'],2)) ?></option><?php endforeach ?></select>
<label>Payment Date</label><input type="date" name="payment_date" value="<?= date('Y-m-d') ?>">
<label>Amount *</label><input type="number" step="0.01" name="amount" required>
<label>Reference</label><input name="reference_no" placeholder="Transfer ref">
<label>Notes</label><textarea name="notes"></textarea>
<button type="submit">Save & Post Payment</button>
</form>
<?= $this->endSection() ?>
