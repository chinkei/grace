<?php
$router['type'] = 'STANDARD'; // REWRITE STANDARD PATH_INFO PATH_INFO

$router['varprefix'] = ':';
$router['delimiter'] = '_';
$router['pattern']   = ':module/:contrl/:method/*';

$router['reqs']['module'] = '[a-zA-Z0-9\.\-_]+';
$router['reqs']['contrl'] = '[a-zA-Z0-9\.\-_]+';
$router['reqs']['method'] = '[a-zA-Z0-9\.\-_]+';

$router['default']['module'] = 'home';
$router['default']['contrl'] = 'index';
$router['default']['method'] = 'main';

$router['postfix']  = '.htm';
$router['type']     = 'PATH_PARM'; // REWRITE STANDARD PATH_INFO PATH_PARM
?>