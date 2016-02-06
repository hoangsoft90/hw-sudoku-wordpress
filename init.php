<?php
function initialize_hphp()
{
	if(!defined('app_libs')){
		exit('không tồn tại hằng "app_libs".');
	}
	require(rtrim(app_libs,'/').'/init/require.php');
}
?>