<?php
/*************************************************************************************
Created by G. Papaioannou
You can contact with me at g_papaioannou@rocketmail.com

Paradox Interactive (c) 2011,Greece
http://www.paradoxinteractive.gr
**************************************************************************************/

include('ipinfodb.class.php');


/***********************************
function OpenDB()

Opens DB

************************************/
function OpenDB() {

    global $DB_Prefix;
    global $DB;
    //enable the Stored Procedures calls
    $DB = mysql_connect(MySQLIP, Username, Password,false,65536) or die(mysql_error());

    mysql_select_db(DataBase, $DB) or die(mysql_error());
	mysql_query("SET NAMES 'utf8'");

}


/***********************************
function CloseDB()

Close DB

************************************/
function CloseDB() {
     global $DB;

     mysql_close($DB);
}

/****************************************************************************
 function QueryDB($query)

 Execute a query and returns the result

 Remeber to Open and Close the DB first with OpenDB() and CloseDB()

*****************************************************************************/
function QueryDB($query) {

    $result_id = mysql_query($query);
    if (!$result_id) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }

    if (is_bool($result_id))
       return true;
    else
       return $result_id;
}


/****************************************************************************
 function GetResultQueryDB($query)

Receives the result from the previously called QueryDB($query) function
It is not needed if you call query with delete, update etc.

*****************************************************************************/
function GetResultQueryDB($result_id) {


    if (!$result_id) {
        $message  = 'Invalid handler: ' . mysql_error() . "\n";
        die($message);
    }

    return mysql_fetch_assoc($result_id);
}

/************************************
function GetDBPrefix()

Get the prefix of that DB tables ie adm_
*************************************/
function GetDBPrefix() {

    global $DB_Prefix;

    return $DB_Prefix;
}


/**************************************************
function UpdateDB($table,$tablekey,$recordid,$params)

Update a record

$table = table name without the prefix
$tablekey = the primary key of the table
$recordid = # of the record
$params = string parameters

***************************************************/
function UpdateDB($table,$tablekey,$recordid,$params) {

     OpenDB();

     // if recordid==-1 means that we are trying to insert a record.
     // do a dummy insert and then update.
     if ($recordid == -1) {
       QueryDB('INSERT INTO '.GetDBPrefix().$table.' SET '.$params);
       $recordid = mysql_insert_id();
     } else {
         QueryDB('UPDATE '.GetDBPrefix().$table.' SET '.$params.' WHERE '.$tablekey.'='.$recordid);
     }


     CloseDB();
}


/**************************************************
function UpdateDBPosition($recordid)

Update the bPosition of all records that are bigger than
the one that being deleted.


$recordid = # of the record


***************************************************/
function UpdateDBPosition($recordid) {

     OpenDB();

     $result_id = QueryDB('SELECT bPosition,bCategoryID FROM '.GetDBPrefix().'banners WHERE bID='.$recordid);
     if (!$result_id) {
          $message  = 'Invalid query: ' . mysql_error() . "\n";
          $message .= 'Whole query: ' . $query;
          die($message);
     }
     $row = mysql_fetch_assoc($result_id);
     if ($row) {

        QueryDB('UPDATE '.GetDBPrefix().'banners SET bPosition=bPosition-1 WHERE bPosition > '.$row['bPosition'].' AND bCategoryID='.$row['bCategoryID']);
     }




     CloseDB();
}

/**************************************************
function DeleteDB($table,$tablekey,$recordid,$params)

Update a record

$table = table name without the prefix
$tablekey = the primary key of the table
$recordid = # of the record


***************************************************/
function DeleteDB($table,$tablekey,$recordid) {

     OpenDB();
     QueryDB('DELETE FROM '.GetDBPrefix().$table.' WHERE '.$tablekey.'='.$recordid);
     CloseDB();
}

/**************************************************
function DeleteCategoryFiles($recordid)

Deletes all the files from the specific category


$recordid = # of the category record


***************************************************/
function DeleteCategoryFiles($recordid) {

       OpenDB();
       $query = "SELECT bImage FROM ".GetDBPrefix()."categories".",".GetDBPrefix()."banners WHERE bCategoryID=".$recordid." AND cID=".$recordid;
       $result_id = mysql_query($query);
       if (!$result_id) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
            die($message);
       }
       $ER = error_reporting(0);
       while ($row = mysql_fetch_assoc($result_id)){

            if ($row['bImage'] != '')
                unlink("banners/".$row['bImage']);
       }
       error_reporting($ER);
      CloseDB();
}

/*****************************************************8
function GetLastUpdated()

Returns the date of the last updated or created record


*********************************************************/
function GetLastUpdated() {

    global $DB_Prefix;

    //enable the Stored Procedures calls
    $DB = mysql_connect(MySQLIP, Username, Password,false,65536) or die(mysql_error());

    mysql_select_db(DataBase, $DB) or die(mysql_error());
	mysql_query("SET NAMES 'utf8'");

    $query = "SELECT ".$DB_Prefix."banners.bCreationDate  FROM ".$DB_Prefix."banners order by bCreationDate desc Limit 1";
    $result_id = mysql_query($query);
    if (!$result_id) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $query;
        die($message);
    }
    $row = mysql_fetch_assoc($result_id);
    if ($row) {
    	$recvalue = GR_Time('d-m-Y H:m:s',strtotime($row["bCreationDate"]));
    } else
        $recvalue = "00/00/0000";

    return $recvalue;

    mysql_close($DB);

}


/*************************************************************************
function GR_Time

Convert to GR local date/time independently if the server uses its own
local settings.

usage : GR_Time('Y-m-d',time())


**************************************************************************/
function GR_Time($format,$timestamp){
   //Offset is in hours from gmt, including a - sign if applicable.
   //So lets turn offset into seconds
   if  (date('I') == 1)
       $offset = 10800; // GMT=3*60*60;
   else
       $offset = 7200; // GMT=2*60*60;
   $timestamp = $timestamp + $offset;
    //Remember, adding a negative is still subtraction ;)
   return gmdate($format,$timestamp);
}


/**
 * Register a string in the global translation file.
 *
 * @param string $p_value
 * @param string $p_key
 * @return void
 */
function regLang($p_key, $p_value)
{
	global $g_translationStrings;

	if (!isset($g_translationStrings[$p_key])) {
		$g_translationStrings[$p_key] = $p_value;
	}
}

function getTranslation($p_key) {

  global $g_translationStrings;

  if  (isset($g_translationStrings[$p_key]))
      echo $g_translationStrings[$p_key];
  else
      echo $p_key;
}

function getTranslationString($p_key) {

  global $g_translationStrings;

  if  (isset($g_translationStrings[$p_key]))
      return $g_translationStrings[$p_key];
  else
      return $p_key;
}

/****************************************************************
function DateToIso($strDate)

Mysql compatible date format

****************************************************************/
function DateToIso($strDate) {
        return date("Y-m-d",strtotime ($strDate) );
}


/****************************************************************
function DateFormat($strFormat, $date)

format your date as you want

****************************************************************/
function DateFormat($strFormat, $date) {
        return date($strFormat,strtotime ($date));
}

      function simpleLoadURL($submit_url)
      {
            $curl = curl_init();
            $url_parts = parse_url($submit_url);
            $url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
            curl_setopt($curl, CURLOPT_SSLVERSION,3);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $url_parts['query']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            $response = curl_exec($curl);
            curl_close($curl);
            return(array('body'=>$response));
      }

/****************************************************************
function IPInfo()

Get IP info

****************************************************************/


      function IPInfo($addr_ip,$site){

         //http://ipinfodb.com/
         if ($site==1) {
                //Load the class
                $ipinfodb = new ipinfodb;
                $ipinfodb->setKey('cdc8f7f98e5bc3375c35ac63a2f13f10e7548471e1b04542ef077ffe5cf0552a');

                //Get errors and locations
                $locations = $ipinfodb->getGeoLocation($addr_ip);
                $errors = $ipinfodb->getError();

                //Getting the result
                if (!empty($locations) && is_array($locations)) {
                      return   $locations['CountryCode'].' / '.$locations['RegionName'].' ('.$addr_ip.')';
                } else
                      return $addr_ip;
          } else if ($site==2) {

                //http://api.hostip.info/
                $locations = simpleLoadURL('http://api.hostip.info/get_html.php?ip='.$addr_ip.'&position=true ');
                $body= $locations['body'];
                if ( (!empty($locations)) && is_array($locations) && (strpos($locations['body'],'Unknown')===false) ) {
                          $pos1 = strpos($locations['body'],'(');
                          $pos2 = strpos($locations['body'],',');

                          $body=substr($locations['body'],$pos1+1,$pos2-$pos1-1);
                          $body=str_replace(')','',$body);
                          $body=str_replace('City:',' / ',$body);
                          return   $body.' ('.$addr_ip.')';
                } else
                          return $addr_ip;

          }

      }

      function generate_random_string($length=32) {
       $random="";
       srand((double)microtime()*1000000);
       $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
       $char_list.= "abcdefghijklmnopqrstuvwxyz";
       $char_list.= "1234567890";

       for($i=0;$i<$length;++$i) {
          $random.=substr($char_list,(rand()%(strlen($char_list))), 1);
       }

       return $random;
      };

      function add_session($a,$b) {

          $_SESSION[$a]=$b;

      }

      function get_session($a) {

          return $_SESSION[$a];
      }

      function remove_session($a) {

          unset($_SESSION[$a]);

      }



?>