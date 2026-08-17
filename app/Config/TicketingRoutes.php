<?php
// Copy these route definitions into app/Config/Routes.php

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
