<?php
/*
type: sys-lib
location: libraries/table.php
	table
*/
/*
	extends: hEle, to_extend
*/
class table extends hEle{

function __construct($a=null){$this->O=cEle('table',array('cellspacing'=>1,'cellpadding'=>1,'border'=>'1px'))->O;$this->c_rows(func_get_args());}

function c_rows($n){$rs=array();$a=args_level_1(func_get_args());if(count($a)&&($s=_drw($a)->fir())&&!isDrw($s[1]))$a=array($a);foreach($a as $v)$rs[]=cEle('tr')->addChild($this->c_cols($v));_($this->O)->addChild($rs);return $this;}

function c_cols($d){$h=array();if(!isDrw($d))$d=(array)$d;foreach($d as $v)$h[]=cEle('td',(isNumStr($v)?array('html'=>$v):(isDrw($v)?$v:null)));return $h;}

function cell($i=null,$j=null){if(isNum($i))return _(_($this->_('tr'))->eq($i));else return _(_($this->O)->_('tr'));if(isNum($j)&&isNum($i))return _($this->cell($i)->eq($j));}

};
?>