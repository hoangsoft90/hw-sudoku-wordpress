<?php
/*
type: sys-lib
location: libraries/mysql.php
	Quản lý CSDL mysql.
*/
/*
	extends: table_struct, DRW, to_extend
*/
if(!class_exists('table_store'))require table_store;

class mysql extends table_store{

const SQL_BIND_INHERIT='SQL_BIND_INHERIT';

private static $con;

public $result;

public $table;

private $bind=null;

private $order=null;

public $host;

public $user;

public $pass;

static function &mysql_instance($config=Array()){
	if(self::connected()) $db=new mysql(self::$con);
	else if(count($config)>=3) $db=new mysql($config['host'],$config['user'],$config['pass']);
	return $db;
}

function mysql($host='localhost',$user='root',$pass='root'){
	$this->connect($host,$user,$pass);
	if(is_resource($host)) self::$con=$host;
}

function connect($host,$user,$pass){
	if(isStr($host)&&isStr($user)&&isStr($pass)){
		self::$con=mysql_connect($host,$user,$pass) or die('Loi ket noi MySQL!');
		$this->show_error();
		$this->host=$host;$this->user=$user;$this->pass=$pass;
	}
	return $this;
}

function show_error(){
	if(($msg=mysql_error())){
		out("<div style='background:#dadada'>");out($msg);out("</div>");
		return true;
	}
	return false;
}

static function connected(){return is_resource(self::$con);}

function reconnect(){if(!self::connected())$this->connect($this->host,$this->user,$this->pass);}

function db($db){mysql_select_db($db,self::$con);return $this;}

function query($sql,$calback=null){
	$this->result=mysql_query($sql,self::$con);
	$this->show_error();
	if(ispack($calback))$calback($this->result);
	return $this;
}

function free_resource(){mysql_free_result($this->result);return $this;}

function num_rows(){if(!$this->show_error())return mysql_num_rows($this->result);}

function table_exist($table,$db_name){
	$this->query('show tables from '.$db_name);
	if(!$this->show_error()){
		$d=$this->fetch_data(false);
		if(count($d))foreach($d as $i=>$v)if($i==$table)return true;
	}
	return false;
}

function fetch($z){/*array('table'=>'huy','limit'=>3,'format'=>'array')*/
	$a=func_get_args();
	$cols='*';
	if(isDrw($a[0])){
		$table=_drw($a[0])->get('table');	
			if(!$table){
				$table=$this->table;
				$cols=isDrw($table)?(isStr($table[1])?$table[1]:'*'):'*';
			}
		$limit=_drw($a[0])->get('limit');if(!$limit)$limit='';else $limit=' LIMIT '.$limit;
		$format=_drw($a[0])->get('format');
	}else $table=$a[0];
	if(!$format)$format='array';
	
	$query="select ".$cols." from ".$table.$this->bind.$this->order.$limit;
	$this->query($query);
	$dt=$this->fetch_data();
	return ($format=='array')?$dt:(object)$dt;
}

function fetch_data($opt=true){
	$j=Array();
	if(!$this->show_error())while($v=($opt?mysql_fetch_array($this->result):mysql_fetch_row($this->result)))$j[]=$v;return $j;
}

function table($table,$bind=null,$order=null){
	$tb=str($table);
	if($tb->pos(':')!=false){
		$this->table=array();
		$this->table[]=$tb->split(':')->eq(0);
		$this->table[]=$tb->split(':')->eq(1);
	}
		else $this->table=$table;
	if($bind)$this->bind=" WHERE ".$bind;
	if($order)$this->order=" ORDER BY ".$order;
	return $this;
}

function bind($c=null){
	$a=func_get_args();
	foreach($a as $c)if($c&&isStr($c))$this->bind.=(!$this->bind)?' WHERE '.$c:' AND '.$c;
	return $this;
}

private function sql($m){
	$a=new DRW($m);$s='';$c='';$tb='';
	while($y=$a->shift()){$k=$y[1];if($k===true)$this->table=$k;if(!isStr($k))continue;$j=new STR($k);if($j->bind_pos('or','>','<','=',',')){if(!$s)$s=$k;else $c=' WHERE '.$k;}else{ if($k!==SQL_BIND_INHERIT&&!$tb)$tb=$k;else $this->order=$k;}};
	if(!$c)$c=$this->bind;
	return Array(((!$tb)?$this->table:$tb),$s,$c);
}

function insert($w){
	$a=func_get_args();$e=$this->sql($a);$t=explode(':',$e[0]);$cols='';$vals='';foreach(explode(',',$e[1]) as $v){$r=explode('=',$v);if(count($r)>1){$cols.=$r[0].',';$vals.=$r[1].',';}else $vals.=$r[0].',';};$q='insert into '.$t[0].((count(explode(',',$cols))==count(explode(',',$vals)))?'('.substr($cols,0,strlen($cols)-1).')':'').' values('.substr($vals,0,strlen($vals)-1).')';$this->query($q);
	return $this;
}

function update($w){
	$e=$this->sql(func_get_args());$this->query('update '.$e[0].' set '.$e[1].$e[2]);
	return $this;
}

function del($w){
	$e=$this->sql(func_get_args());$t=explode(':',$e[0]);$q='delete from '.$t[0].' where '.$e[1];$this->query($q);return $this;
}

function mysql_copy($t1,$to,$c=''){
	$t=explode(':',$t1);$q="SELECT ".(count($t)>1?$t[1]:'*')." INTO ".$to." FROM ".$t[0].($c?" WHERE ".$c:'');$this->query($q);
	return $this;
}

function create_table($n,$fields){
	$f=new STR($fields);$q='';foreach($f->toDRW() as $i=>$v)$q.=$i.' '.$v.',';$this->query('create table '.$n.'('.substr($q,0,strlen($q)-1).')');
	return $this;
}

function table_array($ag){
	$a=func_get_args();$tab=array();$fields=array();foreach($a as $v)if(isDrw($v))foreach($v as $k)$tab[$k]=null;/*add tab as member*/if(isStr($a[0])&&!property_exists($this,$a[0]))$this->{$a[0]}=array();return isStr($a[0])?$this->{$a[0]}:$tab;
}

function add_row_table(){}

function create_db($n){if(isStr($n))$this->query("create database ".$n);return $this;}

function close(){mysql_close(self::$con);return $this;}

function __destruct(){}
};
?>