<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $template = 'default';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $osC_Language->getTextDirection(); ?>" xml:lang="<?php echo $osC_Language->getCode(); ?>" lang="<?php echo $osC_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $osC_Language->getCharacterSet(); ?>" />

<?php
  if ($request_type == 'SSL') {
    echo '<link rel="shortcut icon" href="images/favicon_ssl.ico" />';
  } else {
    echo '<link rel="shortcut icon" href="images/favicon.ico" />';
  }
?>

<title><?php echo TITLE; ?></title>

<link rel="stylesheet" type="text/css" href="templates/default/stylesheet.css">

<script language="javascript" src="includes/general.js"></script>

</head>

<body>

<?php require('templates/default/header.php'); ?>

<div class="pageContents"><?php require('templates/pages/' . $page_contents); ?></div>

<?php require('templates/default/footer.php'); ?>

</body>

</html>
