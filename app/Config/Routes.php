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
