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
<title>Banners List</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="css/styles.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="build/datatable/assets/skins/sam/datatable.css" />
<link rel="stylesheet" type="text/css" href="build/paginator/assets/skins/sam/paginator.css" />
<link rel="stylesheet" type="text/css" href="build/button/assets/skins/sam/button.css" />
<link rel="stylesheet" type="text/css" href="build/menu/assets/skins/sam/menu.css" />
<link rel="stylesheet" type="text/css" href="build/container/assets/skins/sam/container.css" />


<script type="text/javascript" src="build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="build/container/container-min.js"></script>
<script type="text/javascript" src="build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="build/element/element-min.js"></script>
<script type="text/javascript" src="build/button/button-min.js"></script>
<script type="text/javascript" src="build/menu/menu-min.js"></script>
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

.delete-button {
	cursor:pointer;
	background: #FFFFCC url(images/delete.png) no-repeat center center;
	width:16px;height:16px;
}


.edit-button {
	cursor:pointer;
	background: #FFFFCC url(images/edit.png) no-repeat center center;
	width:16px;height:16px;
}

.yui-button#pushbuttonAddLocalRec button {

    padding-left: 1em;
    padding-right: 1em;
    width: 14em;
    height: 2em;
    font-size : 12px;
    background: url(images/add.png) 10% 50% no-repeat;

}

.yui-button#Menu_categories button {

    padding-left: 1em;
    padding-right: 1em;
    width: 24em;
    height: 2em;
    font-size : 12px;
    background: url(images/filter.png) 10px 50% no-repeat;

}



</style>

</head>

<body class="yui-skin-sam">
    <?php include 'includes/navigation.php' ?>
    <?php include 'includes/header.php' ?>

	<div id="content">
		<div id="content-wrap">
			<div class="welcome">
				<h3><?php getTranslation('Banners') ?></h3>
				<p><?php getTranslation('BannersList') ?> </p>
                <br />
                <hr width=100% />
                <br />
			</div>
            <div class="tablereport">
                  <span id="pushbuttonAddLocalRec" class="yui-button yui-push-button">
                  <strong class="first-child">
                    <button type="button" name="pushbuttonAddLocalRec">Νέα Διαφήμιση</button>
                  </strong>
                  </span>


                      <?php

                           //save the userbetssynopsis table and get the primary ID
                           $myDB = mysql_connect(MySQLIP, Username, Password) or die(mysql_error());
                           mysql_select_db(DataBase, $myDB) or die(mysql_error());
                       	   mysql_query("SET NAMES 'utf8'");

                           echo '<input type="button" id="Menu_categories" name="Menu_categories" value="Όλες οι κατηγορίες">';
                           echo '<select id="categoriesmenu" name="categoriesmenu">';
                           echo '<option value="*">Όλες οι κατηγορίες</option>';
                           if (!IsAdminUser()) {
                                $filter = " cUserID=".$_SESSION['user/id'];
                           } else
                                $filter = " 1=1";
                           $result_id = mysql_query("select cDescription,cID from ".GetDBPrefix()."categories where ".$filter." order by cDescription ASC");
                           while ($row = mysql_fetch_assoc($result_id)) {
                              echo '<option value="'.$row['cID'].'">'.$row['cDescription'].'</option>';
                           }
                           echo '</select>';
                           mysql_close($myDB);



                      ?>



                    <br /> <br />

                   <div id="report" align="center"></div>



                   <script type="text/javascript">

                    var DataTable;




                     var ajaxLoadingPanel = new YAHOO.widget.Panel('ajaxLoadPanel', {
                          width:"240px",
                          fixedcenter:true,
                          close:false,
                          draggable:false,
                          modal:true,
                          visible:false
                          //effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}
                      }
                      );

                      ajaxLoadingPanel.setHeader('Παρακαλώ περιμένετε...');
                      ajaxLoadingPanel.setBody('<img src="images/ajax-loader.gif" />');
                      ajaxLoadingPanel.render(document.body);
                      ajaxLoadingPanel.bringToTop();




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


                      function onAddRec(p_oEvent) {

                               window.location="bannertype.php?Action=add&Record=-1";

                      }

                      	var onMenu_categories_click = function (p_sType, p_aArgs) {

                        	var oEvent = p_aArgs[0],	//	DOM event
                        		oMenuItem = p_aArgs[1];	//	MenuItem instance that was the target of the event

                        	if (oMenuItem) {
                        	  //oMenuItem.cfg.getProperty("text")
                              // oMenuItem.value

                                oMenu_categories.set("label",oMenuItem.cfg.getProperty("text") );

                                ajaxLoadingPanel.show();

                                // Sends a request to the DataSource for more data
                                var oUpdateTableCallBack = {
                                    success :function (oRequest , oResponse , oPayload) {

                                    				DataTable.onDataReturnReplaceRows(oRequest,oResponse,oPayload);
                                                    DataTable.set('sortedBy', null);
                                    				ajaxLoadingPanel.hide();

                                				},
                                    failure :DataTable.onDataReturnReplaceRows,
                                    scope   :DataTable,
                                    argument: DataTable.getState()
                                };


                                var sqlquery = "query=banners&type=select&whereID=" + oMenuItem.value;
                                DataTable.getDataSource().sendRequest(sqlquery, oUpdateTableCallBack);

                            }
                      }

                      var οPushButtonAddLocalRec = new YAHOO.widget.Button("pushbuttonAddLocalRec");
                      οPushButtonAddLocalRec.on("click", onAddRec);


              		  var oMenu_categories = new YAHOO.widget.Button("Menu_categories",{ type: "menu", menu: "categoriesmenu" });
                      oMenu_categories.getMenu().subscribe("click", onMenu_categories_click);


                       var CustomformatDate = function(elCell, oRecord, oColumn, oData) {


                                var formatted_value;
                                if (oData=='01/01/1970') {

                                   formatted_value = 'Κανένα';

                                } else
                                    formatted_value = oData;


                                elCell.innerHTML = formatted_value;


                       };

                      YAHOO.util.Event.addListener(window, "load", function() {
                          YAHOO.example.report = function() {


                              var DataSource = new YAHOO.util.DataSource("includes/data.php?");
                              DataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
                              DataSource.connXhrMode = "queueRequests";

                              var ColumnDefs = [
                                  {key:"bID", label:"CategoryID",hidden:true, isPrimaryKey:true},
                                  {key:"bContentType",label:"Type", width:40, hidden:true},
                                  {key:'edit', label:'', className: 'edit-button', action:'edit', width:15},
                                  {key:"cDescription",label:"Κατηγορία", width:150, sortable:true, resizeable:true},
                                  {key:"bDescription",label:"Περιγραφή", width:150, sortable:true, resizeable:true},
                                  {key:"bStartDate",label:"Αρχή", formatter:CustomformatDate, width:90, sortable:true, resizeable:true},
                                  {key:"bEndDate",label:"Τέλος", formatter:CustomformatDate, width:90, sortable:true, resizeable:true},
                                  {key:"bPosition",label:"Θέση", width:50, sortable:true, resizeable:true},
                                  {key:"bActive",label:"Ενεργό", formatter:YAHOO.widget.DataTable.formatCheckbox, width:50, sortable:true, resizeable:true},
                                  {key:'del', label:'', className: 'delete-button', action:'delete', width:15}
                              ];


                              DataSource.responseSchema = {
                                  resultsList: "DataSet.items",
                                  // Use the parse methods to populate the RecordSet with the right data types
                                  fields: [
                                  {key:"bID", parser:"number"},
                                  {key:"bMachineName", parser:"string"},
                                  {key:"cDescription", parser:"string"},
                                  {key:"bDescription", parser:"string"},
                                  {key:"bURL", parser:"number"},
                                  {key:"bStartDate",parser:function (oDate){return parseSQLDate(oDate,2);}},
                                  {key:"bEndDate" ,parser:function (oDate){return parseSQLDate(oDate,2);}},
                                  {key:"bPosition", parser:"number"},
                                  {key:"bActive", parser:"number"},
                                  {key:"bContentType", parser:"number"}
                                  ],

                                   metaFields: {totalRecords: "DataSet.totalRecords"}
                              };

                              var oConfigs = {

                                      dateOptions:{format:"%d/%m/%Y"},
                                      selectionMode:"single",
                                      numberOptions:{decimalPlaces:2, thousandsSeparator:","},
                                      MSG_EMPTY:'<?php getTranslation("no records") ?>',
                                      draggableColumns:false,
                                      initialRequest:"query=banners&type=select",
                                      paginator: new YAHOO.widget.Paginator(
                                      { rowsPerPage:30,
                                        rowsPerPageOptions: [25, 50, 100],
                                        previousPageLinkLabel : "προηγούμενη",
                                        nextPageLinkLabel : "επόμενη",
                                        firstPageLinkLabel : "πρώτη",
                                        lastPageLinkLabel : "τελευταία"
                                      }),


                  	                  dynamicData : false

                  	        };

                            DataTable = new YAHOO.widget.DataTable("report", ColumnDefs, DataSource, oConfigs  );

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

                		  DataTable.subscribe('cellClickEvent',function(oArgs) {


                                var target = oArgs.target;

                                column = this.getColumn(target);
                                record = this.getRecord(target);



                                switch (column.action) {

                                 case 'delete' :

                                            if (confirm('Είσαι σίγουρος για τη διαγραφή ;')) {

                                                var record = this.getRecord(target);

                                                 YAHOO.util.Connect.asyncRequest('GET','includes/data.php?query=banners&type=delete' + myBuildUrl(this,record),
                                                    {

                                                        success: function (o) {
                                                                this.deleteRow(target);


                                                                // Update the whole table, to get back the new positions
                                                                var oUpdateTableCallBack = {
                                                                    success :DataTable.onDataReturnReplaceRows,
                                                                    failure :DataTable.onDataReturnReplaceRows,
                                                                    scope   :DataTable
                                                                };

                                                               var sqlquery = "query=banners&type=select";
                                                               DataTable.getDataSource().sendRequest(sqlquery, oUpdateTableCallBack);
                                                               
                                                        },
                                                        failure: function (o) {
                                                                alert('Κάποιο σφάλμα δημιουργήθηκε');
                                                        },
                                                        scope:DataTable
                                                    }

                                                );


                                            }
                                            break;
                                 case 'edit' :

                                             switch (record.getData(this.getColumn('bContentType').key)) {
                                                 case 0: contenttype="&Class=text&";break;
                                                 case 1: contenttype="&Class=image&";break;
                                                 case 2: contenttype="&Class=code&";break;
                                                 default:contenttype="&Class=unknown&";
                                             }

                                             window.location="editbanner.php?Action=edit"+contenttype+"Record="+record.getData(this.getColumn('bID').key);


                                            break;
                                  default:DataTable.onEventShowCellEditor(oArgs);
                                            break;
                                }

                          })


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
