<?php
$GLOBALS['g_campsiteDir'] = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once($GLOBALS['g_campsiteDir'].'/conf/liveuser_configuration.php');

// Only logged in admin users allowed
if (!$LiveUser->isLoggedIn()) {
    header("Location: /$ADMIN/login.php");
    exit(0);
} else {
    $userId = $LiveUser->getProperty('auth_user_id');
    $userTmp = new User($userId);
    if (!$userTmp->exists() || !$userTmp->isAdmin()) {
        header("Location: /$ADMIN/login.php");
        exit(0);
    }
    unset($userTmp);
}

require_once('config.inc.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/configuration.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");
camp_load_translation_strings("tiny_media_plugin");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title><?php putGS('Insert media file'); ?></title>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="css/campsitemedia.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
  <script type="text/javascript" src="js/campsitemedia.js"></script>
  <script type="text/javascript" src="assets/popup.js"></script>
  <script type="text/javascript" src="assets/dialog.js"></script>
  <script type="text/javascript" src="assets/manager.js"></script>
</head>
<body>
  <form action="attachments.php" id="uploadForm" method="post">
  <fieldset>
    <div class="dirs">
      <iframe src="attachments.php?article_id=<?php echo $_REQUEST['article_id']; ?>&language_selected=<?php echo $_REQUEST['language_selected']; ?>" name="attachmentsManager" id="attachmentsManager" class="attachmentFrame" scrolling="auto" title="Media File Selection" frameborder="0"></iframe>
    </div>
  </fieldset>

  <!-- file attachment properties -->
  <table class="inputTable">
    <input type="hidden" id="f_attachment_id" value="" />
    <input type="hidden" id="f_url" value="" />
    <input type="hidden" id="f_description" value="" />
  </table>
  <!--// file attachment properties -->
  <div style="text-align: right;">
    <hr />
    <button type="button" class="buttons" onclick="CampsiteMediaDialog.insert('');"><?php putGS('Select'); ?></button>
    <button type="button" class="buttons" onclick="CampsiteMediaDialog.close();"><?php putGS('Cancel'); ?></button>
  </div>
  </form>
</body>
</html>