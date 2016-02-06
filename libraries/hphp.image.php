<?php
/*image class*/
class image{
function __construct(){}
static function img($src){return cEle('img',array('src'=>$src))->do_method(func_get_args());}
};
/*located:/system/libraries/image.php*/
?>