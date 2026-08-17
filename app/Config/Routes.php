<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'DashboardController::index');

// Customers
// Copy these into app/Config/Routes.php
$routes->get('customers', 'Customers::index');
$routes->get('customers/create', 'Customers::create');
$routes->post('customers/store', 'Customers::store');
$routes->get('customers/edit/(:num)', 'Customers::edit/$1');
$routes->post('customers/update/(:num)', 'Customers::update/$1');
$routes->post('customers/delete/(:num)', 'Customers::delete/$1');

/* $routes->get('customers', 'CustomersController::index');
$routes->get('customers/create', 'CustomersController::create');
$routes->post('customers/store', 'CustomersController::store');
$routes->get('customers/(:num)', 'CustomersController::show/$1');
$routes->get('customers/(:num)/edit', 'CustomersController::edit/$1');
$routes->post('customers/(:num)/update', 'CustomersController::update/$1');
$routes->post('customers/(:num)/deactivate', 'CustomersController::deactivate/$1');
$routes->post('customers/(:num)/activate', 'CustomersController::activate/$1');
 */
// Bookings
/* $routes->get('bookings', 'BookingsController::index');
$routes->get('bookings/create', 'BookingsController::create');
$routes->post('bookings/store', 'BookingsController::store');
$routes->get('bookings/(:num)', 'BookingsController::show/$1');
$routes->get('bookings/(:num)/invoice', 'BookingsController::invoice/$1'); */

// Invoices
/* $routes->get('invoices', 'InvoicesController::index');
$routes->get('invoices/(:num)', 'InvoicesController::show/$1');
$routes->get('invoices/(:num)/post', 'InvoicesController::post/$1');

// Payments
$routes->get('payments', 'PaymentsController::index');
$routes->get('payments/create', 'PaymentsController::create');
$routes->post('payments/store', 'PaymentsController::store');
 */
// Accounting
$routes->get('finance', 'AccountingController::finance');
$routes->get('ledger', 'AccountingController::gl');
$routes->get('accounting/finance', 'AccountingController::finance');
$routes->get('accounting/journal', 'AccountingController::journal');
/* $routes->get('accounting/gl', 'AccountingController::gl');
$routes->get('accounting/ar', 'AccountingController::ar');
$routes->get('accounting/ap', 'AccountingController::ap'); */

// Copy these lines into app/Config/Routes.php
/* $routes->get('accounting/accounts', 'ChartOfAccounts::index');
$routes->get('accounting/accounts/create', 'ChartOfAccounts::create');
$routes->post('accounting/accounts/store', 'ChartOfAccounts::store');
$routes->get('accounting/accounts/show/(:num)', 'ChartOfAccounts::show/$1');
$routes->get('accounting/accounts/edit/(:num)', 'ChartOfAccounts::edit/$1');
$routes->post('accounting/accounts/update/(:num)', 'ChartOfAccounts::update/$1');
$routes->post('accounting/accounts/toggle/(:num)', 'ChartOfAccounts::toggle/$1');
$routes->post('accounting/accounts/delete/(:num)', 'ChartOfAccounts::delete/$1');
$routes->get('accounting/coa', 'ChartOfAccounts::index'); */

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
//---

// Products
$routes->get('products', 'Products::index');
$routes->get('products/create', 'Products::create');
$routes->post('products/store', 'Products::store');
$routes->get('products/edit/(:num)', 'Products::edit/$1');
$routes->post('products/update/(:num)', 'Products::update/$1');
$routes->post('products/delete/(:num)', 'Products::delete/$1');

//booking - ticket - tours
/* $routes->get('bookings', 'Bookings::index');
$routes->get('bookings/create', 'Bookings::create');
$routes->post('bookings/store', 'Bookings::store');
$routes->get('bookings/edit/(:num)', 'Bookings::edit/$1');
$routes->post('bookings/update/(:num)', 'Bookings::update/$1');
$routes->post('bookings/cancel/(:num)', 'Bookings::cancel/$1'); */

$routes->get('bookings', 'Bookings::index');
$routes->get('bookings/create', 'Bookings::create');
$routes->post('bookings/store', 'Bookings::store');

$routes->get('bookings/show/(:num)', 'Bookings::show/$1');
$routes->get('bookings/edit/(:num)', 'Bookings::edit/$1');
$routes->post('bookings/update/(:num)', 'Bookings::update/$1');
$routes->post('bookings/cancel/(:num)', 'Bookings::cancel/$1');


$routes->get('tickets', 'Tickets::index');
$routes->get('tickets/create', 'Tickets::create');
$routes->post('tickets/store', 'Tickets::store');
$routes->get('tickets/edit/(:num)', 'Tickets::edit/$1');
$routes->post('tickets/update/(:num)', 'Tickets::update/$1');
$routes->post('tickets/cancel/(:num)', 'Tickets::cancel/$1');

$routes->get('tours', 'Tours::index');
$routes->get('tours/create', 'Tours::create');
$routes->post('tours/store', 'Tours::store');
$routes->get('tours/edit/(:num)', 'Tours::edit/$1');
$routes->post('tours/update/(:num)', 'Tours::update/$1');
$routes->get('tours/departures/(:num)', 'Tours::departures/$1');
$routes->post('tours/departures/store/(:num)', 'Tours::storeDeparture/$1');
$routes->post('tours/departures/cancel/(:num)', 'Tours::cancelDeparture/$1');

/* // Passengers
$routes->get('passengers', 'Passengers::index');
$routes->get('passengers/create', 'Passengers::create');
$routes->post('passengers/store', 'Passengers::store');
$routes->get('passengers/edit/(:num)', 'Passengers::edit/$1');
$routes->post('passengers/update/(:num)', 'Passengers::update/$1');
$routes->post('passengers/delete/(:num)', 'Passengers::delete/$1'); */

//-- routes by gpt
/* $routes->get('ticketing', 'Ticketing::index');
$routes->get('ticketing/new', 'Ticketing::new');
$routes->post('ticketing/create', 'Ticketing::create');
$routes->get('ticketing/show/(:num)', 'Ticketing::show/$1');
$routes->get('ticketing/edit/(:num)', 'Ticketing::edit/$1');
$routes->post('ticketing/update/(:num)', 'Ticketing::update/$1');
$routes->post('ticketing/delete/(:num)', 'Ticketing::delete/$1');
$routes->get('ticketing/booking-passengers/(:num)', 'Ticketing::bookingPassengers/$1');

$routes->get('passengers', 'Passengers::index');
$routes->get('passengers/new', 'Passengers::new');
$routes->post('passengers/create', 'Passengers::create');
$routes->get('passengers/show/(:num)', 'Passengers::show/$1');
$routes->get('passengers/edit/(:num)', 'Passengers::edit/$1');
$routes->post('passengers/update/(:num)', 'Passengers::update/$1');
$routes->post('passengers/delete/(:num)', 'Passengers::delete/$1'); */

//--
$routes->get('ticketing', 'Ticketing::index');
$routes->get('ticketing/new', 'Ticketing::new');
$routes->post('ticketing/create', 'Ticketing::create');
$routes->get('ticketing/show/(:num)', 'Ticketing::show/$1');
$routes->get('ticketing/edit/(:num)', 'Ticketing::edit/$1');
$routes->post('ticketing/update/(:num)', 'Ticketing::update/$1');
$routes->post('ticketing/delete/(:num)', 'Ticketing::delete/$1');
$routes->get('ticketing/booking-passengers/(:num)', 'Ticketing::bookingPassengers/$1');

$routes->get('passengers', 'Passengers::index');
$routes->get('passengers/new', 'Passengers::new');
$routes->post('passengers/create', 'Passengers::create');
$routes->get('passengers/show/(:num)', 'Passengers::show/$1');
$routes->get('passengers/edit/(:num)', 'Passengers::edit/$1');
$routes->post('passengers/update/(:num)', 'Passengers::update/$1');
$routes->post('passengers/delete/(:num)', 'Passengers::delete/$1');