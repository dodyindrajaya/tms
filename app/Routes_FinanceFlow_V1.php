<?php
// Add these routes to app/Config/Routes.php. They are intentionally separate
// so the finance package can be installed without replacing the whole route file.
$routes->get('finance', 'FinanceController::index');

$routes->get('invoices', 'InvoicesController::index');
$routes->get('invoices/create/booking/(:num)', 'InvoicesController::createFromBooking/$1');
$routes->post('invoices/create/booking/(:num)', 'InvoicesController::createFromBooking/$1');
$routes->get('invoices/(:num)', 'InvoicesController::show/$1');
$routes->get('invoices/(:num)/post', 'InvoicesController::post/$1');

$routes->get('payments', 'PaymentsController::index');
$routes->get('payments/create', 'PaymentsController::create');
$routes->post('payments/store', 'PaymentsController::store');

$routes->get('accounting/journal', 'AccountingController::journal');
$routes->get('accounting/gl', 'AccountingController::gl');
$routes->get('accounting/ar', 'AccountingController::ar');
$routes->get('accounting/ap', 'AccountingController::ap');

// COA V1 routes from the latest COA package.
$routes->get('accounting/accounts', 'ChartOfAccounts::index');
$routes->get('accounting/accounts/create', 'ChartOfAccounts::create');
$routes->post('accounting/accounts/store', 'ChartOfAccounts::store');
$routes->get('accounting/accounts/show/(:num)', 'ChartOfAccounts::show/$1');
$routes->get('accounting/accounts/edit/(:num)', 'ChartOfAccounts::edit/$1');
$routes->post('accounting/accounts/update/(:num)', 'ChartOfAccounts::update/$1');
$routes->post('accounting/accounts/toggle/(:num)', 'ChartOfAccounts::toggle/$1');
$routes->post('accounting/accounts/delete/(:num)', 'ChartOfAccounts::delete/$1');
$routes->get('accounting/coa', 'ChartOfAccounts::index');
