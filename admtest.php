<?php

  include_once 'adm.php';

  echo "--------------- TESTing --------------";
  echo "<br><br>";

  $result = ADM_Open('RightSide');

  if ($result===false) {

      echo "error trying to find the resource";
      return;
  }



  while ( $link=ADM_GetLink() ) {

    echo "<p>".$link."</p>";

  }

  ADM_Close();

?>