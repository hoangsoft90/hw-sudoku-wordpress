<?php
/*
type: sys-lib
location: libraries/hphp.En_Decrypt.php
	Mã hóa, giải mã.
*/
class En_Decrypt{

}
?>


<?php
$key="kljhflk73#OO#*U$O(*YO";
//encrypt string
function encrypt($string, $key) {
  $result = '';
  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)+ord($keychar));
    $result.=$char;
  }

  return base64_encode($result);
}
//decrypt string
function decrypt($string, $key) {
  $result = '';
  $string = base64_decode($string);

  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)-ord($keychar));
    $result.=$char;
  }

  return $result;
}


$file=@file_get_contents('mahoa_ne.php');
if($file) echo "<br>ok";
//if(str_replace(strpos('huyhoang','huy')!='') echo "found";
if($HTTP_GET_VARS['txt_en']!='')
echo encrypt($HTTP_GET_VARS['txt_en'], $key)."<br>";
if($HTTP_GET_VARS['txt_de']!='')
echo decrypt($HTTP_GET_VARS['txt_de'], $key)."<br>";
?>
<form method="get" action="encd.php">
<input type="text" name="txt_en"/><input type="submit" value="encode"> 
</form>
<form method="get" action="encd.php">
<input type="text" name="txt_de"/><input type="submit" value="encode"> 
</form>