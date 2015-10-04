<?php

$config['default_path'] = "http://{$_SERVER['SERVER_NAME']}/Project/";
$config['default_controller'] = 'Index';
$config['default_method'] = 'index';
$config['namespaces']['Controllers'] = 'ShoppingCart/Controllers/';
$config['namespaces']['Routers'] = 'ShoppingCart/Routers/';
$config['namespaces']['Models'] = 'ShoppingCart/Models/';

$config['displayExceptions'] = true;

$config['session']['auto_start'] = true;
$config['session']['type'] = 'native';
$config['session']['name'] = 'app_session';
$config['session']['lifetime'] = 60 * 60 * 15;
$config['session']['path'] = '/';
$config['session']['domain'] = '';
$config['session']['secure'] = false;

return $config;