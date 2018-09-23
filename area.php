<?php 

require "global.php";
$file = file('area.txt');  
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";

$files = array();  
foreach($file as $v){  
  $f = explode('    ',$v);  
  //这里用trim()函数去除头尾的空格不起效果，因为是全角格式。所以用功能更强大的正则来去除空格  
  $f[1] = mb_ereg_replace('^(　| )+', '', $f[1]);  
  array_push($files,$f);  
}  
echo '<pre>';  




	  $sql="INSERT INTO `".$db_prefix."nation` (`id`, `code`, `province`, `city`, `district`, `parent`) VALUES";
	  $i=0;
      foreach ($files  as $url){ 
	  if($i>0){ $sql=$sql.",";}
	  if ( substr($url[0],2,4)=='0000'){
		  
		  echo "$i $url[0] $url[1] <br />"; 
	  
 $sql=$sql."('', '$url[0]', '$url[1]', '', '', '0')";
    } 
	  elseif ( substr($url[0],4,2)=='00')
	  {
		    echo "$i  -- $url[0] $url[1] <br />"; 
 $sql=$sql."('', '$url[0]', '', '$url[1]', '', '".substr($url[0],0,2)."0000')";
	  }
	  	  else
	  {
     echo "  ----$i $url[0] $url[1] <br />"; 
	  	  $sql=$sql."('', '$url[0]', '', '', '$url[1]', '".substr($url[0],0,4)."00')";
    } 
	
	$i++;
		}
		echo $sql;
		//$DB->query($sql);	
						//echo json_encode(array('error'=>'birthday', 'msg'=>''.trim($_POST[birthday])));
			//exit();
					 // echo $sql;
  //$DB->query($sql);	
//print_r($files);  



?>