<html>
<head>
	<title>Sudoku 1.0</title>
	<style type='text/css'>
	body{
		background:#dadada;
		margin:0px;
	}
	.view{
		font-size:50px;
		text-align:center;
		background:#ffffff;
	}
	.hide{
		width: 40; height: 40;font-size:25;
		border:1px solid #dadada;
		text-align:center;
		color:red;
	}
	.button
	{
		padding:3px;
		border:1px solid gray;
		background:#999966;
		color:#FFFFFF;
	}
	</style>
	<script src="jquery-1.7.1.min.js"></script>
	
</head>
<body>

<script>
function get_debugger(line)
{
	var p=this;
	
	p.bound=document.createElement('div');
	p.bound.style.cssText='position:fixed; border:1px solid gray;background:#ffffff;opacity:0.5;filter:alpha(opacity=50);';
	
	p.title=document.createElement('div');
	p.title.style.cssText='background:gray;color:#ffffff;';
	$(p.title).html('debugger');
	
	p.contents=document.createElement('div');
	p.contents.style.cssText='overflow:auto;width:300;height:400;';
	
	p.line_contents=[];
	
	p.active_line;
	
	p.bound.appendChild(p.title);
	p.bound.appendChild(p.contents);
	document.body.appendChild(p.bound);
	
	p.set_line=function(str)
	{
		var l=document.createElement('div');
		l.style.cssText="border-bottom:3px solid gray;";
		p.line_contents.push(l);
		p.active_line=p.line_contents[p.line_contents.length-1];
		p.content(str);
		p.init();
	}
	
	p.init=function()
	{
		p.contents.innerHTML='';
		for(var i in p.line_contents) 
			p.contents.appendChild(p.line_contents[i]);
	}
	
	p.get_line=function(i){
		if(i<0) i=0;
		if(i>=p.line_contents.length) i=p.line_contents.length-1;
		p.active_line=p.line_contents[i];
		return p;
	}
	
	p.content=function(str){
		if(p.active_line)
		{
			if(str!==null) p.active_line.innerHTML=str;
				else return p.active_line.innerHTML;
		}else 
			p.get_line(0).content(str);
	}
	

	if(line) while(line--) p.set_line('');
	p.init();
}

if(<?php echo (isset($_GET['do'])&&$_GET['do']=='game')||(!isset($_GET['do']))?1:0?>)
	var test=new get_debugger(2);
</script>
<?php
/*hien thi loi*/
ini_set('display_errors','On');

include 'init.php';

define('app_libs','libraries/');

initialize_hphp();

include matrix;
include mysql;
include plugin_Element;

include class_Element;
include forms;
include Document;

/*init*/
actived_hDoc_instance();

/*
	@Tro choi sudoku
	@author: Q. Hoang, 01663.930.250
*/
class HQ_Sudoku extends matrix{

/*
	Đưa ra 1 phần tử ở vị trí dòng $i, cột $j
*/
function generate_item($i,$j){

	$mis=$this->get_total_items($i,$j);
	
	if(($picks=$this->group_top_right_items($i,$j))!==false)
	{
		//if($i==1&&$j==2) out1($picks,'b');
		$picks=_drw($picks);
		
		if(count(($e=$picks->removes($mis)->O))){
			//if($i==1&&$j==2) out1($e,'b');
			if(count($e)>1){
				
				if($this->get_col_items($j+1)!==null){
					$e=cross_array('max',true,$this->get_col_items($j+1),$e);
					
				}
			}
			
			$get=get_rand_item($e);
			//$get=pick_rand_number(1,$size,$mis);
			
		}else
			$get=pick_rand_number(1,$this->size,$mis);
		
	}else{
		$get=pick_rand_number(1,$this->size,$mis);
		
	}
	if($get==false){
		echo '{-----------<br>'.($i).','.($j-1);
		dump($this->group_top_right_items($i,$j-1));
		echo '-------------}<br>';
	}
	return $get;
}

/*
	Các phần tử ở phía góc top-right so với phần tử ($i,$j)
*/
function group_top_right_items($i,$j){
	if($i>=1){
		$ds=array('max',true);
		$res=array();
		for($q=0;$q<$i+1;$q++){
			$ds_=$this->get_row_items($q);
			shift($ds_,$j+1);
			if(count($ds_))$ds[]=$ds_;
		}
		//if($i==1&&$j==2) out1($ds);
		if(count($ds)>2){
			$res=cross_array($ds);
			if(!count($res)){
				$ds[0]='min';
				$res=cross_array($ds);
				
			}
			
			return $res;
		}else return false;
	}
	return false;
}

/*
	Lấy hợp các phần tử của dòng & cột có giao với phần tử ($i,$j)
*/
function get_total_items($i,$j){
	$this->remove_item($i,$j);
	
	$total=$this->get_row_items($i);
	$total=merge_distinct($total,$this->get_col_items($j));
	return $total;
}

/*
	Khởi tạo matrix Sudoku
*/
function init(){
//do
	for($i=0;$i<$this->size;$i++)
	for($j=0;$j<$this->size;$j++)
	{
		$this->grid[$i][$j]=$this->generate_item($i,$j);
	}

	$this->rand_rows();
}

/*
	Kiểm tra phần tử ($i,$j) có hợp lệ
*/
function valid_item($i,$j,$a)
{
	echo '['.$i.','.$j.']=>'.$a;dump($this->get_total_items($i,$j));
	return !in_array($a,$this->get_total_items($i,$j));
}

/*
	Chuyển $grid thành dữ liệu chuỗi
*/
function matrix_string()
{
	return serialize($this->grid);
}

/*
	Hiển thị kết quả sudoku
*/
function suggest_result($opt=true,$id='sudoku'){
	$view='<table border=1 class="view" id="'.$id.'">';

	for($i=0;$i<$this->size;$i++)
	{
		$tr='<tr >';
		for($j=0;$j<$this->size;$j++)
		{
			$tr.='<td width="50">'.$this->grid[$i][$j].'</td>';
		}
		$tr.='</tr>';
		$view.=$tr;
	}

	$view.='</table>';
	if($opt==true) echo $view;
	return $view;
}
/*
	Hiển thị trò chơi
*/
function show_game($opt1=true,$opt2='miss_items'){
	$view='<table id="sudoku" border="1px solid gray" cellspadding=0 cellspacing=0 class="view">';
	$items_hidden='';
	
	for($i=0;$i<$this->size;$i++)
	{
		$hide=pick_rand_more(0,$this->size-1,pick_rand_number(1,$this->size-1));
		$tr='<tr >';
		for($j=0;$j<$this->size;$j++)
		{
			if(in_array($j,$hide)){ 
				$items_hidden.=$i.'-'.$j.',';
				$str='<input onclick="focus_item(this)" onBlur="_input_item_event(this)"  class="hide" pos="'.$i.'-'.$j.'" value=""/>';
				//$this->grid[$i][$j]
			}else 
				$str=$this->grid[$i][$j];
				
			$tr.='<td width="50">'.$str.'</td>';
		}
		$tr.='</tr>';
		$view.=$tr;
	}

	$view.='</table>';
	
	if($opt1==true) echo $view;
	
	$game1=new HQ_Sudoku($this->size);
	$game1->grid=$this->grid;
	$game1->remove_item(explode(',',$items_hidden));
	
	return ($opt2=='miss_items')?substr($items_hidden,0,strlen($items_hidden)-1):$game1->grid;
}

/*------------------------------end of class-------------------------------------*/
}
/*global for main*/
$size=isset($_GET['size'])?$_GET['size']:3;

/*
	@do=game
*/
if(!isset($_GET['do'])||$_GET['do']=='game')
{
?>
	<center>
		<div style="padding:5px; background:#999966;font-size:20;color:#FFFFFF">Sudoku 1.0</div>
		<br/>
		<div style="">
		<form action='index.php' method='GET'>
		<input onclick="alert('Ban can nap lai game?');this.form.submit()" type='checkbox' <?php echo isset($_GET['auto_check'])?'checked':''?> name='auto_check'/><a> Kiểm tra ngay</a>
		<select name='size'>
		<?php 
		for($i=3;$i<=10;$i++)
		{
			echo '<option '.($size==$i?'selected':'').' value="'.$i.'">'.$i.'</option>';
		}
		?>
		</select>
		<input type='submit' value='reset'/>
		</form>
		<a class='button' href='javascript:void(0)' onclick="suggest_item()">>>Gợi ý</a>
		
		</div>
		<br/>
<?php
	$game=new HQ_Sudoku($size);

	$game->init();
	$game_grid=serialize($game->show_game(true,'grid'));
?>
	</center>
<?php
}
/*
	@do=check_item
*/
elseif($_GET['do']=='check_item')
{

	$matrix=unserialize($_GET['matrix']);
	$i=$_GET['i'];
	$j=$_GET['j'];
	$a=$_GET['a'];
	echo $size;
	
	$game1=new HQ_Sudoku($size);
	$game1->grid=$matrix;
	
	$valid=$game1->valid_item($i,$j,$a);
	if($valid)
	{
		$game1->set_item($i,$j,$a);
		
		//save to db
		$db=new mysql('sql311.summerhost.info','sum_6305668','1671988');
		$db->db('sum_6305668_test');
		$db->query("update sudoku set grid='".$game1->matrix_string()."'");
		
		echo '<textarea>'.$game1->matrix_string().($game1->is_full_matrix()?'[DONE]':'').'</textarea>';
		
		echo '<script>test.get_line(1).content("'.valid_str_in_str($game1->suggest_result(false,'ok')).'");</script>';
		
		exit();
	}
	else
	{	//out1($i.'-'.$j);
		$game1->remove_item($i,$j);
		
		echo '<textarea>'.$game1->matrix_string().'[FALSE]</textarea>';
		exit();
	}
}
/*
	@do=suggest_item
*/
elseif($_GET['do']=='suggest_item')
{
	$i=$_GET['i'];
	$j=$_GET['j'];
	$matrix_origin=unserialize($_GET['matrix_origin']);
	$matrix=unserialize($_GET['matrix']);
	
	//compare with matrix original to get item
	$game1=new HQ_Sudoku($size);
	$game1->grid=$matrix_origin;
	
	var_dump($matrix_origin);
	
	$item_value=$game1->get_item($i,$j);
	echo $i.'-'.$j.'=>'.$item_value;
	
	$game1->grid=$matrix;
	$game1->set_item($i,$j,$item_value);
	
	$done=$game1->is_full_matrix()?'*[DONE]':'';
	
	echo '<textarea>'.$item_value.'*'.$game1->matrix_string().$done.'</textarea>';
}

/*
	@do=valid_game
*/
elseif($_GET['do']=='valid_game')
{
	$matrix=unserialize($_GET['matrix']);
	$items_string=$_GET['items_string'];
	
	$items=explode(',',$items_string);
	
	$game1=new HQ_Sudoku($size);
	$game1->grid=$matrix;
	
	$valid=1;
	
	foreach($items as $v)
	{
		$item=explode(':',$v);
		$ij=explode('-',$item[0]);
		
		$game1->set_item($ij[0],$ij[1],$item[1]);
	}
	
	foreach($items  as $v)
	{
		$item=explode(':',$v);
		$ij=explode('-',$item[0]);
		
		if(!$game1->valid_item($ij[0],$ij[1],$item[1]))
		{
			
			$valid=0;
			break;
		}
	}
	
	echo '<textarea>'.$valid.'</textarea>';
}
?>

<?php if(isset($game)){ ?>

<textarea id='matrix_string' style='visibility:hidden;'><?php echo $game_grid;?></textarea>

<?php }?>


<!-- who is -->
<script>var _wau = _wau || []; _wau.push(["tab", "zc8tefoxil2w", "8cc", "left-middle"]);(function() { var s=document.createElement("script"); s.async=true; s.src="http://widgets.amung.us/tab.js";document.getElementsByTagName("head")[0].appendChild(s);})();</script>

</body>
<script>
<?php if(isset($game)){?>

var __item=[];

var _enabled_valid=<?php echo isset($_GET['auto_check'])?1:0?>

/*
	parse result that got from server
*/
function parse_text(html)
{
	var r=document.createElement('div');	
	$(r).html(html);
	return r.getElementsByTagName('textarea')[0].value;
	
	//return r.value.replace(/(\r\n|\n|\r|\s+)/gm, "");
}

/*
	get focus item
*/
function focus_item(p)
{
	__item[0]=p.getAttribute('pos');
	__item[1]=p;
}

/*
	disabled all inputs
*/
function disabled_all(opt)
{
	var items=document.getElementById('sudoku').getElementsByTagName('input');
	for(var i=0;i<items.length;i++)
	{
		items[i].disabled=opt;
	}
}

/*
	suggest item
*/

function suggest_item(){
	if(!__item.length) return;
	
	var ij=__item[0].split('-');
		
	var matrix=document.getElementById('matrix_string').value;
		
	var matrix_origin="<?php echo $game->matrix_string()?>";
		
	var url="index.php?do=suggest_item&matrix_origin="+matrix_origin+"&matrix="+matrix+"&i="+ij[0]+"&j="+ij[1]+'&size=<?php echo $size?>';
	
	disabled_all(true);
	
	$.get(url,function(dt){
		var rs=parse_text(dt).split('*');
		
		__item[1].value=rs[0];
		$('#matrix_string').text(rs[1]);
		
		if(rs.length==3) alert('Chuc mung ban, ban da chien thang !');
		else 
			disabled_all(false);
	});
}

/*
	valid input
*/
function valid_input_item(O){
	if(!$.trim(O.value)
		||!parseInt(O.value)
		||parseInt(O.value)<0 || parseInt(O.value)><?php echo $size?>
	){
			O.value='';
			return false;
		}
		return true;
}

/*
	event for each item
*/
function _input_item_event(O){
	if(!valid_input_item(O)) return;
if(_enabled_valid)
{
	var loader, ij, url, grid;
	
	ij = O.getAttribute('pos').split('-');
	grid=document.getElementById('matrix_string').value;//$('#sudoku').html();
	
	url='index.php?do=check_item&matrix='+grid+'&i='+ij[0]+'&j='+ij[1]+'&a='+O.value+'&size=<?php echo $size?>';
	
	disabled_all(true);
	
	$.get(url,function(dt){
		var r=parse_text(dt);
		
		if(r.match(/\[FALSE\]/g))
		{
			$('#matrix_string').text(r.replace(/\[FALSE\]/g,''));
			
			O.value='';
			alert('Khong hop le !');
			disabled_all(false);
			return;
		}
		if(r.match(/\[DONE\]/g))
		{
			$('#matrix_string').text(r.replace(/\[DONE\]/g,''));
			alert('Chuc mung ban, Ban da chien thang ! ^_^');
			
			return;
		}
		
		$('#matrix_string').text(r);
		
		disabled_all(false);
	});
}
else
{
	var i, items=document.getElementById('sudoku').getElementsByTagName('input'),
	items_string='',
	url;
	
	for(i=0;i<items.length;i++){
		if(!items[i].value) return;
		else items_string+=items[i].getAttribute('pos')+':'+items[i].value+',';
	}
	
	items_string=items_string.substr(0,items_string.length-1);
	
	url='index.php?do=valid_game&matrix=<?php echo $game->matrix_string()?>&items_string='+items_string+'&size=<?php echo $size?>';
	
	disabled_all(true);
	
	$.get(url,function(dt){
		if(!parseInt(parse_text(dt))) disabled_all(false);
		else 
			alert('Chuc mung, Ban da chien thang !');
	});
	
}
}	

<?php } ?>
</script>
</html>