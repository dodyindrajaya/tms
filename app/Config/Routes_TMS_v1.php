<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'DashboardController::index');

// Customers
$routes->get('customers', 'CustomersController::index');
$routes->get('customers/create', 'CustomersController::create');
$routes->post('customers/store', 'CustomersController::store');

// Bookings
$routes->get('bookings', 'BookingsController::index');
$routes->get('bookings/create', 'BookingsController::create');
$routes->post('bookings/store', 'BookingsController::store');
$routes->get('bookings/(:num)', 'BookingsController::show/$1');
$routes->get('bookings/(:num)/invoice', 'BookingsController::invoice/$1');

// Invoices
$routes->get('invoices', 'InvoicesController::index');
$routes->get('invoices/(:num)', 'InvoicesController::show/$1');
$routes->get('invoices/(:num)/post', 'InvoicesController::post/$1');

// Payments
$routes->get('payments', 'PaymentsController::index');
$routes->get('payments/create', 'PaymentsController::create');
$routes->post('payments/store', 'PaymentsController::store');

// Accounting
$routes->get('accounting/journal', 'AccountingController::journal');
$routes->get('accounting/gl', 'AccountingController::gl');
