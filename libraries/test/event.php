<?php
require "../core/hook.php";
function ceshi1()
{
	return 'ceshi1';
}
function ceshi2()
{
	return 'ceshi2';
}
function ceshi3()
{
	return 'ceshi3';
}

function test() {
	echo 'test';
}
Hook::setPriority(2);
Hook::addListeners('e_func', 'ceshi3', 0);
Hook::addListeners('e_func', 'ceshi2', 1);
Hook::addListeners('e_func', 'ceshi1', 2);
Hook::trigger('e_func')
?>