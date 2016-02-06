<?php
/*
type: sys-lib
location: libraries/URLLoader.php
	liên kết nạp môi trường file để sử lý lấy thông tin.
*/
class URLLoader{
	public $curl;
	public $url;
	public $curl_opts=Array();
	
	function __construct($url=null){
		$this->curl=curl_init();
		$this->opts(array(CURLOPT_RETURNTRANSFER=>1,CURLOPT_URL=>$url);
	}
	
	function get_instance(){return $this->curl;}
	
	function opts($name,$value=''){if(func_num_args()==2&&isStr($name)&&isStr($value))curl_setopt($this->curl,$name,$value);else foreach(func_get_args() as $v)if(isDrw($v))foreach($v as $i=>$l)$this->opts($i,$l);return $this;}
	
	function set_header($header){if(isDrw($header))$this->opts(CURLOPT_HTTPHEADER,$header);return $this;}
	
	static function get_site_header(){
		$o=new URLLoader('http://serverheader.com/header/form.html');
		
	}
	
	function init(){$this->opts($this->curl_opts);return $this;}
	
	function setURL($url){
		if(is_string($url)){
			$this->url=$url;
			$this->opts(CURLOPT_URL,$url);
		}
		return $this;
	}
	
	function load($url=null){
		$this->setURL($url);
		return curl_exec($this->curl);
	}
	
	function close(){curl_close($this->curl);}
}
?>