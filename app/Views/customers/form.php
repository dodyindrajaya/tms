<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$isEdit = ($mode ?? 'create') === 'edit';

$action = $isEdit
    ? site_url('customers/' . $customer['id'] . '/update')
    : site_url('customers/store');
?>

<h1>
    <?= $isEdit ? 'Edit Customer' : 'New Customer' ?>
</h1>

<div class="card">

<form method="post" action="<?= $action ?>">

    <label>Customer Code</label>

    <input
        type="text"
        name="customer_code"
        value="<?= esc(old('customer_code', $customer['customer_code'] ?? '')) ?>"
        <?= $isEdit ? 'readonly' : '' ?>
    >

    <label>Name *</label>

    <input
        type="text"
        name="name"
        required
        value="<?= esc(old('name', $customer['name'] ?? '')) ?>"
    >

    <label>Customer Type</label>

    <select name="customer_type">

        <option value="individual"
            <?= old('customer_type', $customer['customer_type'] ?? 'individual') === 'individual' ? 'selected' : '' ?>>
            Individual
        </option>

        <option value="company"
            <?= old('customer_type', $customer['customer_type'] ?? '') === 'company' ? 'selected' : '' ?>>
            Company
        </option>

    </select>

    <label>Phone</label>

    <input
        type="text"
        name="phone"
        value="<?= esc(old('phone', $customer['phone'] ?? '')) ?>"
    >

    <label>Email</label>

    <input
        type="email"
        name="email"
        value="<?= esc(old('email', $customer['email'] ?? '')) ?>"
    >

    <label>Address</label>

    <textarea name="address"><?= esc(old('address', $customer['address'] ?? '')) ?></textarea>

    <button type="submit">
        <?= $isEdit ? 'Update Customer' : 'Save Customer' ?>
    </button>

    <a class="btn" href="<?= site_url('customers') ?>">
        Cancel
    </a>

</form>

</div>

<?= $this->endSection() ?>