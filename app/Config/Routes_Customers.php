// Copy these into app/Config/Routes.php
$routes->get('customers', 'Customers::index');
$routes->get('customers/create', 'Customers::create');
$routes->post('customers/store', 'Customers::store');
$routes->get('customers/edit/(:num)', 'Customers::edit/$1');
$routes->post('customers/update/(:num)', 'Customers::update/$1');
$routes->post('customers/delete/(:num)', 'Customers::delete/$1');
