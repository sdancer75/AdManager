<?php

/*

Usage
Save the above file as SimpleImage.php and take a look at the following examples of how to use the script.

The first example below will load a file named picture.jpg resize it to 250 pixels wide and 400 pixels high and resave it as picture2.jpg

<?php
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resize(250,400);
   $image->save('picture2.jpg');
?>
If you want to resize to a specifed width but keep the dimensions ratio the same then the script can work out the required height for you, just use the resizeToWidth function.

<?php
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resizeToWidth(250);
   $image->save('picture2.jpg');
?>
You may wish to scale an image to a specified percentage like the following which will resize the image to 50% of its original width and height

<?php
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->scale(50);
   $image->save('picture2.jpg');
?>
You can of course do more than one thing at once. The following example will create two new images with heights of 200 pixels and 500 pixels

<?php
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resizeToHeight(500);
   $image->save('picture2.jpg');
   $image->resizeToHeight(200);
   $image->save('picture3.jpg');
?>
The output function lets you output the image straight to the browser without having to save the file. Its useful for on the fly thumbnail generation

<?php
   header('Content-Type: image/jpeg');
   include('SimpleImage.php');
   $image = new SimpleImage();
   $image->load('picture.jpg');
   $image->resizeToWidth(150);
   $image->output();
?>
The following example will resize and save an image which has been uploaded via a form

<?php
   if( isset($_POST['submit']) ) {
      include('SimpleImage.php');
      $image = new SimpleImage();
      $image->load($_FILES['uploaded_image']['tmp_name']);
      $image->resizeToWidth(150);
      $image->output();
   } else {
?>
   <form action="upload.php" method="post" enctype="multipart/form-data">
      <input type="file" name="uploaded_image" />
      <input type="submit" name="submit" value="Upload" />
   </form>
<?php
   }
?>


*/
class SimpleImage {
   
   var $image;
   var $image_type;
   var $filename;

   function load($filename) {
      $image_info = getimagesize($filename);
      $this->filename = $filename;
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }




   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=90, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }   
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }

   function resize($width,$height) {

      //Check if GD extension is loaded
      if (!extension_loaded('gd') && !extension_loaded('gd2')) {
             trigger_error("GD is not loaded", E_USER_WARNING);
             return false;
      }

     //If image dimension is smaller, do not resize
     if  ( ($this->getHeight() <= $height) && ($this->getWidth() <= $width) ) {
          return true;
     } else {

          $new_image = imagecreatetruecolor($width, $height);
          imagealphablending($new_image, false);
          imagesavealpha($new_image, true);
          imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));

          imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
          $this->image = $new_image;

          return true;
     }
   }



   function is_animated_gif()   {
      $raw = file_get_contents( $this->filename );

      $offset = 0;
      $frames = 0;
      while ($frames < 2)
      {
          $where1 = strpos($raw, "\x00\x21\xF9\x04", $offset);
          if ( $where1 === false )
          {
                  break;
          }
          else
          {
                  $offset = $where1 + 1;
                  $where2 = strpos( $raw, "\x00\x2C", $offset );
                  if ( $where2 === false )
                  {
                          break;
                  }
                  else
                  {
                          if ( $where1 + 8 == $where2 )
                          {
                                  $frames ++;
                          }
                          $offset = $where2 + 1;
                  }
          }
      }

      return $frames > 1;
  }



}


class GIFDecoder {
    var $GIF_TransparentR = - 1;
    var $GIF_TransparentG = - 1;
    var $GIF_TransparentB = - 1;
    var $GIF_TransparentI = 0;
    var $GIF_buffer = array();
    var $GIF_arrays = array();
    var $GIF_delays = array();
    var $GIF_dispos = array();
    var $GIF_stream = "";
    var $GIF_string = "";
    var $GIF_bfseek = 0;
    var $GIF_anloop = 0;
    var $GIF_screen = array();
    var $GIF_global = array();
    var $GIF_sorted;
    var $GIF_colorS;
    var $GIF_colorC;
    var $GIF_colorF;

    function GIFDecoder($GIF_pointer) {
            $this->GIF_stream = $GIF_pointer;
            GIFDecoder :: GIFGetByte(6);
            GIFDecoder :: GIFGetByte(7);
            $this->GIF_screen = $this->GIF_buffer;
            $this->GIF_colorF = $this->GIF_buffer[4] & 0x80 ? 1 : 0;
            $this->GIF_sorted = $this->GIF_buffer[4] & 0x08 ? 1 : 0;
            $this->GIF_colorC = $this->GIF_buffer[4] & 0x07;
            $this->GIF_colorS = 2 << $this->GIF_colorC;
            if($this->GIF_colorF == 1) {
                    GIFDecoder :: GIFGetByte(3 * $this->GIF_colorS);
                    $this->GIF_global = $this->GIF_buffer;
            }
            for($cycle = 1; $cycle;) {
                    if(GIFDecoder :: GIFGetByte(1)) {
                            switch($this->GIF_buffer[0]) {
                                    case 0x21 :
                                            GIFDecoder :: GIFReadExtensions();
                                            break;
                                    case 0x2C :
                                            GIFDecoder :: GIFReadDescriptor();
                                            break;
                                    case 0x3B :
                                            $cycle = 0;
                                            break;
                            }
                    }
                    else{
                            $cycle = 0;
                    }
            }
    }

    function GIFReadExtensions() {
            GIFDecoder :: GIFGetByte(1);
            if($this->GIF_buffer[0] == 0xff) {
                    for(;;) {
                            GIFDecoder :: GIFGetByte(1);
                            if(($u = $this->GIF_buffer[0]) == 0x00) {
                                    break;
                            }
                            GIFDecoder :: GIFGetByte($u);
                            if($u == 0x03) {
                                    $this->GIF_anloop = ($this->GIF_buffer[1] | $this->GIF_buffer[2] << 8);
                            }
                    }
            }
            else{
                    for(;;) {
                            GIFDecoder :: GIFGetByte(1);
                            if(($u = $this->GIF_buffer[0]) == 0x00) {
                                    break;
                            }
                            GIFDecoder :: GIFGetByte($u);
                            if($u == 0x04) {
                                    if($this->GIF_buffer[4] & 0x80) {
                                            $this->GIF_dispos[] = ($this->GIF_buffer[0] >> 2) - 1;
                                    }
                                    else{
                                            $this->GIF_dispos[] = ($this->GIF_buffer[0] >> 2) - 0;
                                    }
                                    $this->GIF_delays[] = ($this->GIF_buffer[1] | $this->GIF_buffer[2] << 8);
                                    if($this->GIF_buffer[3]) {
                                            $this->GIF_TransparentI = $this->GIF_buffer[3];
                                    }
                            }
                    }
            }
    }

    function GIFReadDescriptor() {
            $GIF_screen = array();
            GIFDecoder :: GIFGetByte(9);
            $GIF_screen = $this->GIF_buffer;
            $GIF_colorF = $this->GIF_buffer[8] & 0x80 ? 1 : 0;
            if($GIF_colorF) {
                    $GIF_code = $this->GIF_buffer[8] & 0x07;
                    $GIF_sort = $this->GIF_buffer[8] & 0x20 ? 1 : 0;
            }
            else{
                    $GIF_code = $this->GIF_colorC;
                    $GIF_sort = $this->GIF_sorted;
            }
            $GIF_size = 2 << $GIF_code;
            $this->GIF_screen[4] &= 0x70;
            $this->GIF_screen[4] |= 0x80;
            $this->GIF_screen[4] |= $GIF_code;
            if($GIF_sort) {
                    $this->GIF_screen[4] |= 0x08;
            }
            if($this->GIF_TransparentI) {
                    $this->GIF_string = "GIF89a";
            }
            else{
                    $this->GIF_string = "GIF87a";
            }
            GIFDecoder :: GIFPutByte($this->GIF_screen);
            if($GIF_colorF == 1) {
                    GIFDecoder :: GIFGetByte(3 * $GIF_size);
                    if($this->GIF_TransparentI) {
                            $this->GIF_TransparentR = $this->GIF_buffer[3 * $this->GIF_TransparentI + 0];
                            $this->GIF_TransparentG = $this->GIF_buffer[3 * $this->GIF_TransparentI + 1];
                            $this->GIF_TransparentB = $this->GIF_buffer[3 * $this->GIF_TransparentI + 2];
                    }
                    GIFDecoder :: GIFPutByte($this->GIF_buffer);
            }
            else{
                    if($this->GIF_TransparentI) {
                            $this->GIF_TransparentR = $this->GIF_global[3 * $this->GIF_TransparentI + 0];
                            $this->GIF_TransparentG = $this->GIF_global[3 * $this->GIF_TransparentI + 1];
                            $this->GIF_TransparentB = $this->GIF_global[3 * $this->GIF_TransparentI + 2];
                    }
                    GIFDecoder :: GIFPutByte($this->GIF_global);
            }
            if($this->GIF_TransparentI) {
                    $this->GIF_string .= "!\xF9\x04\x1\x0\x0".chr($this->GIF_TransparentI)."\x0";
            }
            $this->GIF_string .= chr(0x2C);
            $GIF_screen[8] &= 0x40;
            GIFDecoder :: GIFPutByte($GIF_screen);
            GIFDecoder :: GIFGetByte(1);
            GIFDecoder :: GIFPutByte($this->GIF_buffer);
            for(;;) {
                    GIFDecoder :: GIFGetByte(1);
                    GIFDecoder :: GIFPutByte($this->GIF_buffer);
                    if(($u = $this->GIF_buffer[0]) == 0x00) {
                            break;
                    }
                    GIFDecoder :: GIFGetByte($u);
                    GIFDecoder :: GIFPutByte($this->GIF_buffer);
            }
            $this->GIF_string .= chr(0x3B);
            $this->GIF_arrays[] = $this->GIF_string;
    }

    function GIFGetByte($len) {
            $this->GIF_buffer = array();
            for($i = 0; $i < $len; $i++) {
                    if($this->GIF_bfseek > strlen($this->GIF_stream)) {
                            return 0;
                    }
                    $this->GIF_buffer[] = ord($this->GIF_stream {
                            $this->GIF_bfseek++
                    }
                    );
            }
            return 1;
    }

    function GIFPutByte($bytes) {
            foreach($bytes as $byte) {
                    $this->GIF_string .= chr($byte);
            }
    }

    function GIFGetFrames() {
            return ($this->GIF_arrays);
    }

    function GIFGetDelays() {
            return ($this->GIF_delays);
    }

    function GIFGetLoop() {
            return ($this->GIF_anloop);
    }

    function GIFGetDisposal() {
            return ($this->GIF_dispos);
    }

    function GIFGetTransparentR() {
            return ($this->GIF_TransparentR);
    }

    function GIFGetTransparentG() {
            return ($this->GIF_TransparentG);
    }

    function GIFGetTransparentB() {
            return ($this->GIF_TransparentB);
    }
}

class GIFEncoder {
        var $GIF = "GIF89a";
        var $VER = "GIFEncoder V2.05";
        var $BUF = array();
        var $LOP = 0;
        var $DIS = 2;
        var $COL = - 1;
        var $IMG = - 1;
        var $ERR = array("ERR00" => "Does not supported function for only one image!", "ERR01" => "Source is not a GIF image!", "ERR02" => "Unintelligible flag ", "ERR03" => "Does not make animation from animated GIF source",);

        function GIFEncoder($GIF_src, $GIF_dly, $GIF_lop, $GIF_dis, $GIF_red, $GIF_grn, $GIF_blu, $GIF_mod) {
                if(!is_array($GIF_src) && !is_array($GIF_tim)) {
                        printf("%s: %s", $this->VER, $this->ERR['ERR00']);
                        exit (0);
                }
                $this->LOP = ($GIF_lop > - 1)?$GIF_lop : 0;
                $this->DIS = ($GIF_dis > - 1)?(($GIF_dis < 3)?$GIF_dis : 3) : 2;
                $this->COL = ($GIF_red > - 1 && $GIF_grn > - 1 && $GIF_blu > - 1)?($GIF_red | ($GIF_grn << 8) | ($GIF_blu << 16)) : - 1;
                for($i = 0; $i < count($GIF_src); $i++) {
                        if(strToLower($GIF_mod) == "url") {
                                $this->BUF[] = fread(fopen($GIF_src[$i], "rb"), filesize($GIF_src[$i]));
                        }
                        else
                                if(strToLower($GIF_mod) == "bin") {
                                        $this->BUF[] = $GIF_src[$i];
                                }
                                else{
                                        printf("%s: %s ( %s )!", $this->VER, $this->ERR['ERR02'], $GIF_mod);
                                        exit (0);
                        }
                        if(substr($this->BUF[$i], 0, 6) != "GIF87a" && substr($this->BUF[$i], 0, 6) != "GIF89a") {
                                printf("%s: %d %s", $this->VER, $i, $this->ERR['ERR01']);
                                exit (0);
                        }
                        for($j = (13 + 3 * (2 << (ord($this->BUF[$i] { 10 }) & 0x07))), $k = true; $k; $j++) {
                                switch($this->BUF[$i] { $j }) {
                                        case "!" :
                                                if((substr($this->BUF[$i], ($j + 3), 8)) == "NETSCAPE") {
                                                        printf("%s: %s ( %s source )!", $this->VER, $this->ERR['ERR03'], ($i + 1));
                                                        exit (0);
                                                }
                                                break;
                                        case ";" :
                                                $k = false;
                                                break;
                                }
                        }
                }
                GIFEncoder :: GIFAddHeader();
                for($i = 0; $i < count($this->BUF); $i++) {
                        GIFEncoder :: GIFAddFrames($i, $GIF_dly[$i]);
                }
                GIFEncoder :: GIFAddFooter();
        }

        function GIFAddHeader() {
                $cmap = 0;
                if(ord($this->BUF[0] { 10 }) & 0x80) {
                        $cmap = 3 * (2 << (ord($this->BUF[0] {
                                10
                        }
                        ) & 0x07 ) );
                        $this->GIF .= substr($this->BUF[0], 6, 7);
                        $this->GIF .= substr($this->BUF[0], 13, $cmap);
                        $this->GIF .= "!\377\13NETSCAPE2.0\3\1".GIFEncoder :: GIFWord($this->LOP)."\0";
                }
        }

        function GIFAddFrames($i, $d) {
                $Locals_str = 13 + 3 * (2 << (ord($this->BUF[$i] {
                        10
                }
                ) & 0x07 ) );
                $Locals_end = strlen($this->BUF[$i]) - $Locals_str - 1;
                $Locals_tmp = substr($this->BUF[$i], $Locals_str, $Locals_end);
                $Global_len = 2 << (ord($this->BUF[0] {
                        10
                }
                ) & 0x07 );
                $Locals_len = 2 << (ord($this->BUF[$i] {
                        10
                }
                ) & 0x07 );
                $Global_rgb = substr($this->BUF[0], 13, 3 * (2 << (ord($this->BUF[0] {
                        10
                }
                ) & 0x07 ) ) );
                $Locals_rgb = substr($this->BUF[$i], 13, 3 * (2 << (ord($this->BUF[$i] {
                        10
                }
                ) & 0x07 ) ) );
                $Locals_ext = "!\xF9\x04".chr(($this->DIS << 2) + 0).chr(($d >> 0) & 0xFF).chr(($d >> 8) & 0xFF)."\x0\x0";
                if($this->COL > - 1 && ord($this->BUF[$i] { 10 }) & 0x80) {
                        for($j = 0; $j < (2 << (ord($this->BUF[$i] { 10 }) & 0x07)); $j++) {
                                if(ord($Locals_rgb { 3 * $j + 0 }) == (($this->COL >> 16) & 0xFF) && ord($Locals_rgb { 3 * $j + 1 }) == (($this->COL >> 8) & 0xFF) && ord($Locals_rgb { 3 * $j + 2 }) == (($this->COL >> 0) & 0xFF)) {
                                        $Locals_ext = "!\xF9\x04".chr(($this->DIS << 2) + 1).chr(($d >> 0) & 0xFF).chr(($d >> 8) & 0xFF).chr($j)."\x0";
                                        break;
                                }
                        }
                }
                switch($Locals_tmp { 0 }) {
                        case "!" :
                                $Locals_img = substr($Locals_tmp, 8, 10);
                                $Locals_tmp = substr($Locals_tmp, 18, strlen($Locals_tmp) - 18);
                                break;
                        case "," :
                                $Locals_img = substr($Locals_tmp, 0, 10);
                                $Locals_tmp = substr($Locals_tmp, 10, strlen($Locals_tmp) - 10);
                                break;
                }
                if(ord($this->BUF[$i] { 10 }) & 0x80 && $this->IMG > - 1) {
                        if($Global_len == $Locals_len) {
                                if(GIFEncoder :: GIFBlockCompare($Global_rgb, $Locals_rgb, $Global_len)) {
                                        $this->GIF .= ($Locals_ext.$Locals_img.$Locals_tmp);
                                }
                                else{
                                        $byte = ord($Locals_img {
                                                9
                                        }
                                        );
                                        $byte |= 0x80;
                                        $byte &= 0xF8;
                                        $byte |= (ord($this->BUF[0] {
                                                10
                                        }
                                        ) & 0x07 );
                                        $Locals_img {
                                                9
                                        }
                                        = chr($byte);
                                        $this->GIF .= ($Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp);
                                }
                        }
                        else{
                                $byte = ord($Locals_img {
                                        9
                                }
                                );
                                $byte |= 0x80;
                                $byte &= 0xF8;
                                $byte |= (ord($this->BUF[$i] {
                                        10
                                }
                                ) & 0x07 );
                                $Locals_img {
                                        9
                                }
                                = chr($byte);
                                $this->GIF .= ($Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp);
                        }
                }
                else{
                        $this->GIF .= ($Locals_ext.$Locals_img.$Locals_tmp);
                }
                $this->IMG = 1;
        }

        function GIFAddFooter() {
                $this->GIF .= ";";
        }

        function GIFBlockCompare($GlobalBlock, $LocalBlock, $Len) {
                for($i = 0; $i < $Len; $i++) {
                        if($GlobalBlock { 3 * $i + 0 } != $LocalBlock { 3 * $i + 0 } || $GlobalBlock { 3 * $i + 1 } != $LocalBlock { 3 * $i + 1 } || $GlobalBlock { 3 * $i + 2 } != $LocalBlock { 3 * $i + 2 }) {
                                return (0);
                        }
                }
                return (1);
        }

        function GIFWord($int) {
                return (chr($int & 0xFF).chr(($int >> 8) & 0xFF));
        }

        function GetAnimation() {
                return ($this->GIF);
        }
}

?>