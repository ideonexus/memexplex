<?php

/**
 * This is the location the framework boots the user
 * out to when an unexpected, unhandled error occurs.
 * This page should never be seen in production.
 *
 * @package Framework
 * @author Ryan Somma
 * @ignore
 */

//DETERMINE THE ROOT FOLDER FOR ALL LINKS
define
(
    'ROOT_FOLDER'
    ,substr
    (
        $_SERVER['PHP_SELF']
        ,0
        ,strpos($_SERVER['PHP_SELF'], basename(__FILE__))
    )
);

//DON'T SHOW HEADER, ETC IF PAGE OCCURS IN AJAX CALL
if (!isset($_GET['ajaxMethod']))
{
?>
<!-- BEGIN PAGE HEADER -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>MemexPlex - Unexpected Error</title><link rel="shortcut icon" type="image/x-icon" href="<?php echo ROOT_FOLDER ?>framework/images/gear.ico"/><link rel="Stylesheet" type="text/css" href="<?php echo ROOT_FOLDER ?>framework/css/memexplex_style.css"/><meta http-equiv="Content-type" content="text/html;charset=UTF-8"/><meta http-equiv="PRAGMA" content="NO-CACHE"/><meta http-equiv="CACHE-CONTROL" content="NO-CACHE"/><meta http-equiv="EXPIRES" content="-1"/></head><body><div class="header"><a href="<?php echo ROOT_FOLDER ?>"><img src="<?php echo ROOT_FOLDER ?>framework/images/memexplexlogo.jpg" width="257" height="50" alt="MemexPlex" /></a>&nbsp;&nbsp;&nbsp;&nbsp;Forging Paths of Knowledge</div><span class="dynamic" id="headerData"></span> 
<!-- END PAGE HEADER --> 
<?php
}
?>
<!-- BEGIN DISPLAY ERROR -->
<div class="largeBlue">
<p><em>An Unexpected Error has Occurred.</em></p>
</div>
<div class="largeBlue">
<p>You should never see this.</p>
</div>
<!-- END DISPLAY ERROR -->
<?php
if (!isset($_GET['ajaxMethod']))
{
?>
<!-- BEGIN PAGE FOOTER -->
  </body>
</html>
<!-- END PAGE FOOTER -->
<?php
}
exit();
?>
