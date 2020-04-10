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
<title>Statistics</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="build/datatable/assets/skins/sam/datatable.css" />
<link rel="stylesheet" type="text/css" href="build/paginator/assets/skins/sam/paginator.css" />
<link rel="stylesheet" type="text/css" href="build/button/assets/skins/sam/button.css" />

<script type="text/javascript" src="build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="build/element/element-min.js"></script>
<script type="text/javascript" src="build/button/button-min.js"></script>
<script type="text/javascript" src="build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="build/datatable/datatable-min.js"></script>
<script type="text/javascript" src="build/connection/connection-min.js"></script>
<script type="text/javascript" src="build/paginator/paginator-min.js"></script>
<script type="text/javascript" src="build/json/json-min.js"></script>


<script type="text/javascript" src="javascript/dateformat.js"></script>

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



.more-button {
	cursor:pointer;
	background: #FFFFCC url(images/edit.png) no-repeat center center;
	width:16px;height:16px;
}



</style>

</head>

<body class="yui-skin-sam">
    <?php include 'includes/navigation.php' ?>
    <?php include 'includes/header.php' ?>

	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php getTranslation('Statistics') ?></h3>
				<p><?php getTranslation('StatisticsList') ?> </p>
                <br />
                <hr width=100% />
                <br />
			</div>
            <div class="tablereport">

                   <div id="report" align="center"></div>



                      <script type="text/javascript">


                      parseSQLDate = function(ISOdate,output)
                      {


                          var time = '';
                          var pos = ISOdate.indexOf('-');
                          var year = ISOdate.substr(0,pos);

                          var pos2 = ISOdate.indexOf('-',pos+1);
                          var month =  ISOdate.substr(pos+1,(pos2-pos-1));

                          pos = ISOdate.indexOf(' ');
                          if (pos != -1) {
                              var day =  ISOdate.substr(pos2+1,(pos-pos2-1));
                              time = ISOdate.substr(pos+1);

                          }
                          else
                              var day =  ISOdate.substr(pos2+1);




                          if (pos != -1) {
                              var jsdate = new Date (month+"/"+day+"/"+year+' '+ time);
                              var formatedDateTime = dateFormat(jsdate,"dd/mm/yyyy HH:MM");

                          }
                          else {
                              var jsdate = new Date (month+"/"+day+"/"+year);
                              var formatedDateTime = dateFormat(jsdate,"dd/mm/yyyy");
                          }

                          if (output==1)
                              return jsdate;
                          else
                              return  formatedDateTime;

                      };


                      var myRequestBuilder = function(oState, oSelf) {
                          // Get states or use defaults

                          oState = oState || { pagination: null, sortedBy: null };
                          var sort = (oState.sortedBy) ? oState.sortedBy.key : "sDate";
                          var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";
                          var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
                          var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100;

                          // Build custom request
                          return  "query=statistics_details" +
                                  "&type=select" +
                                  "&BannerID=<?php echo $_GET['BannerID'] ?>" +
                                  "&column=" + sort +
                                  "&dir=" + dir +
                                  "&pageStart=" + startIndex +
                                  "&Recs=" + results;

                      };

                      YAHOO.util.Event.addListener(window, "load", function() {
                          YAHOO.example.report = function() {


                              var DataSource = new YAHOO.util.DataSource("includes/data.php?");
                              DataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
                              DataSource.connXhrMode = "queueRequests";

                              var ColumnDefs = [
                                  {key:"sID", label:"StatisticsID",hidden:true, isPrimaryKey:true},
                                  {key:"sBannerID", label:"BannerID",hidden:true,},
                                  {key:"cID", label:"CategoryID",hidden:true},
                                  {key:"bDescription",label:"Περιγραφή διαφήμισης", width:150, sortable:true, resizeable:true},
                                  {key:"sDate",label:"Ημερομηνία", formatter:"date", width:120, sortable:true, resizeable:true},
                                  {key:"sIP",label:"Διεύθυνση IP", width:200, sortable:true, resizeable:true},
                                  {key:"sClicksSUM",label:"Clicks", width:50, sortable:true, resizeable:true},
                                  {key:"sImpressionsSUM",label:"Εμφανίσεις", width:60, sortable:true, resizeable:true}


                              ];


                              DataSource.responseSchema = {
                                  resultsList: "DataSet.items",
                                  // Use the parse methods to populate the RecordSet with the right data types
                                  fields: [
                                  {key:"sID", parser:"number"},
                                  {key:"sBannerID", parser:"number"},
                                  {key:"cID", parser:"number"},
                                  {key:"bDescription", parser:"string"},
                                  {key:"sDate",parser:function (oDate){return parseSQLDate(oDate,2);}},
                                  {key:"sIP", parser:"string"},
                                  {key:"sClicksSUM", parser:"number"},
                                  {key:"sImpressionsSUM", parser:"number"}
                                  ],

                                   metaFields: {totalRecords: "DataSet.totalRecords"}
                              };

                              var oConfigs = {

                                      dateOptions:{format:"%d/%m/%Y"},
                                      selectionMode:"single",
                                      numberOptions:{decimalPlaces:2, thousandsSeparator:","},
                                      MSG_EMPTY:'<?php getTranslation("no records") ?>',
                                      draggableColumns:false,
                                      initialRequest:"query=statistics_details&type=select&BannerID=<?php echo $_GET['BannerID'] ?>&column=sDate&dir=desc&pageStart=0&Recs=25",
                                      paginator: new YAHOO.widget.Paginator(
                                      { rowsPerPage:25,
                                        rowsPerPageOptions: [25, 50, 100],
                                        previousPageLinkLabel : "προηγούμενη",
                                        nextPageLinkLabel : "επόμενη",
                                        firstPageLinkLabel : "πρώτη",
                                        lastPageLinkLabel : "τελευταία"
                                      }),


                  	                  dynamicData : true,
                                      generateRequest: myRequestBuilder

                  	        };

                            DataTable = new YAHOO.widget.DataTable("report", ColumnDefs, DataSource, oConfigs  );

                            // Update totalRecords on the fly with value from server
                            DataTable.handleDataReturnPayload = function (oRequest, oResponse, oPayload) {
                                oPayload.totalRecords = oResponse.meta.totalRecords;
                                return oPayload;
                            }





                            var i=1,bReverseSorted = false;

                            // Track when Column is reverse-sorted, since new data will come in out of order
                            var trackReverseSorts = function(oArg) {
                                bReverseSorted = (oArg.dir === YAHOO.widget.DataTable.CLASS_DESC);
                            };

                          // Set up editing flow
              	          var highlightEditableCell = function(oArgs) {
              	            var elCell = oArgs.target;
              	            if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {
              	                this.highlightCell(elCell);
              	            }
              	         };


                          DataTable.subscribe("cellMouseoverEvent", highlightEditableCell);
              	          DataTable.subscribe("cellMouseoutEvent", DataTable.onEventUnhighlightCell);
                          DataTable.subscribe("cellClickEvent", DataTable.onEventShowCellEditor);





                          var myBuildUrl = function(datatable,record) {
                              var url = '';
                              var cols = datatable.getColumnSet().keys;
                              for (var i = 0; i < cols.length; i++) {
                                  if (cols[i].isPrimaryKey) {
                                      url += '&id=' + escape(record.getData(cols[i].key));
                                      url += '&primaryKey=' + cols[i].key;
                                  }
                              }

                              return url;
                          };




                              return {
                                  oDS: DataSource,
                                  oDT: DataTable
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

