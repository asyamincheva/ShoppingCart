<?php

const GOES_TO = 'goesTo';
const METHODS = 'methods';
const REQUEST_METHOD = 'requestMethod';
const NS = 'namespace';
const CONTROLLERS = 'controllers';

// Default
$config['*'][NS] = 'Controllers';

// Home
$config['*'][CONTROLLERS]['home'][GOES_TO] = 'index';
$config['*'][CONTROLLERS]['home'][METHODS]['index'] = 'index';
$config['*'][CONTROLLERS]['home'][REQUEST_METHOD]['index'] = 'get';

// Login
$config['*'][CONTROLLERS]['user'][GOES_TO] = 'user';
$config['*'][CONTROLLERS]['user'][METHODS]['login'] = 'login';
$config['*'][CONTROLLERS]['user'][REQUEST_METHOD]['login'] = 'post';

// Register
$config['*'][CONTROLLERS]['user'][METHODS]['register'] = 'register';
$config['*'][CONTROLLERS]['user'][REQUEST_METHOD]['register'] = 'post';

// Logout
$config['*'][CONTROLLERS]['user'][METHODS]['logout'] = 'logout';

// Categories
$config['*'][CONTROLLERS]['categories'][GOES_TO] = 'category';
$config['*'][CONTROLLERS]['categories'][METHODS]['index'] = 'index';

// Cart
$config['*'][CONTROLLERS]['cart'][GOES_TO] = 'cart';
$config['*'][CONTROLLERS]['cart'][METHODS]['index'] = 'index';

// Help
$config['*'][CONTROLLERS]['help'][GOES_TO] = 'help';
$config['*'][CONTROLLERS]['help'][METHODS]['index'] = 'index';

// Administration panel
$config['Admin'][NS] = 'Controllers\Admin';
$config['Admin'][CONTROLLERS]['index'][GOES_TO] = 'index';
$config['Admin'][CONTROLLERS]['index'][METHODS]['index'] = 'index';
$config['Admin'][CONTROLLERS]['index'][REQUEST_METHOD]['index'] = 'get';
$config['Admin'][CONTROLLERS]['index'][METHODS]['edit'] = 'edit';
$config['Admin'][CONTROLLERS]['index'][REQUEST_METHOD]['edit'] = 'get';
$config['Admin'][CONTROLLERS]['index'][METHODS]['add'] = 'add';
$config['Admin'][CONTROLLERS]['index'][REQUEST_METHOD]['add'] = 'post';
$config['Admin'][CONTROLLERS]['index'][METHODS]['remove'] = 'remove';
$config['Admin'][CONTROLLERS]['index'][REQUEST_METHOD]['remove'] = 'delete';

// Editor panel
$config['Editor'][NS] = 'Controllers\Editor';
$config['Editor'][CONTROLLERS]['index'][GOES_TO] = 'index';
$config['Editor'][CONTROLLERS]['index'][METHODS]['index'] = 'index';
$config['Editor'][CONTROLLERS]['index'][REQUEST_METHOD]['index'] = 'get';
$config['Editor'][CONTROLLERS]['category'][GOES_TO] = 'category';
$config['Editor'][CONTROLLERS]['category'][METHODS]['add'] = 'add';
$config['Editor'][CONTROLLERS]['category'][REQUEST_METHOD]['index'] = 'post';
$config['Editor'][CONTROLLERS]['category'][METHODS]['remove'] = 'remove';
$config['Editor'][CONTROLLERS]['category'][REQUEST_METHOD]['remove'] = 'delete';
$config['Editor'][CONTROLLERS]['category'][METHODS]['rename'] = 'rename';
$config['Editor'][CONTROLLERS]['category'][REQUEST_METHOD]['rename'] = 'put';

return $config;