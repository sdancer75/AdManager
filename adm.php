<?php

/*************************************************************************************
Created by G. Papaioannou
You can contact with me at g_papaioannou@rocketmail.com

Paradox Interactive (c) 2011,Greece
http://www.paradoxinteractive.gr

**************************************************************************************/



     session_start();
     include_once 'includes/config.php';


     $_SESSION['DB_Prefix'] = $DB_Prefix;
     $_SESSION['g_ADM_Result'] = $g_ADM_Result;


      define("ADM_CATEGORY", 0);
      define("ADM_BANNER", 1);
      define("CRYPTKEY","password to (en/de)crypt");

      function encrypt($key,$string){
  	   return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
      }

  	  function decrypt($key,$string){
  		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
  	  }


      function ADM_Close() {

          mysql_close($_SESSION['DB']);
      }

      function ADM_Open($machinename,$type=ADM_CATEGORY) {



          if ($type==ADM_BANNER) {
             $query = "SELECT * from ".$_SESSION['DB_Prefix']."banners WHERE bMachinename='".$machinename."' AND bActive=1 AND ( (bStartDate <= CURDATE() AND bEndDate >= CURDATE()) OR (bStartDate='1970-01-01') )";
          } else if ($type==ADM_CATEGORY) {
             $query = "SELECT * from ".$_SESSION['DB_Prefix']."categories,".$_SESSION['DB_Prefix']."banners WHERE bCategoryID=cID AND cMachinename='".$machinename."' AND cActive=1 AND bActive=1 AND ( (bStartDate <= CURDATE() AND bEndDate >= CURDATE()) OR (bStartDate='1970-01-01') ) ORDER BY bPosition ASC";
          } else
              return false;


          //enable the Stored Procedures calls
          $_SESSION['DB'] = mysql_connect(MySQLIP, Username, Password,false,65536) or die(mysql_error());

          mysql_select_db(DataBase, $_SESSION['DB']) or die(mysql_error());
      	  mysql_query("SET NAMES 'utf8'");



          $_SESSION['g_ADM_Result'] = mysql_query($query);
          if (!$_SESSION['g_ADM_Result']) {
              $message  = 'Invalid query: ' . mysql_error() . "\n";
              $message .= 'Whole query: ' . $query;
              die($message);
          }

          if ($_SESSION['g_ADM_Result'])
             return $_SESSION['g_ADM_Result'];
          else{

             return false;

          }

      }

      function ADM_GetLink() {


          $row = mysql_fetch_assoc($_SESSION['g_ADM_Result']);
          if ($row) {

            switch ($row['bContentType']) {
                case 0:$htmlcode = htmlspecialchars_decode($row['bText'],ENT_QUOTES);break;
                case 1:if ($row['bSWF']) {

                            if ($row['cHeight']==0) {
                               if ($row['bSWFHeight']==0)
                                    $row['cHeight'] = $row['cWidth'] / 3;
                               else
                                    $row['cHeight'] = $row['bSWFHeight'];
                            } else if  ($row['bSWFHeight']!=0) {
                                    $row['cHeight'] = $row['bSWFHeight'];
                            }

                            if ($row['cWidth']==0) {
                               if ($row['bSWFWidth']==0)
                                    $row['cWidth'] = $row['cHeight'] * 3;
                               else
                                    $row['cWidth'] = $row['bSWFWidth'];

                            } else if  ($row['bSWFWidth']!=0) {
                                    $row['cWidth'] = $row['bSWFWidth'];
                            }

                            if ($row['bSWFBgColor']!='') {
                                $bgcolor = $row['bSWFBgColor'];
                            } else
                                $bgcolor = "#FFFFFF";


                            $htmlcode = '<object width="'.$row['cWidth'].'" height="'.$row['cHeight'].'">';
                            $htmlcode .= '<param name="quality" value="high" />';
                            $htmlcode .= '<param name="wmode" value="opaque" />';
                            $htmlcode .= '<param name="bgcolor" value="'.$bgcolor.'" />';
                            $htmlcode .= '<param name="movie" value="http://'.$_SERVER['SERVER_NAME'].'/adm/banners/'.$row['bImage'].'">';
                            $htmlcode .= '<embed src="http://'.$_SERVER['SERVER_NAME'].'/adm/banners/'.$row['bImage'].'" width="'.$row['cWidth'].'" height="'.$row['cHeight'].'" name="'.$row['bDescription'].'" align="" type="application/x-shockwave-flash" pluginspage="http://get.adobe.com/flashplayer/" bgcolor="'.$bgcolor.'"></embed>';
                            $htmlcode .= '</object>';


                       } else {

                            $htmlcode = '<img src="http://'.$_SERVER['SERVER_NAME'].'/adm/banners/'.$row['bImage'].'" border=0 alt="'.($row['bDescription']).'">';
                       }
                       break;
                case 2:$htmlcode = htmlspecialchars_decode($row['bCode'],ENT_QUOTES);break;
                default : return false;break;
            }


          } else {
            // no record found
            return false;
          }


          if ($row['bURL'] != '') {

            //$htmlcode = '<a href="/adm/adm.php?admURL='.rawurlencode(encrypt(CRYPTKEY,$row['bID'])).'" target="_blank">'.$htmlcode.'</a>';
            $htmlcode = '<a href="/adm/adm.php?admURL='.rawurlencode($row['bID']).'" target="_blank">'.$htmlcode.'</a>';
          }

          if ($row['bStyleCSS'] != '') {

            $htmlcode = '<div style="'.rawurldecode($row['bStyleCSS']).'">'.$htmlcode.'</div>';
          }

          //Update Impressions
          $query = "UPDATE ".$_SESSION['DB_Prefix']."statistics SET sDate=NOW(),sImpressions=sImpressions+1,sIP='".$_SERVER['REMOTE_ADDR']."' WHERE sBannerID=".$row['bID'];
          $result_id = mysql_query($query);
          if (mysql_affected_rows()==0) {
              $query = "INSERT ".$_SESSION['DB_Prefix']."statistics SET sDate=NOW(),sImpressions=1, sClicks=0, sIP='".$_SERVER['REMOTE_ADDR']."', sBannerID=".$row['bID'];
              $result_id = mysql_query($query);
          }

          return $htmlcode;


      }

      //if URL is enabled come here
      if (isset($_GET['admURL'])) {
          //enable the Stored Procedures calls
          $_SESSION['DB'] = mysql_connect(MySQLIP, Username, Password,false,65536) or die(mysql_error());

          //$BannerID = decrypt(CRYPTKEY,rawurldecode($_GET['admURL']));
          $BannerID = $_GET['admURL'];
          mysql_select_db(DataBase, $_SESSION['DB']) or die(mysql_error());
      	  mysql_query("SET NAMES 'utf8'");

          $query = "SELECT * from ".$_SESSION['DB_Prefix']."banners WHERE bID=".$BannerID;
          $_SESSION['g_ADM_Result'] = mysql_query($query);
          if (!$_SESSION['g_ADM_Result']) {
              $message  = 'Invalid query: ' . mysql_error() . "\n";
              $message .= 'Whole query: ' . $query;
              die($message);
          }
          $row = mysql_fetch_assoc($_SESSION['g_ADM_Result']);

          if ($row['bURL'] != '') {
               //Update clicks
               $query = "UPDATE ".$_SESSION['DB_Prefix']."statistics SET sClicks=sClicks+1, sDate=NOW(), sIP='".$_SERVER['REMOTE_ADDR']."' WHERE sBannerID=".$BannerID;
               $_SESSION['g_ADM_Result'] = mysql_query($query);
                if (mysql_affected_rows()==0) {
                    $query = "INSERT ".$_SESSION['DB_Prefix']."statistics SET sDate=NOW(),sImpressions=0, sClicks=1, sIP='".$_SERVER['REMOTE_ADDR']."', sBannerID=".$BannerID;
                    $_SESSION['g_ADM_Result'] = mysql_query($query);
                }
               header("Location: ".htmlspecialchars_decode($row['bURL']));
          }

          mysql_close($_SESSION['DB']);

      }

?>