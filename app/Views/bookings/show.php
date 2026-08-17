<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$money = static fn($v) => 'Rp ' . number_format((float)$v, 0, ',', '.');
$statusLabel = $statuses[$booking['status']] ?? ucfirst((string)$booking['status']);

// Map status to badge class for dynamic styling
$statusClass = [
    'draft' => 'info',
    'pending' => 'warning',
    'confirmed' => 'success',
    'cancelled' => 'danger',
][$booking['status']] ?? 'secondary';
?>

<div class="booking-detail-page">

    <div class="booking-detail-header">
        <div>
            <div class="eyebrow">TRANSACTIONS / BOOKINGS</div>
            <div class="booking-detail-title-row">
                <h1><?= esc($booking['booking_no']) ?></h1>
                <span class="status-badge <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
            </div>
            <p>Booking detail, commercial lines and current payment position.</p>
        </div>

        <div class="booking-detail-actions">
            <a class="btn btn-secondary" href="<?= site_url('bookings') ?>">Back</a>
            <a class="btn btn-secondary" href="<?= site_url('bookings/edit/' . $booking['id']) ?>">Edit Booking</a>
            <?php if ($booking['status'] !== 'cancelled'): ?>
                <form method="post"
                      action="<?= site_url('bookings/cancel/' . $booking['id']) ?>"
                      onsubmit="return confirm('Cancel this booking?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger" type="submit">Cancel</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="booking-kpi-grid">
        <div class="booking-kpi-card">
            <span>Customer</span>
            <strong><?= esc($booking['customer_name'] ?? '-') ?></strong>
            <small><?= esc($booking['customer_code'] ?? '-') ?></small>
        </div>

        <div class="booking-kpi-card">
            <span>Travel Date</span>
            <strong><?= esc($booking['travel_start_date'] ?: '-') ?></strong>
            <small>
                <?php if (!empty($booking['travel_end_date'])): ?>
                    until <?= esc($booking['travel_end_date']) ?>
                <?php else: ?>
                    No end date
                <?php endif; ?>
            </small>
        </div>

        <div class="booking-kpi-card">
            <span>Total Booking</span>
            <strong><?= $money($booking['total_amount']) ?></strong>
            <small><?= esc($booking['currency_code']) ?></small>
        </div>

        <div class="booking-kpi-card booking-kpi-outstanding">
            <span>Outstanding</span>
            <strong><?= $money($booking['outstanding_amount']) ?></strong>
            <small>Paid <?= $money($booking['paid_amount']) ?></small>
        </div>
    </div>

    <div class="booking-detail-layout">

        <div class="booking-detail-main">

            <div class="card booking-detail-card">
                <div class="detail-card-header">
                    <div>
                        <h2>Booking Information</h2>
                        <p>Commercial transaction header.</p>
                    </div>
                    <span class="detail-chip"><?= esc($statusLabel) ?></span>
                </div>

                <div class="detail-info-grid">
                    <div>
                        <span>Booking No</span>
                        <strong><?= esc($booking['booking_no']) ?></strong>
                    </div>
                    <div>
                        <span>Booking Date</span>
                        <strong><?= esc($booking['booking_date']) ?></strong>
                    </div>
                    <div>
                        <span>Source</span>
                        <strong><?= esc(ucwords(str_replace('_', ' ', $booking['source']))) ?></strong>
                    </div>
                    <div>
                        <span>Currency</span>
                        <strong><?= esc($booking['currency_code']) ?></strong>
                    </div>
                    <div>
                        <span>Customer Type</span>
                        <strong><?= esc(ucfirst($booking['customer_type'] ?? '-')) ?></strong>
                    </div>
                    <div>
                        <span>Phone</span>
                        <strong><?= esc($booking['customer_phone'] ?? '-') ?></strong>
                    </div>
                    <div>
                        <span>Email</span>
                        <strong><?= esc($booking['customer_email'] ?? '-') ?></strong>
                    </div>
                    <div>
                        <span>Travel Period</span>
                        <strong>
                            <?= esc($booking['travel_start_date'] ?: '-') ?>
                            <?php if (!empty($booking['travel_end_date'])): ?>
                                → <?= esc($booking['travel_end_date']) ?>
                            <?php endif; ?>
                        </strong>
                    </div>
                </div>

                <?php if (trim((string)$booking['notes']) !== ''): ?>
                    <div class="booking-note">
                        <span>Notes</span>
                        <p><?= nl2br(esc($booking['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card booking-detail-card">
                <div class="detail-card-header">
                    <div>
                        <h2>Booking Items</h2>
                        <p>Products/services included in this booking.</p>
                    </div>
                    <span class="detail-count"><?= count($items) ?> line(s)</span>
                </div>

                <div class="table-wrap">
                    <table class="booking-detail-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Description</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Tax</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="7" class="empty-cell">No booking items found.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($item['product_name'] ?? '-') ?></strong>
                                    <small><?= esc($item['product_code'] ?? '') ?></small>
                                </td>
                                <td><?= esc($item['description'] ?? '-') ?></td>
                                <td class="text-right"><?= number_format((float)$item['quantity'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= $money($item['unit_sale_price']) ?></td>
                                <td class="text-right"><?= $money($item['discount_amount']) ?></td>
                                <td class="text-right"><?= $money($item['tax_amount']) ?></td>
                                <td class="text-right"><strong><?= $money($item['line_total']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card booking-detail-card">
                <div class="detail-card-header">
                    <div>
                        <h2>Ticketing</h2>
                        <p>Ticket records already linked to this booking.</p>
                    </div>
                    <a class="btn btn-primary" href="<?= site_url('tickets/create') ?>">+ New Ticket</a>
                </div>

                <div class="table-wrap">
                    <table class="booking-detail-table">
                        <thead>
                            <tr>
                                <th>Passenger</th>
                                <th>Type</th>
                                <th>Route</th>
                                <th>Departure</th>
                                <th>Ticket / Booking Code</th>
                                <th>Status</th>
                                <th class="text-right">Selling</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($tickets)): ?>
                            <tr>
                                <td colspan="7" class="empty-cell">
                                    No ticket linked yet. Ticketing V1 will use this booking as its commercial parent.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= esc($ticket['passenger_name'] ?? '-') ?></td>
                                <td><?= esc(ucfirst($ticket['ticket_type'] ?? '-')) ?></td>
                                <td><?= esc(($ticket['origin'] ?? '-') . ' → ' . ($ticket['destination'] ?? '-')) ?></td>
                                <td><?= esc($ticket['departure_date'] ?? '-') ?></td>
                                <td><?= esc($ticket['ticket_number'] ?: ($ticket['booking_code'] ?: '-')) ?></td>
                                <td><span class="status-badge <?= $statusClass ?>"><?= view('components/status_badge', ['status' => $ticket['status']]) ?></span></td>
                                <td class="text-right"><?= $money($ticket['selling_price']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <aside class="booking-detail-sidebar">

            <div class="card booking-financial-card">
                <div class="detail-card-header">
                    <div>
                        <h2>Financial Summary</h2>
                        <p>Current booking value.</p>
                    </div>
                    <div class="table-actions">
                        <?php if (!empty($invoice)): ?>
                            <a class="btn btn-secondary" href="<?= site_url('invoices/'.$invoice['id']) ?>">View Invoice</a>
                        <?php else: ?>
                            <a class="btn btn-primary" href="<?= site_url('bookings/'.$booking['id'].'/invoice') ?>">Create Invoice</a>
                        <?php endif; ?>
                        <?php if ((float)$booking['outstanding_amount'] > 0): ?>
                            <a class="btn btn-secondary" href="<?= site_url('payments/create') ?>">Receive Payment</a>
                        <?php endif; ?>
                    </div>
                </div>

                <table class="financial-summary-table">
                    <tr>
                        <td>Subtotal</td>
                        <td><?= $money($booking['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td class="negative">- <?= $money($booking['discount_amount']) ?></td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td><?= $money($booking['tax_amount']) ?></td>
                    </tr>
                    <tr class="financial-total">
                        <th>Total</th>
                        <th><?= $money($booking['total_amount']) ?></th>
                    </tr>
                    <tr>
                        <td>Paid</td>
                        <td class="positive"><?= $money($booking['paid_amount']) ?></td>
                    </tr>
                    <tr class="financial-outstanding">
                        <th>Outstanding</th>
                        <th><?= $money($booking['outstanding_amount']) ?></th>
                    </tr>
                </table>
            </div>

            <div class="card booking-next-card">
                <div class="detail-card-header">
                    <div>
                        <h2>Next Process</h2>
                        <p>Booking becomes the parent transaction.</p>
                    </div>
                </div>

                <div class="next-process-list">
                    <div class="next-process-item">
                        <span class="process-dot"></span>
                        <div>
                            <strong>Ticketing</strong>
                            <small>Flight, train, bus or ferry ticket can be linked to this booking.</small>
                        </div>
                    </div>
                    <div class="next-process-item">
                        <span class="process-dot"></span>
                        <div>
                            <strong>Tour</strong>
                            <small>Tour package/departure can later be fulfilled from the same booking.</small>
                        </div>
                    </div>
                    <div class="next-process-item">
                        <span class="process-dot"></span>
                        <div>
                            <strong>Payment</strong>
                            <small>Payment will reduce the outstanding balance and feed finance.</small>
                        </div>
                    </div>
                </div>
            </div>

        </aside>
    </div>
</div>

<?= $this->endSection() ?>
