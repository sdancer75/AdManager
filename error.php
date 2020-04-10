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
<title>Error</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="build/datatable/assets/skins/sam/datatable.css" />

<script type="text/javascript" src="build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="build/element/element-min.js"></script>
<script type="text/javascript" src="build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="build/datatable/datatable-min.js"></script>
<script type="text/javascript" src="build/connection/connection-min.js"></script>
<script type="text/javascript" src="build/json/json-min.js"></script>

<style type="text/css">
/* custom styles for this example */
.yui-skin-sam .yui-dt-liner { white-space:nowrap; }

.yui-skin-sam .yui-dt tr.yui-dt-last td,
.yui-skin-sam .yui-dt th,
.yui-skin-sam .yui-dt td {
    border-bottom: 1px solid #7f7f7f;
    text-align: center;
    height: 30px;
}

/* custom styles for this example */
.yui-skin-sam .yui-dt-body { cursor:pointer; } /* when cells are selectable */
#cellrange, #singlecell { margin-top:2em; }



/* custom styles for this example */
.modform {margin-bottom: 1em;}
.index {width:5em;}

.myHighlightClassName {
    background-color: #FFCA95;
    Color:black;
}



.yui-skin-sam .yui-checkbox-button button {
    width: 4em;
}

.yui-button#pushbuttonDelRec button {

    padding-left: 3em;
    background: url(../grafix/delete.gif) 10% 50% no-repeat;


}

</style>

</head>

<body class="yui-skin-sam">
    <?php include 'includes/navigation.php' ?>
    <?php include 'includes/header.php' ?>

	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php getTranslation('Error page') ?></h3>
				<p><?php getTranslation('Error description') ?></p>
			</div>
            <div class="tablereport">
                  <br />
                   <div id="report" align="center"></div>
                    <?php

                      $error_type = $_GET['error_type'];

                      switch (intval($error_type)) {
                              case 1: $msg = 'Το αρχείο που επιλέξατε δεν έχει επέκταση gif, png, jpg, jpeg.<br><br>Αν επιλέξατε αρχείο <u>SWF Flash</u>, τότε δεν τσεκάρατε την αντίστοιχη επιλογή από το πλαίσιο [Στοιχεία διαφήμισης].';break;
                              case 2: $msg = 'Το μέγεθος του αρχείου είναι μεγαλύτερο από 3MΒ.';break;
                              case 3: $msg = 'Το αρχείο δεν αντιγράφτηκε για άγνωστους λόγους. Ελέγξτε τη διαδρομή ή τα δικαιώματα πρόσβασης.';break;
                              case 4: $msg = 'Το αρχείο που επιλέχθηκε είτε δεν είναι η εικόνα ή προέκυψε άλλο σφάλμα.';break;
                              case 5: $msg = 'Επιλέξατε αρχείο Flash (*.swf) αλλά δεν στεκάρατε την αντίστοιχη επιλογή.';break;
                              default : $msg = 'Άγνωστο';break;

                      }
                    ?>
                    <h1>Σφάλμα : <strong><?php echo $msg ?></strong></h1>
                    <br /><br /><br /><br /><br /><br />
                    <p>
                    <?php
                      $url = 'editbanner.php?Action='.$_GET['Action'].'&Record='.$_GET['Record'].'&Class='.$_GET['Class'].'&CategoryID='.$_GET['CategoryID'];
                    ?>
                    <a class="link" href="<?php echo $url ?>">Επιστροφή στην προηγούμενη φόρμα</a>
                    </p>

            </div>

			<div class="clear"></div>
		</div>
	</div>

    <?php include 'includes/footer.php' ?>
</body>

</html>
