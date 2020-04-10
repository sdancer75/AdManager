<?php
/*************************************************************************************
Created by G. Papaioannou
You can contact with me at g_papaioannou@rocketmail.com

Paradox Interactive (c) 2011,Greece
http://www.paradoxinteractive.gr

**************************************************************************************/

$g_language_code ="us";
$g_language = 'english.php';
$DB_Prefix = 'adm_';
$g_ADM_Result = false;




define("MySQLIP", "127.0.0.1");
define("Username", "root");
define("Password", "");
define("DataBase", "adm");


    function IsAdminUser(){
        if ($_SESSION['user/id'] && ($_SESSION['user/privilege']==0) ) {
           return true;
        } else
          return false;
    }

?>