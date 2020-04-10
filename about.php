<?php
/*************************************************************************************
Created by G. Papaioannou
You can contact with me at g_papaioannou@rocketmail.com

Paradox Interactive (c) 2011,Greece
http://www.paradoxinteractive.gr
**************************************************************************************/

    session_start();
    include_once 'includes/config.php';
    include_once 'includes/functions.php';
    include_once 'translations/'.$g_language;
    include_once 'includes/login.php';

     OpenDB();

     if (!check_login() ) {
        header( 'Location: index.php' ) ;
     }

     CloseDB();




?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>About us</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />




</head>

<body class="yui-skin-sam">
    <?php include 'includes/navigation.php' ?>
    <?php include 'includes/header.php' ?>

	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php getTranslation('AboutUs') ?></h3>
				<p> </p>
			</div>
            <div class="tablereport">
                  <br />
                   <div id="report" align="center"></div>

                    <?php getTranslation('About Description') ?>


            </div>

			<div class="clear"></div>
		</div>
	</div>

    <?php include 'includes/footer.php' ?>
</body>

</html>
