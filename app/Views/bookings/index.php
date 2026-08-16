<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?= view('components/page_header', [
    'eyebrow' => 'Transactions',
    'title' => 'Bookings',
    'subtitle' => 'Central commercial transaction for customers, tours, tickets and travel services.',
    'action' => ['label' => 'New Booking', 'url' => site_url('bookings/create')]
]) ?>

<?= view('components/search_bar', [
    'action' => site_url('bookings'),
    'placeholder' => 'Search booking number or customer...',
    'value' => $q,
    'filters' => [
        ['name' => 'status', 'label' => 'All Status', 'value' => $status, 'options' => $statuses]
    ]
]) ?>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Booking No</th>
                <th>Customer</th>
                <th>Travel Date</th>
                <th>Source</th>
                <th>Total</th>
                <th>Outstanding</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>

            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="8" class="empty-cell">No bookings found.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><strong><?= esc($b['booking_no']) ?></strong></td>
                    <td><?= esc($b['customer_name'] ?? '-') ?></td>
                    <td><?= esc($b['travel_start_date'] ?? '-') ?></td>
                    <td><?= esc(ucwords(str_replace('_', ' ', $b['source']))) ?></td>
                    <td><?= number_format((float)$b['total_amount'], 0, ',', '.') ?></td>
                    <td><?= number_format((float)$b['outstanding_amount'], 0, ',', '.') ?></td>
                    <td><?= view('components/status_badge', ['status' => $b['status']]) ?></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn btn-secondary" href="<?= site_url('bookings/show/' . $b['id']) ?>">View</a>
                            <a class="btn btn-secondary" href="<?= site_url('bookings/edit/' . $b['id']) ?>">Edit</a>

                            <?php if ($b['status'] !== 'cancelled'): ?>
                                <form method="post"
                                      action="<?= site_url('bookings/cancel/' . $b['id']) ?>"
                                      onsubmit="return confirm('Cancel this booking?')"
                                      style="display:inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-danger" type="submit">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <?php if ($pager->getPageCount() > 1): ?>
            <div class="pagination-wrap"><?= $pager->links() ?></div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
