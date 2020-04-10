<?php
/*************************************************************************************
Created by G. Papaioannou
You can contact with me at g_papaioannou@rocketmail.com

Paradox Interactive (c) 2011,Greece
http://www.paradoxinteractive.gr
**************************************************************************************/


  session_start();


  include_once 'config.php';
  include_once 'functions.php';



$table=$_GET['query'];
$type=$_GET['type'];
$record=$_GET['id'];
$primaryKey=$_GET['primaryKey'];
$whereID=$_GET['whereID'];

/*************** Functions List**********************************************/


/******************************************************************************
 function convert_2_json($result_id)

returns the sql query result in json format.


******************************************************************************/
function convert_2_json($result_id,$result_id_num_total_recs=false)
{


      //******************** Column Definition *************************************
      if ($result_id)  {

         //here return the table fields, column names
         $column_count = mysql_num_fields($result_id) or die("display_db_query:" . mysql_error());
         $first = 0;



          while ($row = mysql_fetch_row($result_id)) {

              for ($column_num = 0; $column_num < $column_count; $column_num++) {

                  $field_name = mysql_field_name($result_id, $column_num);



                  if($first==0){
                        $field_value .= ($column_num == 0)?"{" :" ";
                        $first = 1;
                  }
                  else {
                        $field_value .= ($column_num == 0)?",{" :" ";
                  }


                  if ($field_name == 'sIP') {
                        $colum_value = ($row[$column_num]==nil || $row[$column_num]=="") ? "\"\"" : "\"".IPInfo(htmlspecialchars($row[$column_num]),1)."\"";
                  } else {
                        $colum_value = ($row[$column_num]==nil || $row[$column_num]=="") ? "\"\"" : "\"".htmlspecialchars($row[$column_num])."\"";
                  }
                  $field_value .= "\"".$field_name."\":".$colum_value;
                  $field_value .= (($column_num + 1 ) == $column_count)?" } ":",";

              }


           }

           //create envelop

           if ($result_id_num_total_recs) {

            $row = mysql_fetch_row($result_id_num_total_recs);

            $totalrecs = $row[0];
            $field_value = "{\"DataSet\": {\"totalRecords\":".$totalrecs.",\"items\":[".$field_value."]}}";
           } else
            $field_value = "{\"DataSet\": {\"totalRecords\":".mysql_num_rows ($result_id).",\"items\":[".$field_value."]}}";


           return $field_value;


       }  else

          return '{"DataSet": {"totalRecords":0,"items":[]}}';

}







/***************************************************************/








    //enable the Stored Procedures calls
    $DB = mysql_connect(MySQLIP, Username, Password,false,65536) or die(mysql_error());

    mysql_select_db(DataBase, $DB) or die(mysql_error());
	mysql_query("SET NAMES 'utf8'");






       $UserRestriction = "";

       //**********************************************************************************************************************
       //****************************************** SELECT ********************************************************************
       //**********************************************************************************************************************


       if (strcasecmp($type,"select") ==0 ) {

        if (strcasecmp($table, "top10banners") == 0) {

            if (!IsAdminUser())
                 $UserRestriction = "AND cUserID=".get_session("user/id");


            $query = "SELECT ".$DB_Prefix."banners.bDescription,".$DB_Prefix."categories.cDescription,".$DB_Prefix."statistics.sBannerID, sum(sClicks) as sum_clicks, sum(sImpressions) as sum_impressions FROM "
                      .$DB_Prefix."banners,".$DB_Prefix."categories,".$DB_Prefix."statistics WHERE cID=bCategoryID AND sBannerID=bID ".$UserRestriction." Group by bID order by sum_clicks desc LIMIT 10";
  			$result_id = mysql_query($query);
            if (!$result_id) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $query;
                die($message);
            }
  	        $MYDATASOURCE = convert_2_json($result_id);
  	        echo $MYDATASOURCE;


        } else if (strcasecmp($table, "categories") == 0) {

            if (!IsAdminUser())
                 $UserRestriction = "WHERE cUserID=".get_session("user/id");

            $query = "SELECT ".$DB_Prefix."categories.* FROM ".$DB_Prefix."categories ".$UserRestriction." order by cCreationDate desc";
  			$result_id = mysql_query($query);
            if (!$result_id) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $query;
                die($message);
            }
  	        $MYDATASOURCE = convert_2_json($result_id);
  	        echo $MYDATASOURCE;

        } else if (strcasecmp($table, "banners") == 0) {

            if (!IsAdminUser())
                 $UserRestriction = "AND cUserID=".get_session("user/id");


            if ( ($whereID=='*') || ($whereID=='') )
                $filter="";
            else
                $filter = " AND cID=".$whereID;


            $query = "SELECT bID,bMachineName,bDescription,bURL,bStartDate,bEndDate,bPosition,bActive,bContentType,".$DB_Prefix."categories.* FROM ".$DB_Prefix."banners,".$DB_Prefix."categories WHERE cID=bCategoryID ".$filter.' '.$UserRestriction." order by bPosition Asc";

            $result_id = mysql_query($query);
            if (!$result_id) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $query;
                die($message);
            }
  	        $MYDATASOURCE = convert_2_json($result_id);
  	        echo $MYDATASOURCE;

        } else if (strcasecmp($table, "statistics_by_banner") == 0) {

            if (!IsAdminUser())
                 $UserRestriction = "AND cUserID=".get_session("user/id");

            $query = "SELECT ".$DB_Prefix."banners.bDescription,".$DB_Prefix."categories.cID,".$DB_Prefix."statistics.sBannerID, sum(sClicks) as sum_clicks, sum(sImpressions) as sum_impressions FROM "
                      .$DB_Prefix."banners,".$DB_Prefix."categories,".$DB_Prefix."statistics WHERE cID=bCategoryID AND sBannerID=bID ".$UserRestriction." Group by bID order by sum_clicks desc";
  			$result_id = mysql_query($query);
            if (!$result_id) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $query;
                die($message);
            }
  	        $MYDATASOURCE = convert_2_json($result_id);
  	        echo $MYDATASOURCE;

        } else if (strcasecmp($table, "statistics_details") == 0) {

            if (!isset($_GET['pageStart']) or !is_numeric($_GET['Recs'])) {
              $StartRow = 0;
              $Recs = 25;
            } else  {
              $StartRow = intval($_GET['pageStart']);
              $Recs = intval($_GET['Recs']);
            }

            if (!IsAdminUser())
                 $UserRestriction = "AND cUserID=".get_session("user/id");

            $query = "SELECT SQL_CALC_FOUND_ROWS ".$DB_Prefix."banners.bDescription,".$DB_Prefix."categories.cID, SUM(".$DB_Prefix."statistics.sImpressions) as sImpressionsSUM, SUM(".$DB_Prefix."statistics.sClicks) as sClicksSUM,".$DB_Prefix."statistics.sIP,".$DB_Prefix."statistics.sDate FROM "
                      .$DB_Prefix."banners,".$DB_Prefix."categories,".$DB_Prefix."statistics WHERE cID=bCategoryID AND sBannerID=bID AND sBannerID=".$_GET['BannerID']." ".$UserRestriction." Group By sIP,DATE(sDate) order by ".$_GET['column']." ".$_GET['dir']." LIMIT ".$StartRow.",".$Recs;

            //echo $query;
  			$result_id = mysql_query($query);
            if (!$result_id) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $query;
                die($message);
            }
            $result_id_num_total_recs = mysql_query("SELECT FOUND_ROWS()");
  	        $MYDATASOURCE = convert_2_json($result_id,$result_id_num_total_recs);
  	        echo $MYDATASOURCE;
        } else if (strcasecmp($table, "users") == 0) {

            if (!IsAdminUser())
                 $UserRestriction = " WHERE uID=".get_session("user/id");


            $query = "SELECT ".$DB_Prefix."users.* FROM ".$DB_Prefix."users ".$UserRestriction." order by uUsername desc";
  			$result_id = mysql_query($query);
            if (!$result_id) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $query;
                die($message);
            }
  	        $MYDATASOURCE = convert_2_json($result_id);
  	        echo $MYDATASOURCE;
        }


       //**********************************************************************************************************************
       //****************************************** DELETE ********************************************************************
       //**********************************************************************************************************************


      }  else if ($type=="delete") {

           //if banner, delete the file first
           if ($primaryKey=='bID')  {

               $query = "SELECT bImage,bPosition,bCategoryID FROM ".$DB_Prefix.$table." WHERE ".$primaryKey."=".$record;
      		   $result_id = mysql_query($query);
               if (!$result_id) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $query;
                    die($message);
               }
               $row = mysql_fetch_assoc($result_id);

               If ($row) {
                    if ($row['bImage'] != '')
                        unlink($_SERVER['DOCUMENT_ROOT']."\\adm\\banners\\".$row['bImage']);
               }

               $query = 'UPDATE '.$DB_Prefix.$table.' SET bPosition=bPosition-1 WHERE bPosition > '.$row['bPosition'].' AND bCategoryID='.$row['bCategoryID'];

      		   $result_id = mysql_query($query);
               if (!$result_id) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $query;
                    die($message);
               }

           }

           //if category, delete all the files first
           if ($primaryKey=='cID')  {

               $query = "SELECT bImage FROM ".$DB_Prefix.$table.",".$DB_Prefix."banners WHERE bCategoryID=".$record." AND ".$primaryKey."=".$record;
      		   $result_id = mysql_query($query);
               if (!$result_id) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $query;
                    die($message);
               }
               while ($row = mysql_fetch_assoc($result_id)){

                    if ($row['bImage'] != '')
                        unlink($_SERVER['DOCUMENT_ROOT']."\\adm\\banners\\".$row['bImage']);
               }

           }

           // dont delete the admin account (uID=1)
           if ( ($primaryKey=='uID') && ($record==1) ) {

                return;
           }

           $query = "DELETE FROM ".$DB_Prefix.$table." WHERE ".$primaryKey."=".$record;
  		   $result_id = mysql_query($query);
           if (!$result_id) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $query;
                die($message);
           }


      }




   mysql_close($DB);


?>