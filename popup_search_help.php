<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $osC_NavigationHistory->removeCurrentPage();

  require('includes/languages/' . $osC_Language->getDirectory() . '/' . FILENAME_SEARCH);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $osC_Language->getTextDirection(); ?>" xml:lang="<?php echo $osC_Language->getCode(); ?>" lang="<?php echo $osC_Language->getCode(); ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $osC_Language->getCharacterSet(); ?>">
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<title><?php echo STORE_NAME . ($osC_Template->hasPageTitle() ? ': ' . $osC_Template->getPageTitle() : ''); ?></title>
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="10" marginheight="10" topmargin="10" bottommargin="10" leftmargin="10" rightmargin="10">
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => $osC_Language->get('search_help_heading'));

  new infoBoxHeading($info_box_contents, true, true);

  $info_box_contents = array();
  $info_box_contents[] = array('text' => $osC_Language->get('search_help'));

  new infoBox($info_box_contents);
?>

<p class="smallText" align="right"><?php echo '<a href="javascript:window.close()">' . $osC_Language->get('close_window') . '</a>'; ?></p>

</body>
</html>
<?php require('includes/application_bottom.php'); ?>
