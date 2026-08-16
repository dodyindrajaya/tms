<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>New Booking</h1>
<form method="post" action="<?= site_url('bookings/store') ?>">
<label>Customer *</label><select name="customer_id" required><option value="">-- select --</option><?php foreach($customers as $c): ?><option value="<?= $c['id'] ?>"><?= esc($c['customer_code'].' - '.$c['name']) ?></option><?php endforeach ?></select>
<label>Booking Date</label><input type="date" name="booking_date" value="<?= date('Y-m-d') ?>">
<label>Travel Start</label><input type="date" name="travel_start_date">
<label>Travel End</label><input type="date" name="travel_end_date">
<label>Source</label><select name="source"><option>office</option><option>whatsapp</option><option>telegram</option><option>website</option></select>
<label>Product *</label><select name="product_id" required><option value="">-- select --</option><?php foreach($products as $p): ?><option value="<?= $p['id'] ?>"><?= esc($p['product_code'].' - '.$p['name']) ?></option><?php endforeach ?></select>
<label>Description</label><input name="description" placeholder="Ticket Jakarta-Bali">
<label>Quantity</label><input type="number" step="0.01" name="quantity" value="1">
<label>Selling Price</label><input type="number" step="0.01" name="unit_sale_price" value="0">
<label>Discount</label><input type="number" step="0.01" name="discount_amount" value="0">
<label>Tax</label><input type="number" step="0.01" name="tax_amount" value="0">
<label>Notes</label><textarea name="notes"></textarea>
<button type="submit">Create Booking</button>
</form>
<?= $this->endSection() ?>
