# TMS v1 CI4 Services

Copy all PHP files to:

    app/Services/

Current services:
- AccountingService
- BookingService
- InvoiceService
- PaymentService
- SupplierBillService
- MarginService
- CustomerService

Important:
1. AccountingService validates that every journal is balanced.
2. InvoiceService posts AR/revenue.
3. PaymentService posts cash/bank against AR.
4. SupplierBillService posts expense/COGS against AP.
5. MarginService calculates booking revenue, cost and margin.
6. Business logic is kept in Services rather than Controllers.

This is a foundation/vertical-slice implementation. Before production use, add:
- authorization
- idempotency keys
- locking/concurrency handling
- tax handling
- invoice/payment allocation
- refund/void/reversal
- supplier payment workflow
- audit trail
- API request validation
- automated tests
