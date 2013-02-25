<?php
class Home_Index_Ctr
{
	public function main($a, $b, $c)
	{
		Application::hook()->addHookListener('HOME_TEST', TRUE);
		Application::hook()->addHookListener('HOME_CESHI', TRUE);
		
		Application::hook()->trigger('HOME_TEST');
		echo 'ceshi';
		echo $a . $b . $c;
		Application::hook()->trigger('HOME_CESHI');
	}
}
?>