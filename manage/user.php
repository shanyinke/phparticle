<?php
error_reporting(7);
require "global.php";

if($action!=logout){
   cpheader();
}

if ($_GET[action]==logout){

    session_unset();
    session_destroy();

    setcookie("username","",time()-3600);
    setcookie("password","",time()-3600);

    cpheader();
    redirect("./index.php","<b>дЦрямкЁЖ╣гб╫</b>");
    cpfooter();
    exit;

}

cpfooter();
?>