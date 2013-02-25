<?php
function create_dir($path)
{
	$index = 0;
	$arrPath = array();
	while ($path != '' && !is_dir($path)) {
		$arrPath[$index] = $path;
		$path = dirname($path);
		$index++;
	}
	
	if (is_array($arrPath)) {
		for ($i = count($arrPath)-1; $i >= 0; $i++) {
			mkdir($arrPath[$i]);
		}
	}
}

function delete_dir($path)
{
	if (!is_dir($path)) {
		return FALSE;
	}
	
	if (FALSE !== ($handle = opendir($path))) {
		while ( FALSE !== ($file = readdir($handle)) ) {
			if ('.' != $file && '..' != $file) {
				is_dir($path.'/'.$file) ? delete_dir($path.'/'.$file) : unlink($path.'/'.$file);
			}
		}
		closedir($handle);
		rmdir($path);
	}
	return TRUE;
}

function size_str($byte, $len = 2)
{
	$sizeArr = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	
	foreach ($sizeArr as $k => $v) {
		if ($byte < pow(1024, $k + 1)) {
			break;
		}
	}
	return round($size / pow(1024, $k), $len) . $v;
}
?>