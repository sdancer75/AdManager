# AdManager
AdManager or ADM is an Advertisement Manager for your web site. It was written in php using Yahoo ajax SDK.

ADManager v1.00
Paradox Interactive
http://www.paradoxinteractive.gr
Personal blog http://papaioannou.paradoxinteractive.gr
Lead programmer , G. Papaioannou
Greece, Sep 2011

![Image1](/Pics/Screenshot_1.jpg)

About
========

AdManager or ADM is an Advertisement Manager for your web site. It was written in php using Yahoo ajax SDK.
This application was designed just for our needs. We are not responsible for any problems you might meet using this application. Please note that we also do not offer any technical support for this product. You can always contact with us, for recommendations, subscriptions or wish list for the next releases. You can use this code, but you have to comply with the GPL license.

Please donate if you want to support our efforts, to improve this software. Without your help, we can not continue to offer this product for free.

Features
==========

- Simple and friendly user interface
- Ajax oriented
- Statistics support for clicks and impressions
- User multilevel support
- Image, Swf flash, text and code banner support
- Automatic image resize (except GIF)
- Multiple categories
- Simple integration
- Only 4 DB MySql tables required
- Multilanguage 
- Custom CSS style
- Working "active period" of each banner



How to Install this Application
=================================
1. Create a new directory ie ADM under /xampp/htdocs (or any other apache compatible web server you use)
2. Copy/Paste the code, under the above directory
3. Inside the phpmyadmin, import the sql script adm.sql you will find inside the folder install.
4. Open the config.php file inside the "includes" folder and make the appropriate changes like sql server ip, username etc.
   You can also change the language you want to use. I have only two language files (under the "translations" folder) at the moment, but
   they are incomplete since some of the texts are hard coded in Greek language inside the code. I don't have the time right now 
   to do this job, so you have to do it by yourself.   
5. You're done. Just open a browser and run the manager.
   Default Admin username  : admin
   Default Admin password  : admin
   
   Default User username  : user
   Default User password  : user   

   
How to use it with your application
====================================

1. Include te following file:
```javascript
   include_once 'adm.php';  
```   
2. Choose the category ID, you want to view, ie 'RightSide' by doing the following
```javascript
   $result = ADM_Open('RightSide'); 
```   
3. Check for errors just to be sure using the following code
```javascript
  if ($result===false) {

      echo "error trying to find the resource";
      return;
  }
```
4. Get the actual banners with following code
```javascript
  while ( $link=ADM_GetLink() ) {

    echo "<p>".$link."</p>";

  }
```  
5. Do not forget to close the whole procedure with the code
```javascript
  ADM_Close(); 
```    
You can always refer to the code of the page "admtest.php" you will find in the root directory.


User Guide
============
Before you create an advertisment you have to create a category for this. For example, if you want to show your banner at the left
side of your web site, you have to create a category with a name ie "Left Side Banners". Under this category, you can add as many banners
as you want. The size is fixed either in Height, or Width or both of them. Exception to this is the GIF images and swf files. In case of the GIF images, you have to resize the image by yourself and in the case of swf files, you have to enter the appropriate size in the swf box. 


To Do List
===========
1. To finish the translation files.
2. To create an preprogrammed list of css styles

Some images from the environment inside.

![Image11](/Pics/Screenshot_11.jpg)
![Image3](/Pics/Screenshot_3.jpg)
![Image2](/Pics/Screenshot_2.jpg)
![Image4](/Pics/Screenshot_4.jpg)
![Image5](/Pics/Screenshot_5.jpg)
![Image6](/Pics/Screenshot_6.jpg)
![Image7](/Pics/Screenshot_7.jpg)
![Image8](/Pics/Screenshot_8.jpg)
![Image9](/Pics/Screenshot_9.jpg)

