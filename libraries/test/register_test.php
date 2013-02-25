<?php
require "../core/register.php";
class test1
{
	
}
class test2
{
	
}
$arr = array(
	'a1' => new stdClass(),
	'a2' => new stdClass(),
	'a3' => new stdClass()
);
Register::setArray($arr);
Register::set('a', new test1());
Register::set('b', new test2());
Register::set('b', new test1());
print_r(Register::getAll())."\n";
Register::set('c', new test1());
print_r(Register::getAll())."\n";
Register::replace('c', new test2());
print_r(Register::getAll())."\n";
Register::remove('a');
print_r(Register::getAll())."\n";
Register::clear();
print_r(Register::getAll())."\n";
?>