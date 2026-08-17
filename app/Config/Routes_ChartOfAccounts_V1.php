<?php
// Copy these lines into app/Config/Routes.php
$routes->get('accounting/accounts', 'ChartOfAccounts::index');
$routes->get('accounting/accounts/create', 'ChartOfAccounts::create');
$routes->post('accounting/accounts/store', 'ChartOfAccounts::store');
$routes->get('accounting/accounts/show/(:num)', 'ChartOfAccounts::show/$1');
$routes->get('accounting/accounts/edit/(:num)', 'ChartOfAccounts::edit/$1');
$routes->post('accounting/accounts/update/(:num)', 'ChartOfAccounts::update/$1');
$routes->post('accounting/accounts/toggle/(:num)', 'ChartOfAccounts::toggle/$1');
$routes->post('accounting/accounts/delete/(:num)', 'ChartOfAccounts::delete/$1');
$routes->get('accounting/coa', 'ChartOfAccounts::index');
