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
<title>Home page</title>
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
				<h3><?php getTranslation('Welcome to our website!') ?></h3>
				<p> Λίστα των top-10 διαφημίσεων με τα περισσότερα κλικ.</p>
			</div>
            <div class="tablereport">
                  <br />
                   <div id="report" align="center"></div>



                      <script type="text/javascript">
                      YAHOO.util.Event.addListener(window, "load", function() {
                          YAHOO.example.report = function() {


                              var myDataSource = new YAHOO.util.DataSource("includes/data.php?");
                              myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
                              myDataSource.connXhrMode = "queueRequests";

                              var myColumnDefs = [
                                  {key:"bID", label:"BannerID",hidden:true},
                                  {key:"bDescription",label:"Περιγραφή Banner", width:250, sortable:true, resizeable:true},
                                  {key:"cDescription",label:"Περιγραφή Κατηγορίας", width:250, sortable:true, resizeable:true},
                                  {key:"sum_clicks",label:"Κλικς", width:50, sortable:true, resizeable:true},
                                  {key:"sum_impressions",label:"Εμφανίσεις", width:80, sortable:true, resizeable:true},
                              ];


                              myDataSource.responseSchema = {
                                  resultsList: "DataSet.items",
                                  // Use the parse methods to populate the RecordSet with the right data types
                                  fields: [
                                  {key:"bID", parser:"number"},
                                  {key:"bDescription", parser:"string"},
                                  {key:"cDescription", parser:"string"},
                                  {key:"sum_clicks", parser:"number"},
                                  {key:"sum_impressions", parser:"number"}
                                  ],

                                   metaFields: {totalRecords: "DataSet.totalRecords"}
                              };

                              var oConfigs = {

                                      dateOptions:{format:"%d/%m/%Y"},
                                      selectionMode:"single",
                                      numberOptions:{decimalPlaces:2, thousandsSeparator:","},
                                      MSG_EMPTY:'<?php getTranslation("no records") ?>',
                                      draggableColumns:false,
                                      initialRequest:"query=top10banners&type=select",
                                      caption:'<?php echo "Τελευταία ενημέρωση έγινε στις ".GetLastUpdated() ?>'
                  	        };

                            myDataTable = new YAHOO.widget.DataTable("report", myColumnDefs, myDataSource, oConfigs  );


                              return {
                                  oDS: myDataSource,
                                  oDT: myDataTable
                              };
                          }();
                      });
                      </script>

            </div>

			<div class="clear"></div>
		</div>
	</div>

    <?php include 'includes/footer.php' ?>
</body>

</html>
