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
<title>Select Banner Type</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/screen.css" rel="stylesheet" type="text/css" >




</head>

<body class="yui-skin-sam">
    <?php include 'includes/navigation.php' ?>
    <?php include 'includes/header.php' ?>

	<div id="content">
		<div id="content-wrap">
            <?php
                   OpenDB();
                   if (!IsAdminUser())
                        $UserRestriction = "WHERE cUserID=".get_session("user/id");

                   $result_id=QueryDB("SELECT cDescription,cID  FROM ".GetDBPrefix()."categories ". $UserRestriction." order by cID desc");
                   $category_data='<option value="-1">---Επιλογή κατηγορίας---</option>';

                   if ($result_id) {

                           while ($row=GetResultQueryDB($result_id)) {
                              $category_data .= '<option value="'.$row['cID'].'">'.$row['cDescription'].'</option>';
                           }
                   }
                   CloseDB();

            ?>



			<div class="welcome">
				<h3><?php getTranslation('Banners') ?></h3>
				<p><?php getTranslation('BannersType') ?> </p>
                <br />
                <hr width=100% />
                <br />
			</div>
            <p>
        	<form method="post" id="customform" >
          		<fieldset class="general">
          			<legend>Επιλογή Κατηγορίας</legend>
          			<div>
          				<label for="category">*Κατηγορία</label>
                          <select class="dropdown dd_long" name="categoryid" id="categoryid" size="1">
                          <?php echo $category_data ?>
                          </select>
          			</div>
          		</fieldset>
            </form>
            </p>
            <div id="col-left" class="cols">
                <a href="javascript:submit('editbanner.php?Action=add&Record=-1&Class=text');">
                <h3 class="texttype"></h3>
                <h3>Κείμενο</h3>
                </a>
		   </div>
            <div id="col-mid" class="cols">
                <a href="javascript:submit('editbanner.php?Action=add&Record=-1&Class=image');">
                <h3 class="imagetype"></h3>
                <h3>Εικόνα</h3>
                </a>
		   </div>
            <div id="col-right" class="cols">
                <a href="javascript:submit('editbanner.php?Action=add&Record=-1&Class=code')">
                <h3 class="codetype"></h3>
                <h3>Κώδικας</h3>
                </a>

		   </div>
           <div class="clear"></div>
     </div>
    </div>

    <?php include 'includes/footer.php' ?>
</body>

</html>

<script LANGUAGE="javascript" TYPE="text/javascript">
	function submit(url)
	{

		with(window.document.forms['customform'])
		{

			if (categoryid.value=='-1')
                  alert('Επιλέξτε μια κατηγορία');
            else
				  window.location = url+'&CategoryID='+categoryid.value;
        }

	}

</script>