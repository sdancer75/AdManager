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


    if ($_POST['submittedform']='yes') {

        if ($_GET['type']=='edit') {

                UpdateDB('users','uID',$_POST['recordid'],'uUsername=\''.$_POST['username'].'\',uPassword=\''.$_POST['password'].'\' ,uPrivilege='.$_POST['privilege']);

                header( 'Location: users.php' );

        } else if ($_GET['type']=='delete') {


                DeleteDB('users','uID',$_POST['recordid']);
                header( 'Location: users.php' );

        }


    }



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<Title>Edit/Add Users</Title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/screen.css" rel="stylesheet" type="text/css" >



<link rel="stylesheet" type="text/css" href="build/container/assets/skins/sam/container.css" />

<script type="text/javascript" src="build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="build/container/container-min.js"></script>
<script type="text/javascript" src="javascript/verifyinput.js"></script>

<style>



</style>




</head>

<body class="yui-skin-sam">

    <?php include 'includes/navigation.php' ?>
    <?php include 'includes/header.php' ?>


    <script type="text/javascript">

	    tooltip_username = new YAHOO.widget.Tooltip("tooltip_username",
							{ context:"username",
                              autodismissdelay:10000,
							  text:"To όνομα χρήστη, μέχρι 30 χαρακτήρες." });


	    tooltip_password = new YAHOO.widget.Tooltip("tooltip_password",
							{ context:"password",
                              autodismissdelay:10000,
							  text:"Ο κωδικός χρήστη, μέχρι 30 χαρακτήρες." });

	    tooltip_privilege = new YAHOO.widget.Tooltip("tooltip_privilege",
							{ context:"privilege",
                              autodismissdelay:10000,
							  text:"Δικαιώματα χρήστη." });



    </script>


	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php ($_GET['Action']=='edit') ? getTranslation('Edit User') : getTranslation('Add User')?></h3>
				<p><?php  ($_GET['Action']=='edit') ? getTranslation('Edit User Comments') : getTranslation('Add User Comments') ?></p>
                <br />
                <hr width=100% />
                <br />
			</div>
            <div class="cols">
            <br />
                <?php

                   $username = "";
                   $password = "";
                   $privilege = -1;
                   $userid = -1;

                    if ($_GET['Action']=='edit') {
                            OpenDB();
                            $result_id=QueryDB("SELECT *  FROM ".GetDBPrefix()."users Where uID=".$_GET['Record']." Limit 1");
                            $row=GetResultQueryDB($result_id);
                            if ($row) {


                               $username = $row['uUsername'];
                               $password = $row['uPassword'];
                               $privilege = $row['uPrivilege'];
                               $userid = $row['uID'];

                            }
                            CloseDB();
                    }



                ?>
            	<form method="post" id="customform" >
                    <input type="hidden" id="submittedform" name="submittedform" value="yes" />
                    <input type="hidden" id="recordid" name="recordid" value="<?php echo $_GET['Record'] ?>" />
            		<fieldset class="login">
            			<legend>Γενικά στοιχεία</legend>
            			<div>
            				<label for="username">*Όνομα χρήστη</label> <input class="inputlong" type="text" id="username" name="username" value="<?php echo $username ?>" maxlength="30">

            			</div>
            			<div>
            				<label for="password">*Κωδικός χρήστη</label> <input class="inputlong" type="text" id="password" name="password" value="<?php echo $password ?>" maxlength="30">

            			</div>
            			<div>
            				<label for="user">*Δικαιώματα</label>
                            <select class="dropdown dd_long" name="privilege" id="privilege" size="1">
                            <?php
                                echo '<option value="1">Χρήστης</option>';
                                if ( ( ($privilege==0) || ($privilege==-1) ) && (IsAdminUser()) ) {
                                    echo '<option value="0" selected>Διαχειριστής</option>';
                                }
                            ?>
                            </select>
            			</div>
            			<div>

            			</div>
            		</fieldset>


                    <button type="button" class="button" onclick="javascript:action='edituser.php?type=edit';checkvalues(); ">Αποθήκευση</button>
                     <?php if ( ($_GET['Action']=='edit') && ($userid != 1) ) { ?>
                        <button type="button" class="button" onclick="javascript:action='edituser.php?type=delete';if (confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή ?')) {checkvalues();} ">Διαγραφή</button>
                    <?php } ?>
                    <button type="button" class="button" onclick="javascript:window.location='users.php'; ">Επιστροφή</button>

            	</form>
                <p>* Υποχρεωτικά πεδία</p>


            </div>
           </div>
		   <div class="clear"></div>
		</div>


    <?php include 'includes/footer.php' ?>
</body>

</html>

<script LANGUAGE="javascript" TYPE="text/javascript">
	function checkvalues()
	{
		with(window.document.forms['customform'])
		{

			if ( (username.value=='') || (password.value=='')  )
                  alert('Συμπληρώστε όλα τα υποχρεωτικά πεδία');
            else  {


				  document.forms['customform'].submit();
            }
        }

	}

</script>
