// TMS Booking Detail + Edit V1
// Copy these booking routes into app/Config/Routes.php.
// Keep the existing Ticket/Tour routes unchanged.

$routes->get('bookings', 'Bookings::index');
$routes->get('bookings/create', 'Bookings::create');
$routes->post('bookings/store', 'Bookings::store');

$routes->get('bookings/show/(:num)', 'Bookings::show/$1');
$routes->get('bookings/edit/(:num)', 'Bookings::edit/$1');
$routes->post('bookings/update/(:num)', 'Bookings::update/$1');
$routes->post('bookings/cancel/(:num)', 'Bookings::cancel/$1');
