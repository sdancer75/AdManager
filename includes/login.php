<?php
/*************************************************************************************
Created by G. Papaioannou
You can contact with me at g_papaioannou@rocketmail.com

Paradox Interactive (c) 2011,Greece
http://www.paradoxinteractive.gr
**************************************************************************************/

    session_start();
    date_default_timezone_set("Europe/Athens");





    function check_login() {

      global $DB_Prefix,$LoginMsg;


       if( ($_POST['submittedlogform'])=="yes" ) {

          $name=$_POST['login_name'];
          $remember_me=isset($_POST['remember_me'])?1:0;
          $pass=($_POST['login_pass']);


          $sqlquery = ("SELECT * FROM ".$DB_Prefix."users WHERE uUsername='$name' AND STRCMP(uPassword, BINARY '".$pass."')=0 ");
          $result= mysql_query($sqlquery);
          $row = mysql_fetch_assoc($result);

          If ($row) {
               account_login($row['uID'],$row['uUsername'],$row['uPassword'],$row['uPrivilege'],$remember_me);
               return true;

          } else {
              $LoginMsg="<span style=\"color:#FF2F2F;font-weight: bold\">".
                        "Τα στοιχεία που δώσατε δεν είναι σωστά. Δοκιμάστε ξανά.".
                        "</span>";
                account_logout();
                return false;
          }

           // if the user isn't logged in but has a COOKIE, process it
       } else if(!get_session("user/id") && isset($_COOKIE["remember_me"]) ) {

          list($user_id,$cookie_code)=@unserialize(stripslashes($_COOKIE["remember_me"]));
          if(isset($user_id) && isset($cookie_code)) {

            $sqlquery = ("SELECT * FROM ".$DB_Prefix."users WHERE uID='$user_id' AND uCookie='".$cookie_code."' LIMIT 1");

            $result= mysql_query($sqlquery);
            $row = mysql_fetch_assoc($result);

            if($row) {

              account_login($row['uID'],$row['uUsername'],$row['uPassword'],$row['uPrivilege'],true);
              return true;

            } else {

              $LoginMsg="<span style=\"color:#FF2F2F;font-weight: bold\">".
                        "Λάθος cookie. Δοκιμάστε ξανά.".
                        "</span>";
                account_logout();
                return false;

            }
          }
        } else if (get_session("user/id")) {

            $sqlquery = ("SELECT * FROM ".$DB_Prefix."users WHERE uID='".get_session("user/id")."'  LIMIT 1");
            $result= mysql_query($sqlquery);
            $row = mysql_fetch_row($result);

            if($row) {

              return true;

            } else {

              $LoginMsg="<span style=\"color:#FF2F2F;font-weight: bold\">".
                        "Λάθος Session. Δοκιμάστε ξανά.".
                        "</span>";
                account_logout();
                return false;

            }

        }



        return false;

    };

      function account_login($user_id,$username,$password,$privilege,$remember_me) {
         global $DB_Prefix;

        // update cookie
        if($remember_me==true) {
          $cookie_code=generate_random_string();
          $cookie_str=serialize(array($user_id, $cookie_code));
          setcookie('remember_me', $cookie_str, time() + 60*60*24*30, '/');
          add_session("user/cookie",$cookie_code);

          $cookie_code=get_session("user/cookie");
          $sqlquery = "UPDATE ".$DB_Prefix."users SET uCookie='".$cookie_code."' WHERE uID=".$user_id." LIMIT 1";

          $result = mysql_query($sqlquery);
        } else {
          remove_session("user/cookie");
        }


        add_session("user/privilege",$privilege);
        add_session("user/id",$user_id);
        add_session("user/username",$username);
        add_session("user/password",$password);


      }

      function account_logout() {

        remove_session("user/privilege");
        remove_session("user/id");
        remove_session("user/username");
        remove_session("user/password");
        remove_session("user/cookie");
        setcookie('remember_me', get_session("user/cookie"), time() - 3600, '/');


      };





?>