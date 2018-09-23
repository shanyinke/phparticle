<?php
error_reporting(7);

$noheader = 1;
require "global.php";

$iid= intval($_GET[iid]);

if (strstr($_SERVER[HTTP_USER_AGENT],"MSIE")) {
    $attachment = '';
} else {
    $attachment = ' atachment;';
}

$image = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."gallery WHERE id='$iid'");

$filename ="./upload/images/$image[filename]";
if (!file_exists($filename)) {
    $filename = "./images/notexist.gif";
//    exit;
}


header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                         // HTTP/1.0


//$extension = strtolower(substr(strrchr($image[original],"."),1));


    /*
if ($extension=='gif') {
    header('Content-type: image/gif');
} elseif ($extension=='jpg' or $extension=='jpeg') {
    header('Content-type: image/jpeg');
} elseif ($extension=='png') {
    header('Content-type: image/png');
} else {
    header('Content-type: unknown/unknown');
}
      */
header("Content-disposition:$attachment filename=$image[original]");

$size = @filesize($filename);

header("Content-type: $image[type]");
header("Content-Length: $size");

/*
if (pa_isset($image[type])) {
    header("Content-type: $image[type]");
} else {
    header('Content-type: unknown/unknown');
}
*/
//echo $image[type];exit;
$fd = fopen($filename,rb);
$contents = fread($fd,$size);
fclose ($fd);

echo $contents;
?>