<?php
/*
type: sys-lib
location: libraries/_Date.php
	Quản lý ngày tháng.
	'd': ngày (day)
	'm': tháng (month)
	'y': năm (year)
	Định dạng ngày tháng: d-m-y, y-m-d. Note: không sử dụng định dạng '/'
*/
class _Date{
	public $date_o;
	public $default_format='d-m-y h:i:s';	//default
	/*
		tự động chuyển cùng về 1 định dạng or thiết lập định dạng
	*/
	function __construct($date=null,$f='d-m-y h:i:s'){
		if($date!=null) $this->set_date($date);
		$this->default_format=$f;
	}
	
	function set_time_zone($tz){$this->$date_o->setTimeZone(new DateTimeZone($tz));return $this;}
	
	function get_date(){return $this->date_o;}
	
	function valid_datetime_object($t){//only date
		if(type($t)=='_Date')$y=$t->get_date();
			elseif(type($t)=='DateTime')$y=$t;
		if(isStr($t)&&isDate($t)){$s=explode('-',$t);$y=new DateTime();$y->setDate($s[0],$s[1],$s[2]);}
		if(isset($y)) return $y;
			else return false;
	}
	
	function set_date($date){if($date!=null)$this->date_o=$this->valid_datetime_object($date);return $this;}
	
	function set_time($tm){$c=explode(':',$tm);$this->date_o->setTime($c[0],$c[1],$c[2]);return $this;}
	
	function format($f){
		return $this->date_o->format($f);
	}
	
	function get_datetime_string(){return $this->format($this->default_format);}
}
?>