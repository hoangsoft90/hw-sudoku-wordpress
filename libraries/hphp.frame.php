<?php
/*
type: sys-lib
location: libraries/frame.php
	frame
*/
/*
	extends: hEle, to_extend
*/
class frame extends hEle{
public $accept=array('span','div','label','form','a','b','i','u','fieldset','legend','p','ul','li');
public $child_type='div';
/*pointer:primitive*/
public $cell=null;
public $label;
public $cell_store;
private $idx=0;

function __construct($a=null){$a=func_get_args();$this->cell_store=new DRW();$i=null;$j=null;if(count($a))foreach($a as $v)isNum($v)?(!$i)?$i=$v:$j=$v:'';foreach($a as $y=>$v)if(isNum($v))unset($a[$y]);elseif(isStr($v)){$this->child_type=$v;$this->O=$this->c_cell($a);}if(!$this->O)$this->O=cEle('div')->css('padding:1px')->do_method($a)->O;$this->methods=function($ref,$n,$args){};}

function items($i=null){$s=$this->cell;if($s==null)$this->cell=$this->cell_store->eq(isNum($i)?$i:null);else{ if(isEle(_drw($s)->eq(0)))$this->cell=$s;else{ $this->cell=_drw($s)->eq($i);}}return $this;}

function get_object(){$s=$this->cell;$t=_drw($s)->eq(0);$h=array();if(isDrw($t))foreach($s as $i=>&$v)$h[]=$v[0];else return $t;return _($h);}

function c_cell($d=null){$o=Array();$a=optimize_args(func_get_args());if(count($a)&&($c=isset($a[0])?$a[0]:false)){if(isNum($c)){unset($a[0]);for($i=0;$i<$c;$i++)$o[]=$this->c_cell($a);}else foreach($a as $v)if(isDrw($v))$o[]=$this->c_cell($v);return (count($o)==1?$o[0]:$o);}else return cEle($this->child_type)->do_method(array('class'=>'frame_cell'),$a)->O;}

function nested(&$d,$s){$k=_drw($d)->search($s);$h=array();foreach($k[0] as $i=>&$v){$v[0]==$s;$m=explode('/',$v[1]);pop($m);$h[]=get_item_path($d,join($m,'/'));}return pure($h);}

function &cell_struct(&$c){if(!isDrw($c)){$o=array($c,'t'.($this->idx++)=>new DRW());return $o;}else{$h=array();foreach($c as $i=>&$v)$h['t'.($this->idx++)]=$this->cell_struct($v);return $h;} }

function addCells($ag,$inf){$a=_drw($ag);if(!$a->get('css'))$a->push(array('css'=>$inf.'width:100%'));foreach($a->O as $i=>&$v)if(isDrw($v)){if(isset($v['css'])){preg_match('/([^;]+)?width.*?[^;]+[;|\w,-]/',$v['css'],$m);if(count($m))$j=($n=explode(':',$m[0]))&&$n[0]=='width';$r=substr($v['css'],strlen($v['css'])-1,1)==';'?'':';';$v['css'].=$r.$inf.(isset($j)&&$j?'':'width:100%');}else $v['css']=$inf.'width:100%';}
$r=$this->c_cell($a->O);$s=$this->cell;
$clone=function(&$c,$p){_drw($c)->copy($y);$w=$p->cell_struct($y);return array(pure($y,true),&$w);};
if($s!=null&&isDrw(_drw($s)->eq(0))){
	$y=new DRW();
	foreach($s as $i=>&$q){$z=$clone($r,$this);$u=&$z[1];$y->push($u);_($q[0])->addChild($z[0]);_drw($q)->eq(1)->push($u);};$this->cell=&$y->O;
	}
else{$z=$clone($r,$this);$u=&$z[1];$os=($s!=null)?_($this->cell[0]):$this;if($s!=null){_drw($this->cell)->eq(1)->push($u);$this->cell=$u;}else{$this->cell=$u;if(!isDrw($z[1]))$this->cell_store->push(array('t'.($this->idx++)=>$z[1]));else foreach($z[1] as $s1=>&$v)$this->cell_store->push(array('t'.($this->idx++)=>$v));}$os->addChild($z[0]);}
}

function addRows($d=null){$this->addCells(func_get_args(),'display:table;');return $this;}

function addCols($d=null){$this->addCells(func_get_args(),'display:table-cell;');return $this;}

function isRow($r){return $r->css('display')=='table';}

function root(){$this->cell=null;return $this;}

function parent(){$s=$this->cell;$k=_drw($s)->eq(0);$a=&$this->cell_store->O;if(!isDrw($k))$k=$s;$m=$this->cell_store->search($k);foreach($m[0] as $v)if($v[0]==$k){$l=$v[1];break;};$w=explode('/',$l);pop($w,2);for($i=0;$i<count($w)-1;$i++)($h=OBJ($a,false))&&$a=&$h[$w[$i]];$this->cell=_drw(OBJ($a,false))->eq(0);return $this;}

function next_prev($i=1,$t='next'){$s=$m=$this->cell;if(isDrw(_drw($s)->eq(0)))return $this;$this->parent();$s=_drw($this->cell)->eq(1);$this->cell=OBJ($s);$k=_drw($this->cell)->set_point($m);$r=($t=='next')?$k->next($i):$k->prev($i);$this->cell=&$r[1];}

function next($i=1){$this->next_prev($i);return $this;}

function prev($i=1){$this->next_prev($i,'prev');return $this;}

function remove($k=null){$s=($k==null?$this->cell:$k->cell);$j='$this->cell_store->O';$a=$this->cell_store->O;if(isEle(_drw($s)->eq(0)))$s=array($s);foreach($s as $v1){$m=$this->cell_store->search($v1);$this->removeChild($v1[0]);if(!count($m[0]))continue;foreach($m[0] as $u){$w=explode('/',$u[1]);for($i=0;$i<count($w);$i++){pure($w[$i]);$j.=isset($a->O)?"->O['".$w[$i]."']":"['".$w[$i]."']";$h=OBJ($a,false);$a=&$h[$w[$i]];};eval('unset('.$j.');');}}return $this;}

function setLabel(){}

function unLabel(){}

function reLabel(){}
};
?>