<html> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<head> 
<title>多文件上传</title> 
</head> 
<body> 
<form accept="" method="post" enctype="multipart/form-data"> 
<input type="file" name="img[]" /><br /> 
<input type="file" name="img[]" /><br /> 
<input type="file" name="img[]" /><br /> 
<input type="file" name="img[]" /><br /> 
<input type="file" name="img[]" /><br /> 
<input type="file" name="img[]" /><br /> 
<input type="submit" name="s" /><br /> 
</form> 
<?php 
error_reporting(7);
//上传文件信息 
  extract($_GET,EXTR_SKIP);
    extract($_POST,EXTR_SKIP);
$img = $_FILES['img']; 
if ($img) 
{ 
//文件存放目录，和本php文件同级 
$dir = dirname(__file__); 
$i = 0; 
foreach ($img['tmp_name'] as $value) 
{ 
$filename = $img['name'][$i]; 
if ($value) 
{ 
$savepath="$dir\\$filename"; 
$state = move_uploaded_file($value, $savepath); 
//如果上传成功，预览 
if($state) 
{ 
echo "<img src='$filename' alt='$filename' /> "; 
} 
} 
$i++; 
} 
} 
?> 
</body> 
</html> 