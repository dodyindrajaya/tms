# TMS Finance V1 - Test Checklist

1. Run `php spark migrate`.
2. Run `php spark db:seed TmsSeeder` (or `php spark db:seed --all` if your setup uses that command).
3. Open Bookings and choose an existing booking.
4. Click **Create Invoice**.
5. Open the invoice and click **Post Invoice & Create Journal**.
6. Open **Accounting > Journal Entries** and verify a balanced SALES journal: DR 1300 Accounts Receivable; CR revenue; CR 2100 tax when tax exists.
7. Open **Payments > Receive Payment**. Select the booking and a payment method such as BCA Transfer.
8. Enter an amount not greater than Outstanding, then save.
9. Verify the payment appears with a journal number.
10. Open the payment and verify the BANK journal: DR 1200 Bank; CR 1300 Accounts Receivable.
11. Verify Booking Paid and Outstanding are updated.
12. Verify General Ledger shows the posted lines.

The payment method is intentionally mandatory because `payments.payment_method_id` is NOT NULL in the database. The payment service now derives the accounting account from the selected payment method instead of trusting a manually posted account id.
