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

      global $LoginMsg;

      $LoginMsg = getTranslationString('Please write your username and password to login.');

      if ($_POST['submittedlogform']=='yes') {


           OpenDB();

           if (check_login() ) {
              header( 'Location: home.php' ) ;
           }

           CloseDB();



      }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Paradox Interactive, ADManager Login Page</title>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/styleform.css" rel="stylesheet" type="text/css"  />
</head>

<body>
	<div id="top">
		<div id="top-menu">
			<h1><a href="index.php"></a></h1>
			<div class="clear" ></div>
		</div>
	</div>
    <?php include 'includes/header.php' ?>
	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php getTranslation('Welcome to our website!') ?></h3>
				<p></p>
				<p><?php echo $LoginMsg ?></p>
			</div>

			<div class="cols">

                <div id="login-form">

            	<form  action="index.php" method="post">
                <input type="hidden" id="submittedlogform" name="submittedlogform" value="yes" />
            		<fieldset>

            			<legend>Log in</legend>

            			<label for="login"><?php getTranslation('user') ?></label>
            			<input type="text" id="login_name" name="login_name"/>
            			<div class="clear"></div>

            			<label for="password"><?php getTranslation('password') ?></label>
            			<input type="password" id="login_pass" name="login_pass"/>
            			<div class="clear"></div>

            			<label for="remember_me" style="padding: 0;"><?php getTranslation('password') ?></label>
            			<input type="checkbox" id="remember_me" style="position: relative; top: 3px; margin: 0; " name="remember_me"/>
            			<div class="clear"></div>

            			<br />

            			<input type="submit" style="margin: -20px 0 0 287px;" class="button" name="commit" value="Log in"/>
            		</fieldset>
            	</form>
                </div>

			</div>


			<div class="clear"></div>
		</div>
	</div>
    <?php include 'includes/footer.php' ?>
</body>

</html>
