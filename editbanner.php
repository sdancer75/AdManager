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
    include_once 'includes/resize.php';
    include_once 'includes/login.php';

     OpenDB();

     if (!check_login() ) {
        header( 'Location: index.php' ) ;
     }

     CloseDB();



    function CheckURL($url) {

        if ($url=='')
            return '';

        if ((stristr($url,"http://"))===false)
            return  htmlspecialchars('http://'.$url,ENT_QUOTES);
        else
            return  htmlspecialchars($url,ENT_QUOTES);

    }

    function RemoveNewLine($text){
        $parsedText = str_replace(chr(10), "", $text);
        return str_replace(chr(13), "", $parsedText);

    }

    function Add_Edit_Record($image_name,$contenttype) {

          if (empty($_POST['swf_flash']))
             $_POST['swf_flash']='0';

          if (empty($_POST['swf_color']))
             $_POST['swf_flash']='#FFFFFF';

          if (empty($_POST['swf_width']))
             $_POST['swf_width']='0';

          if (empty($_POST['swf_height']))
             $_POST['swf_height']='0';

          //check the position. if they are not the same, do the swap
          if ($_POST['position'] != $_POST['current_position']) {
                OpenDB();
                $result_id=QueryDB('UPDATE '.GetDBPrefix().'banners SET bPosition='.$_POST['current_position'].' WHERE bPosition='.$_POST['position'].' AND bCategoryID='.$_POST['categoryid']);
                CloseDB();

          }

          UpdateDB('banners','bID',$_POST['recordid'],'bMachineName=\''.$_POST['machinename'].'\',bDescription=\''.$_POST['description'].'\' ,bActive='.(($_POST['active']=='on')?1:0).',bCode=\''.htmlspecialchars(html_entity_decode($_POST['code'],ENT_QUOTES,'UTF-8'),ENT_QUOTES).'\', bText=\''.htmlspecialchars(html_entity_decode($_POST['text'],ENT_QUOTES,'UTF-8'),ENT_QUOTES).'\', bImage=\''.$image_name.
                   '\', bSWF='.(($_POST['swf_flash']=='on')?1:0).', bSWFBgColor=\''.$_POST['swf_color'].'\', bSWFWidth=\''.$_POST['swf_width'].'\', bSWFHeight=\''.$_POST['swf_height'].'\', bStyleCSS=\''.htmlspecialchars($_POST['stylecss'],ENT_QUOTES).'\', bURL=\''.CheckURL($_POST['url']).'\', bStartDate=\''.DateToIso($_POST['startdate']).'\', bEndDate=\''.DateToIso($_POST['enddate']).'\', bPosition='.$_POST['position'].', bCategoryID='.$_POST['categoryid'].', bContentType='.$contenttype.', bCreationDate=Now()');






    }


    if ($_POST['submittedform']='yes') {


        switch ($_POST['classtype']) {
            case 'text' : $contenttype=0;break;
            case 'image' : $contenttype=1;break;
            case 'code' : $contenttype=2;break;
            default: $contenttype=-1;
        }

        if ( ($_POST['action']=='edit') ||  ($_POST['action']=='add') ) {


                $swf_flash=(($_POST['swf_flash']=='on')?1:0);
                $InsertOrUpdate = true;
                $image_name=$_POST['image'];


                //check to see if we want to update the image
                if ($_POST['action']=='edit') {

                    if (empty($_FILES["imageselection"]["name"])) {
                       $InsertOrUpdate = 0;
                    } else {
                        $InsertOrUpdate = 1;
                    }

                }




                //if image and insert or update is true just go on
                if  ( ($contenttype==1) && ($InsertOrUpdate==1)  ) {

                    //define a maxim size for the uploaded images in Kb
                     define ("MAX_SIZE","3000");

                    //This function reads the extension of the file. It is used to determine if the file  is an image by checking the extension.
                     function getExtension($str) {
                             $i = strrpos($str,".");
                             if (!$i) { return ""; }
                             $l = strlen($str) - $i;
                             $ext = substr($str,$i+1,$l);
                             return $ext;
                     }

                    //This variable is used as a flag. The value is initialized with 0 (meaning no error  found)
                    //and it will be changed to 1 if an errro occures.
                    //If the error occures the file will not be uploaded.
                    $errors=0;

                 	//reads the name of the file the user submitted for uploading
                 	$image=$_FILES['imageselection']['name'];
                 	//if it is not empty
                 	if ($image)	{
                 	//get the original name of the file from the clients machine
                 		$filename = stripslashes($_FILES['imageselection']['name']);
                 	//get the extension of the file in a lower case format
                  		$extension = getExtension($filename);
                 		$extension = strtolower($extension);
                     	//if it is not a known extension, we will suppose it is an error and will not  upload the file,
                    	//otherwise we will do more tests

                         if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "swf") )
                   		 {

                   			$errors=1;

                   		 } else if ( ($extension == "swf") && ($swf_flash==0) ){

                            $errors=5;

                         } else {

                             if ($swf_flash==0) {


                                 switch ($extension) {
                                    case 'jpg' : $imagetype=IMAGETYPE_JPEG;break;
                                    case 'jpeg' : $imagetype=IMAGETYPE_JPEG;break;
                                    case 'png' : $imagetype=IMAGETYPE_PNG;break;
                                    case 'gif' : $imagetype=IMAGETYPE_GIF;break;

                                 }

                             }
                            //get the size of the image in bytes
                             //$_FILES['image']['tmp_name'] is the temporary filename of the file
                             //in which the uploaded file was stored on the server
                             $size=filesize($_FILES['imageselection']['tmp_name']);

                            //compare the size with the maxim size we defined and print error if bigger
                            if ($size > MAX_SIZE*1024)
                            {
                            	$errors=2;

                            } else {
                                //we will give an unique name, for example the time in unix time format
                                $image_name=time();
                                $temp_name='tempimage'.'.'.$extension;
                                $image_name .='.'.$extension;
                                //the new name will be containing the full path where will be stored (images folder)
                                $newname="banners/".$temp_name;
                                //we verify if the image has been uploaded, and print error instead
                                $copied = copy($_FILES['imageselection']['tmp_name'], $newname);
                                if (!$copied)
                                {
                                	$errors=3;
                                }

                                //Well done. Now check if we are updating this banner. If yes, delete the old image

                                if ($_POST['action']=='edit') {


                                    unlink("banners/".$_POST['image']);

                                }


                               if ( ($swf_flash==0) && ($errors==0) ) {

                                      //Resize the image
                                     $image = new SimpleImage();
                                     $image->load($newname);

                                     $DoResize = true;
                                     if ($imagetype==IMAGETYPE_GIF) {
                                         if ($image->is_animated_gif() > 0) {
                                           $DoResize = false;
                                           rename($newname, "banners/".$image_name);
                                         }
                                     }

                                     if ($DoResize==true) {


                                         if ($_POST['height']==0) {
                                             $image->resizeToWidth($_POST['width']);
                                         } else if ($_POST['width']==0) {
                                             $image->resizeToHeight($_POST['height']);
                                         } else {
                                             $image->resize($_POST['width'],$_POST['height']);
                                         }

                                         //convert all to jpg
                                         //$image->save("banners/".$image_name);

                                         //keep the original format
                                         $image->save("banners/".$image_name,$imagetype);

                                         unlink($newname);

                                     }






                               } else {

                                    rename($newname, 'banners/'.$image_name);
                               }



                            }
                        }
                    } else {

                        $errors=4;
                    }




                      if ($errors > 0) {

                         header( 'Location: error.php?error_type='.$errors.'&Action='.$_POST['action'].'&Record='.$_POST['recordid'].'&Class='.$_POST['classtype'].'&CategoryID='.$_POST['categoryid']);

                      }

                }





                if ($errors == 0) {

                    Add_Edit_Record($image_name,$contenttype);
                    header( 'Location: banners.php' );

                }

        } else if ($_POST['action']=='delete') {

                //1st delete the file
                if ($_POST['classtype']=="image")					
                    unlink($_SERVER['DOCUMENT_ROOT']."//adm/banners//".$_POST['image']);

                //2nd update the position of the rest
                UpdateDBPosition($_POST['recordid']);


                //3nd delete the record
                DeleteDB('banners','bID',$_POST['recordid']);

                //4th redirect
                header( 'Location: banners.php' );

        }


    }



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<Title>Edit/Add Banner</Title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/screen.css" rel="stylesheet" type="text/css" >



<link rel="stylesheet" type="text/css" href="build/container/assets/skins/sam/container.css" />

<script type="text/javascript" src="build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="build/container/container-min.js"></script>
<script type="text/javascript" src="javascript/verifyinput.js"></script>


<script language="javascript" type="text/javascript" src="javascript/edit_area/edit_area_full.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="css/calendar-blue.css" title="win2k-cold-1" />
<script type="text/javascript" src="javascript/calendar/calendar_min.js"></script>
<script type="text/javascript" src="javascript/calendar/lang/calendar-el.js"></script>
<script type="text/javascript" src="javascript/calendar/calendar-setup_min.js"></script>
<!-- TinyMCE -->
<script type="text/javascript" src="javascript/tiny_mce/tiny_mce.js"></script>




</head>

<body class="yui-skin-sam">

    <?php include 'includes/navigation.php' ?>
    <?php include 'includes/header.php' ?>


    <script type="text/javascript">







	    tooltip_creationdate = new YAHOO.widget.Tooltip("tooltip_creationdate",
							{ context:"creationdate",
                              autodismissdelay:10000,
							  text:"Ημερομηνία δημιουργίας της κατηγορίας. Δημιουργείται αυτόματα από το σύστημα και είναι πεδίο μόνο ανάγνωσης." });


	    tooltip_stylecss = new YAHOO.widget.Tooltip("tooltip_stylecss",
							{ context:"stylecss",
                              autodismissdelay:10000,
							  text:"Το στύλ που θα έχει το αντικείμενο. Αυτός ο κώδικας θα προστεθεί σε ετικέτα div με την ιδιότητα του style.<br>Δεν απαιτείται να βάλετε την ετικέτα style διότι θα μπεί αυτόματα." });


	    tooltip_url = new YAHOO.widget.Tooltip("tooltip_url",
							{ context:"url",
                              autodismissdelay:10000,
							  text:"Η διεύθυνση που θα εμφανιστεί όταν ο χρήστης κάνει κλικ επάνω στο αντίκειμενο, πχ http://www.google.gr" });

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

	    tooltip_image = new YAHOO.widget.Tooltip("tooltip_image",
							{ context:"image",
                              autodismissdelay:10000,
							  text:"To όνομα της εικόνας που έχει αποθηκευτεί. Πεδίο μόνο ανάγνωσης" });

	    tooltip_startdate = new YAHOO.widget.Tooltip("tooltip_startdate",
							{ context:"startdate",
                              autodismissdelay:10000,
							  text:"Ημερομηνία έναρξης της διαφήμισης. Αν δεν έχει οριστεί εμφανίζεται συνεχώς." });

	    tooltip_enddate = new YAHOO.widget.Tooltip("tooltip_enddate",
							{ context:"enddate",
                              autodismissdelay:10000,
							  text:"Ημερομηνία τερματισμού της διαφήμισης. Αν δεν έχει οριστεί εμφανίζεται συνεχώς." });

	    tooltip_position = new YAHOO.widget.Tooltip("tooltip_position",
							{ context:"position",
                              autodismissdelay:10000,
							  text:"Σε ποιά θέση θα εμφανίζεται στη συγκεκριμένη κατηγορία." });

	    tooltip_swf = new YAHOO.widget.Tooltip("tooltip_swf",
							{ context:"swf_flash",
                              autodismissdelay:10000,
							  text:"Αν είναι flash animation (SWF) τότε ενεργοποιήστε την επιλογή αυτήν. Ο κώδικας θα παραχθεί αυτόματα." });

	    tooltip_swf_color = new YAHOO.widget.Tooltip("tooltip_swf_color",
							{ context:"swf_color",
                              autodismissdelay:10000,
							  text:"Χρώμα background." });

	    tooltip_swf_width = new YAHOO.widget.Tooltip("tooltip_swf_width",
							{ context:"swf_width",
                              autodismissdelay:10000,
							  text:"Ορισμός πλάτους SWF ανεξάρτητα από το πλάτος της κατηγορίας.<br>Αν έχει τη τιμή 0 τότε παίρνει αυτόματα το πλάτος της κατηγορίας που ανήκει.<br>Αν το πλάτος της κατηγορίας είναι 0 τότε λαμβάνει τιμή που ισούται  [ύψος κατηγορίας * 3]" });

	    tooltip_swf_height = new YAHOO.widget.Tooltip("tooltip_swf_height",
							{ context:"swf_height",
                              autodismissdelay:10000,
							  text:"Ορισμός ύψους SWF ανεξάρτητα από το ύψος της κατηγορίας.<br>Αν έχει τη τιμή 0 τότε παίρνει αυτόματα το ύψος της κατηγορίας που ανήκει.<br>Αν το ύψος της κατηγορίας είναι 0 τότε λαμβάνει τιμή που ισούται  [πλάτος κατηγορίας / 3] " });





    </script>


	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php ($_GET['Action']=='edit') ? getTranslation('Edit Banner') : getTranslation('Add Banner')?></h3>
				<p><?php  ($_GET['Action']=='edit') ? getTranslation('Edit Banner Comments') : getTranslation('Add Banner Comments') ?></p>
                <br />
                <hr width=100% />
                <br />
			</div>
            <div class="cols">

                <?php

                   $machinename = "";
                   $description = "";
                   $active = "";
                   $code = "";
                   $image = "";
                   $text = "";
                   $stylecss = "";
                   $url = "";
                   $startdate = "";
                   $enddate = "";
                   $position = "0";
                   $creationdate = "";
                   $categoryid=$_GET['CategoryID'];
                   $current_position=1;
                   $height=0;
                   $width=0;
                   $swf_flash = 0;
                   $swf_color = "#FFFFFF";
                   $swf_width = 0;
                   $swf_height = 0;


                    if ($_GET['Action']=='edit') {
                            OpenDB();
                            $result_id=QueryDB("SELECT *  FROM ".GetDBPrefix()."banners,".GetDBPrefix()."categories Where cID=bCategoryID AND bID=".$_GET['Record']." Limit 1");
                            $row=GetResultQueryDB($result_id);
                            if ($row) {

                               $machinename = $row['bMachineName'];
                               $description = ($row['bDescription']);
                               $active = $row['bActive'];
                               $code = htmlspecialchars_decode($row['bCode'],ENT_QUOTES);
                               $image = $row['bImage'];
                               $text = ($row['bText']);
                               $stylecss = htmlspecialchars_decode($row['bStyleCSS'],ENT_QUOTES);
                               $url = htmlspecialchars_decode($row['bURL']);
                               $startdate = $row['bStartDate']=='1970-01-01' ? '' : DateFormat("d-m-Y",$row['bStartDate']);
                               $enddate = $row['bEndDate']=='1970-01-01' ? '' : DateFormat("d-m-Y",$row['bEndDate']);
                               $position = $current_position = $row['bPosition'];
                               $creationdate = date("d/m/Y",strtotime ($row['bCreationDate']));
                               $categoryid=$row['bCategoryID'];
                               $swf_flash = $row['bSWF'];
                               $swf_color = $row['bSWFBgColor'];
                               $swf_width = $row['bSWFWidth'];
                               $swf_height = $row['bSWFHeight'];;

                            }
                            CloseDB();
                    }


                    OpenDB();

                    $data="";
                    $newposdata="";

                    if ($_GET['Action']=='add') {
                        $result_id=QueryDB("SELECT max(bPosition) as maxpos  FROM ".GetDBPrefix()."banners WHERE bCategoryID=".$categoryid." LIMIT 1");
                        $row=GetResultQueryDB($result_id);
                        if ($row) {
                           $current_position = $row['maxpos'] + 1;
                           $newposdata = '<option value="'.$current_position.'" selected >'.$current_position.'</option>';
                        }
                        $categoryid=$_GET['CategoryID'];

                    }

                   $result_id=QueryDB("SELECT bPosition  FROM ".GetDBPrefix()."banners WHERE bCategoryID=".$categoryid." ORDER BY bPosition asc");
                   if ($result_id) {
                           while ($row=GetResultQueryDB($result_id)) {
                              $data .= '<option value="'.$row['bPosition'].'" '.( ($current_position==$row['bPosition']) ? 'selected' : '' ).'>'.$row['bPosition'].'</option>';
                           }
                   }

                   $data = $data.$newposdata;





                   if (!IsAdminUser())
                        $UserRestriction = "WHERE cUserID=".get_session("user/id");

                   $result_id=QueryDB("SELECT cDescription,cID,cWidth,cHeight  FROM ".GetDBPrefix()."categories ". $UserRestriction." order by cID desc");
                   $category_data="";

                   if ($result_id) {

                           while ($row=GetResultQueryDB($result_id)) {
                              $category_data .= '<option value="'.$row['cID'].'" '.( ($row['cID']==$categoryid) ? 'selected' : '' ).'>'.rawurldecode($row['cDescription']).'</option>';
                              if  ($row['cID']==$categoryid) {

                                   $height=$row['cHeight'];
                                   $width=$row['cWidth'];


                              }
                           }
                   }
                   CloseDB();

                ?>
            	<form method="post" id="customform" enctype="multipart/form-data">
                    <input type="hidden" id="submittedform" name="submittedform" value="yes" />
                    <input type="hidden" id="recordid" name="recordid" value="<?php echo $_GET['Record'] ?>" />
                    <input type="hidden" id="categoryid" name="categoryid" value="<?php echo $categoryid ?>" />
                    <input type="hidden" id="classtype" name="classtype" value="<?php echo $_GET['Class'] ?>" />
                    <input type="hidden" id="current_position" name="current_position" value="<?php echo $current_position ?>" />
                    <input type="hidden" id="action" name="action" value="<?php echo $_GET['Action'] ?>" />
                    <input type="hidden" id="height" name="height" value="<?php echo $height ?>" />
                    <input type="hidden" id="width" name="width" value="<?php echo $width ?>" />
            		<fieldset class="general">
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
            				<label for="category">*Κατηγορία</label>
                            <select class="dropdown dd_long readonly" name="categoryidselect" id="categoryidselect" size="1" disabled>
                            <?php echo $category_data ?>
                            </select>
            			</div>
            		</fieldset>

            		<fieldset class="contact">
            			<legend>Διαστάσεις</legend>

            			<div>
            				<label for="width">*Πλάτος</label> <input class="inputsmall readonly" type="text" id="width" name="width" value="<?php echo $width ?>" readonly>
            			</div>
            			<div>
            				<label for="height">*Ύψος</label> <input class="inputsmall readonly" type="text" id="height" name="height" value="<?php echo $height ?>" readonly>
            			</div>


            		</fieldset>

            		<fieldset class="properties">
            			<legend>Στοιχεία διαφήμισης</legend>

                        <?php if ($_GET['Class']=="text") { ?>
                        <script type="text/javascript">
                        	tinyMCE.init({
                        		mode : "textareas",
                        		theme : "advanced"
                        	});
                        </script>
            			<div>
            				<label for="text">*Κείμενο</label> <textarea class="input"  id="text" name="text" cols=49 maxlength="120" showremain="limitOne"><?php echo $text ?></textarea>
            			</div>
                        <?php } ?>


                        <?php if ($_GET['Class']=="image") { ?>
                        <div>

            				<label for="swf_flash">SWF Flash</label> <input class="inputtiny" type="checkbox" id="swf_flash" name="swf_flash" <?php echo ($swf_flash==1) ? 'checked' : '' ?>   onclick="javascript:EnableOrDisableFields(this.checked);" >
            			</div>
                        <div>
            				<label for="swf_color">SWF bgcolor Χρώμα</label> <input class="inputsmall" type="text" id="swf_color" name="swf_color" value="<?php echo $swf_color ?>" maxlength="10" <?php echo ($swf_flash==0) ? 'disabled' : '' ?>>
            			</div>
                        <div>
            				<label for="swf_width">SWF Πλάτος </label> <input class="inputsmall" type="text" id="swf_width" name="swf_width" value="<?php echo $swf_width ?>" maxlength="10"  <?php echo ($swf_flash==0) ? 'disabled' : '' ?> onkeypress="editKeyBoard(event,keybNumeric);">
            			</div>
                        <div>
            				<label for="swf_height">SWF Ύψος </label> <input class="inputsmall" type="text" id="swf_height" name="swf_height" value="<?php echo $swf_height ?>" maxlength="10"  <?php echo ($swf_flash==0) ? 'disabled' : '' ?> onkeypress="editKeyBoard(event,keybNumeric);">
            			</div>


            			<div>
                        	<label for="image">*Εικόνα<br /></label><input type="input" class="inputlong readonly" id="image" name="image" value="<?php echo $image ?>" readonly=1>
                            <?php if ($image == "")
                                       $previewimage = "emptyimage.jpg";
                                  else
                                       $previewimage = $image;

                            ?>
                        </div>
                        <div>
                            <label for="image">Προεπισκόπηση</label>
                            <?php if ($swf_flash==1) { ?>
                               <object width="100" height="100">
                               <param name="quality" value="high" />
                               <param name="bgcolor" value="#ffffff" />
                               <param name="movie" value="<?php echo "banners/".$previewimage ?>" >
                               <embed src="<?php echo 'banners/'.$previewimage ?>" width="100" height="100" name="<?php echo $row['bDescription'] ?>" align="" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/flashplayer/"></embed>
                               </object>
                            <?php } else { ?>
                                <img class="imageframe" src="banners/<?php echo $previewimage ?>" width="100px" height="100px">
                            <?php } ?>

                        </div>
                        <div>

            				<label for="image">*Επιλογή εικόνας<br /><span style="font-size:9px;text-align: right">(μέγιστο μέγεθος 3MB)</span><br /><br /></label><input type="file" class="inputlong" accept="image/*"  maxlength="256" id="imageselection" name="imageselection" >
            			</div>
                        <?php } ?>
               		</fieldset>
            		<fieldset class="properties">
            			<legend>Ιδιότητες</legend>
                        <?php if ($_GET['Class']=="code") { ?>
                        <script language="javascript">
                  		editAreaLoader.init({

                  			id: "code"	// id of the textarea to transform
                  			,start_highlight: true	// if start with highlight
                  			,allow_resize: "no"
                  			,allow_toggle: false
                  			,word_wrap: false
                  			,language: "el"
                  			,syntax: "html"
                  			,syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql,basic"
                  			,toolbar: "word_wrap,search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight"
                              ,min_width: 400
                              ,min_height: 300


                  		});
                        </script>
            			<div>
            				<label for="code">Κώδικας</label><textarea id="code" name="code" ><?php echo $code ?></textarea>
            			</div>
                        <?php } ?>


            			<div>
            				<label for="stylecss">Στύλ CSS</label> <input class="inputlong" type="text" id="stylecss" name="stylecss" value="<?php echo $stylecss ?>" maxlength="512">
            			</div>
            			<div>
            				<label for="url">Διεύθυνση URL</label> <input class="inputlong" type="text" id="url" name="url" value="<?php echo $url ?>" maxlength="768">
            			</div>
            			<div>
            				<label for="position">*Θέση στην ιεραρχία</label>
                            <select class="dropdown" name="position" id="position" size="1">
                            <?php echo $data ?>
                            </select>
            			</div>
            		</fieldset>

            		<fieldset class="dateactive">
            			<legend>Ημερολογιακό διάστημα</legend>

            			<div>
            				<label for="startdate">Έναρξη</label><input class="inputmedium" type="text" id="startdate" name="startdate" value="<?php echo $startdate ?>">
                            <img src="images/img.gif" id="f_trigger_a" align="absmiddle" style="cursor: pointer;" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
                            <script type="text/javascript">

                                Calendar.setup({
                                    inputField     :    "startdate",     // id of the input field
                                    ifFormat       :    "%d-%m-%Y",      // format of the input field
                                    button         :    "f_trigger_a",  // trigger for the calendar (button ID)
                                    align          :    "Tl",           // alignment (defaults to "Bl")
                                    singleClick    :    true

                                });

                            </script>

            			</div>
            			<div>
            				<label for="enddate">Τερματισμός</label> <input class="inputmedium" type="text" id="enddate" name="enddate" value="<?php echo $enddate ?>">
                            <img src="images/img.gif" id="f_trigger_b" align="absmiddle" style="cursor: pointer;" title="Date selector" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
                            <script type="text/javascript">

                                Calendar.setup({
                                    inputField     :    "enddate",     // id of the input field
                                    ifFormat       :    "%d-%m-%Y",      // format of the input field
                                    button         :    "f_trigger_b",  // trigger for the calendar (button ID)
                                    align          :    "Tl",           // alignment (defaults to "Bl")
                                    singleClick    :    true

                                });

                            </script>
            			</div>
            		</fieldset>


                    <button type="button" class="button" onclick="javascript:action='editbanner.php?type=edit';checkvalues(); ">Αποθήκευση</button>
                     <?php if ($_GET['Action']=='edit') { ?>
                        <button type="button" class="button" onclick="javascript:action='editbanner.php?type=delete';if (confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή ?')) {action.value='delete';submit();} ">Διαγραφή</button>
                    <?php } ?>
                    <button type="button" class="button" onclick="javascript:window.location='banners.php'; ">Επιστροφή</button>

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

    function enableFields(){

        window.document.forms['customform'].swf_color.disabled=false;
        window.document.forms['customform'].swf_width.disabled=false;
        window.document.forms['customform'].swf_height.disabled=false;

    }

    function disableFields(){

        window.document.forms['customform'].swf_color.disabled=true;
        window.document.forms['customform'].swf_width.disabled=true;
        window.document.forms['customform'].swf_height.disabled=true;

    }

    function EnableOrDisableFields(checkedStatus) {

        if (checkedStatus)
           enableFields();
        else
            disableFields();
    }

	function checkvalues()
	{
	    var classobj = "<?php echo $_GET['Class'] ?>";
        var actiontype="<?php echo $_GET['Action'] ?>";

        if (classobj=="text") {
          window.document.forms['customform'][classobj].value = tinyMCE.get('text').getContent();
        } else if  (classobj=="image") {

           var filepath=window.document.forms['customform']['imageselection'].value;

           if ( (actiontype=="add") ||  ( (actiontype=="edit") && (filepath.length != 0) ) )
                classobj="imageselection";
           else
                classobj="image"; //in this case we are in edit mode without the intenstion to change the image


        } else if (classobj=="code") {
          window.document.forms['customform'][classobj].value=editAreaLoader.getValue("code");
        }

		with(window.document.forms['customform'])
		{

			if ( (machinename.value=='') || (window.document.forms['customform'][classobj].value=='') )
                  alert('Συμπληρώστε όλα τα υποχρεωτικά πεδία');
            else {
                  if (ValidateDates()) {

                      if (classobj=="image") {
                          if (ExtensionsOkay(classobj))
            				  document.forms['customform'].submit();

                      } else
        				  document.forms['customform'].submit();
                  }
            }
        }

	}



    var dtCh= "-";
    var minYear=2010;
    var maxYear=2100;

    function isInteger(s){
    	var i;
        for (i = 0; i < s.length; i++){
            // Check that current character is number.
            var c = s.charAt(i);
            if (((c < "0") || (c > "9"))) return false;
        }
        // All characters are numbers.
        return true;
    }

    function stripCharsInBag(s, bag){
    	var i;
        var returnString = "";
        // Search through string's characters one by one.
        // If character is not in bag, append to returnString.
        for (i = 0; i < s.length; i++){
            var c = s.charAt(i);
            if (bag.indexOf(c) == -1) returnString += c;
        }
        return returnString;
    }

    function daysInFebruary (year){
    	// February has 29 days in any year evenly divisible by four,
        // EXCEPT for centurial years which are not also divisible by 400.
        return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
    }
    function DaysArray(n) {
    	for (var i = 1; i <= n; i++) {
    		this[i] = 31
    		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
    		if (i==2) {this[i] = 29}
       }
       return this
    }

    function isDate(dtStr){
    	var daysInMonth = DaysArray(12)
    	var pos1=dtStr.indexOf(dtCh)
    	var pos2=dtStr.indexOf(dtCh,pos1+1)

    	var strDay=dtStr.substring(0,pos1)
    	var strMonth=dtStr.substring(pos1+1,pos2)
    	var strYear=dtStr.substring(pos2+1)

    	strYr=strYear

    	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
    	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
    	for (var i = 1; i <= 3; i++) {
    		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
    	}
    	month=parseInt(strMonth)
    	day=parseInt(strDay)
    	year=parseInt(strYr)
    	if (pos1==-1 || pos2==-1){
    		alert("Οι ημερομηνίες πρέπει να έχουν τη μορφή ΗΗ-ΜΜ-ΧΧΧΧ.\n\nΑν δεν θέλετε να ορίσετε χρονικά διαστήματα αφήστε και την Έναρξη και τον Τερματισμό κενά.\n\n")
    		return false
    	}
    	if (strMonth.length<1 || month<1 || month>12){
    		alert("Εισάγετε έγκυρο μήνα");
    		return false
    	}
    	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
    		alert("Εισάγετε έγκυρη μέρα")
    		return false
    	}
    	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
    		alert("Παρακαλώ εισάγετε ένα έτος μεταξύ των "+minYear+" and "+maxYear)
    		return false
    	}
    	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
    		alert("Εισάγετε έγκυρη ημέρα")
    		return false
    	}
    return true
    }

    function ValidateDates(){

    	var startdate=window.document.forms['customform']['startdate'];
    	var enddate=window.document.forms['customform']['enddate'];

        if ( (startdate.value != "") || (enddate.value != "") ) {
          	if (isDate(startdate.value)==false){
          		startdate.focus()
          		return false;
          	}

          	if (isDate(enddate.value)==false){
          		enddate.focus()
          		return false;
          	}
        }

        return true
     }



    function ExtensionsOkay(classobj) {
    var extension = new Array();

    // Step 1 of 2:
    // Replace MyForm with the name of your form and
    //    replace FieldName with the upload field name.


    var fieldvalue = window.document.forms['customform'][classobj].value;

    // Step 2 of 2:
    // Add the file name extensions that are okay (with
    //    the period), for the variables with their numbers
    //    in sequential order, as many or as few as needed,
    //    starting with 0. (These are case sensitive.)

    extension[0] = ".png";
    extension[1] = ".gif";
    extension[2] = ".jpg";
    extension[3] = ".jpeg";
    extension[4] = ".ico";
    extension[5] = ".cur";
    extension[6] = ".swf";


    // No other customization needed.
    var thisext = fieldvalue.substr(fieldvalue.lastIndexOf('.'));
    for(var i = 0; i < extension.length; i++) {
    	if(thisext == extension[i]) { return true; }
    	}
    alert("Επιλέξτε μόνον εικόνες *.gif, *.png, *.jpg, *.jpeg");
    return false;
    }

    function ok(maxchars,classobj) {
         if(window.document.forms['customform'][classobj].value.length > maxchars) {
           alert('Too much data in the text box! Please remove '+
            (window.document.forms['customform'][classobj].value.length - maxchars)+ ' characters');
           return false; }
         else
           return true;
     }


</script>
