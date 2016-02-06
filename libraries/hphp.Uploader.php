<?php
/*
type: controller.
location: plugins/Uploader.php
	Quản lý upload file.
*/
class Uploader{
public $destination='';
public $file_handler='';
public $form;
public $target_frame;

function __construct($config=Array()){
	if(isset($config['destination'])) $this->setDestination($config['destination']);
	if(isset($config['file_handler'])) $this->set_file_handler($config['file_handler']);
	$target=isset($config['target'])?$config['target']:'_self';
	$this->form=new form(array('method'=>'POST','enctype'=>"multipart/form-data",'target'=>$target));
	$this->form->addChild(array(
		$this->form->file->create(array('type'=>'file','name'=>'file')),
		$this->form->submit->create(array('type'=>'submit','value'=>'upload'))
	));
}

function set_destination($dir){
	if(is_dir($dir))$this->destination=$dir;
	return $this;
}

function set_file_handler($hander){
	if(is_file($hander))$this->file_handler=$hander;
	return $this;
}

function html_form(){
	$action=$this->file_handler.'?destination='.($this->destination?$this->destination:'uploads');
	$this->form->attr('action',$action);
	return $this;
}

function set_target_frame($target,$opt=true){if($opt===true)$this->target_frame=cEle('iframe',array('name'=>$target));$this->form->attr('target',$target);return $this;}

function upload(){
	
}

}
?>