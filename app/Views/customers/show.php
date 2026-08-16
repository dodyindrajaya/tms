<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="display:flex;justify-content:space-between;align-items:center;">
    <h1>Customer Detail</h1>

    <div>

        <a class="btn"
           href="<?= site_url('customers/' . $customer['id'] . '/edit') ?>">
            Edit
        </a>

        <a class="btn"
           href="<?= site_url('customers') ?>">
            Back
        </a>

    </div>
</div>

<div class="card">

<table>

<tr>
    <th style="width:200px;">Customer Code</th>
    <td><?= esc($customer['customer_code']) ?></td>
</tr>

<tr>
    <th>Name</th>
    <td><?= esc($customer['name']) ?></td>
</tr>

<tr>
    <th>Type</th>
    <td><?= esc(ucfirst($customer['customer_type'])) ?></td>
</tr>

<tr>
    <th>Phone</th>
    <td><?= esc($customer['phone']) ?></td>
</tr>

<tr>
    <th>Email</th>
    <td><?= esc($customer['email']) ?></td>
</tr>

<tr>
    <th>Address</th>
    <td><?= nl2br(esc($customer['address'])) ?></td>
</tr>

<tr>
    <th>Status</th>

    <td>

        <?php if ($customer['is_active']): ?>

            <strong style="color:green;">
                ACTIVE
            </strong>

        <?php else: ?>

            <strong style="color:red;">
                INACTIVE
            </strong>

        <?php endif; ?>

    </td>

</tr>

</table>

</div>

<div class="card">

<h3>Customer Status</h3>

<?php if ($customer['is_active']): ?>

    <form
        method="post"
        action="<?= site_url('customers/' . $customer['id'] . '/deactivate') ?>"
        onsubmit="return confirm('Nonaktifkan customer ini?');"
    >

        <button type="submit">
            Deactivate Customer
        </button>

    </form>

<?php else: ?>

    <form
        method="post"
        action="<?= site_url('customers/' . $customer['id'] . '/activate') ?>"
    >

        <button type="submit">
            Activate Customer
        </button>

    </form>

<?php endif; ?>

</div>

<?= $this->endSection() ?>