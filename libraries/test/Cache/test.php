<?php
include 'Cache.php';
$cache = Grace_Cache_Cache::loadDriver('file');
print_r($cache);
$cache->set('a', array('b' => 1, 'd' => 2));
print_r($cache->get('a'));

$cache1 = Grace_Cache_Cache::loadDriver('memcached');
$cache1->set('a', 'ffeeesss');
print_r($cache1->get('a'));
?>