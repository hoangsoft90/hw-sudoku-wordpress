<?php
function initialize_hphp()
{
	if(!defined('app_libs')){
		exit('Kh�ng t?n t?i h?ng "app_libs".');
	}
	include rtrim(app_libs,'/').'/init/require.php';
}
?>