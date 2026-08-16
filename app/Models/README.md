# TMS v1 CI4 Models

Copy all `*.php` files into:

    app/Models/

These models match the TMS v1 database migration.

Design rule:
- Models handle persistence/query basics.
- Business workflows belong in `app/Services/`.
- Accounting posting must be centralized in AccountingService so every journal is balanced.
- `returnType` is currently `array` for simple API/service use; it can be changed to entity classes later.

Generated models: 39.
