// TMS Booking + Ticketing + Tour routes.
// Copy into app/Config/Routes.php

$routes->get('bookings', 'Bookings::index');
$routes->get('bookings/create', 'Bookings::create');
$routes->post('bookings/store', 'Bookings::store');
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
