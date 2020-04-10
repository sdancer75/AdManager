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


    if ($_POST['submittedform']='yes') {

        if ($_GET['type']=='edit') {

                UpdateDB('categories','cID',$_POST['recordid'],'cMachineName=\''.$_POST['machinename'].'\',cDescription=\''.($_POST['description']).'\' ,cActive='.(($_POST['active']=='on')?1:0).',cHeight='.$_POST['height'].',cWidth='.$_POST['width'].',cUserID='.$_POST['userid'].', cCreationDate=Now()');

                header( 'Location: categories.php' );

        } else if ($_GET['type']=='delete') {


                //if category, delete all the files first
                DeleteCategoryFiles($_POST['recordid']);
                DeleteDB('categories','cID',$_POST['recordid']);
                header( 'Location: categories.php' );

        }


    }



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<Title>Edit/Add Category</Title>
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

	    tooltip_creationdate = new YAHOO.widget.Tooltip("tooltip_creationdate",
							{ context:"creationdate",
                              autodismissdelay:10000,
							  text:"Ημερομηνία δημιουργίας της κατηγορίας. Δημιουργείται αυτόματα από το σύστημα και είναι πεδίο μόνο ανάγνωσης." });


	    tooltip_width = new YAHOO.widget.Tooltip("tooltip_width",
							{ context:"width",
                              autodismissdelay:10000,
							  text:"Ορίστε το πλάτος σε pixels. Αν όρισετε μόνον το πλάτος και στο ύψος βάλετε 0,<br>τότε το ύψος θα υπολογιστεί αυτόματα διατηρώντας το λόγο αναλογίας." });


	    tooltip_height = new YAHOO.widget.Tooltip("tooltip_height",
							{ context:"height",
                              autodismissdelay:10000,
							  text:"Ορίστε το ύψος σε pixels. Αν όρισετε μόνον το ύψος και στο πλάτος βάλετε 0,<br>τότε το πλάτος θα υπολογιστεί αυτόματα διατηρώντας το λόγο αναλογίας." });

	    tooltip_machinename = new YAHOO.widget.Tooltip("tooltip_machinename",
							{ context:"machinename",
                              autodismissdelay:10000,
							  text:"Αναγνωριστικό μηχανής. Επιτρέπονται μόνον λατινικοί χαρακτήρες, αριθμοί και το σύμβολο της κάτω παύλας _" });

	    tooltip_description = new YAHOO.widget.Tooltip("tooltip_description",
							{ context:"description",
                              autodismissdelay:10000,
							  text:"Περιγραφικός τίτλος για την κατηγορία" });

	    tooltip_active = new YAHOO.widget.Tooltip("tooltip_active",
							{ context:"active",
                              autodismissdelay:10000,
							  text:"Ενεργοποιεί ή απενεργοποιεί την κατηγορίας. Εξ ορισμού είναι ενεργοποιημένη" });

    </script>


	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php ($_GET['Action']=='edit') ? getTranslation('Edit Category') : getTranslation('Add Category')?></h3>
				<p><?php  ($_GET['Action']=='edit') ? getTranslation('Edit Category Comments') : getTranslation('Add Category Comments') ?></p>
                <br />
                <hr width=100% />
                <br />
			</div>
            <div class="cols">
            <br />
                <?php

                   $machinename = "";
                   $description = "";
                   $active = "";
                   $height = "";
                   $width = "";
                   $creationdate = "";
                   $userid = -1;

                    if ($_GET['Action']=='edit') {
                            OpenDB();
                            $result_id=QueryDB("SELECT *  FROM ".GetDBPrefix()."categories Where cID=".$_GET['Record']." Limit 1");
                            $row=GetResultQueryDB($result_id);
                            if ($row) {


                               $machinename = $row['cMachineName'];
                               $description = ($row['cDescription']);
                               $active = $row['cActive'];
                               $height = $row['cHeight'];
                               $width = $row['cWidth'];
                               $creationdate = date("d/m/Y",strtotime ($row['cCreationDate']));
                               $userid = $row['cUserID'];

                            }
                            CloseDB();
                    }

                   OpenDB();
                   if (!IsAdminUser())
                        $UserRestriction = "WHERE uID=".get_session("user/id");

                   $result_id=QueryDB("SELECT uUsername,uID  FROM ".GetDBPrefix()."users ".$UserRestriction." order by uUsername desc");
                   $user_data="";

                   if ($result_id) {

                           while ($row=GetResultQueryDB($result_id)) {
                              $user_data .= '<option value="'.$row['uID'].'" '.( ($_GET['Record']==$userid) ? 'selected' : '' ).'>'.$row['uUsername'].'</option>';
                           }
                   }
                   CloseDB();

                ?>
            	<form method="post" id="customform" >
                    <input type="hidden" id="submittedform" name="submittedform" value="yes" />
                    <input type="hidden" id="recordid" name="recordid" value="<?php echo $_GET['Record'] ?>" />
            		<fieldset class="login">
            			<legend>Γενικά στοιχεία</legend>
            			<div>
            				<label for="creationdate">*Ημερ. Δημιουργίας</label> <input class="inputlong readonly" type="text" id="creationdate" name="creationdate" value="<?php echo $creationdate ?>" readonly >

            			</div>
            			<div>
            				<label for="machinename">*Αναγνωριστικό</label> <input class="inputlong" type="text" id="machinename" name="machinename" value="<?php echo $machinename ?>" onkeypress="editKeyBoard(event,keybAlphaNumeric);" maxlength="40">

            			</div>
            			<div>
            				<label for="password">Περιγραφή</label> <input class="inputlong" type="text" id="description" name="description" value="<?php echo $description ?>" maxlength="100">
            			</div>
            			<div>
            				<label for="active">*Ενεργό</label> <input class="inputtiny" type="checkbox" checked id="active" name="active" <?php echo ($active==1) ? 'checked' : '' ?>>
            			</div>
            			<div>
            				<label for="user">*Χρήστης</label>
                            <select class="dropdown dd_long" name="userid" id="userid" size="1">
                            <?php echo $user_data ?>
                            </select>
            			</div>
            		</fieldset>

            		<fieldset class="contact">
            			<legend>Διαστάσεις</legend>

            			<div>
            				<label for="width">*Πλάτος</label> <input class="inputsmall" type="text" id="width" name="width" value="<?php echo $width ?>" onkeypress="editKeyBoard(event,keybNumeric);">
            			</div>
            			<div>
            				<label for="height">*Ύψος</label> <input class="inputsmall" type="text" id="height" name="height" value="<?php echo $height ?>" onkeypress="editKeyBoard(event,keybNumeric);">
            			</div>


            		</fieldset>

                    <button type="button" class="button" onclick="javascript:action='editcategory.php?type=edit';checkvalues(); ">Αποθήκευση</button>
                     <?php if ($_GET['Action']=='edit') { ?>
                        <button type="button" class="button" onclick="javascript:action='editcategory.php?type=delete';if (confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή ?')) {checkvalues();} ">Διαγραφή</button>
                    <?php } ?>
                    <button type="button" class="button" onclick="javascript:window.location='categories.php'; ">Επιστροφή</button>

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

			if ( (machinename.value=='') || (width.value=='') || (height.value=='') )
                  alert('Συμπληρώστε όλα τα υποχρεωτικά πεδία');
            else  {

                  if ( (isNaN(parseInt(width.value))) || (isNaN(parseInt(height.value)) )  ) {
                     alert('Δεν δώσατε αριθμητικές τιμές σε κάποιο από τα πεδία, πλάτος ή ύψος.');
                     return;
                  }

                  if ( (parseInt(width.value)==0) && (parseInt(height.value)==0 )  ) {
                     alert('Δεν μπορεί να είναι ταυτόχρονα και το ύψος και το πλάτος 0.\n\nΑφήστε εαν επιθυμείτε 0 μόνον σε κάποια από τις διαστάσεις έτσι ώστε να υπολογιστεί αυτόματα σε αυτό το πεδίο, ο λόγος αναλογίας από την εφαρμογή.\n\n');
                     return;
                  }
				  document.forms['customform'].submit();
            }
        }

	}

</script>
