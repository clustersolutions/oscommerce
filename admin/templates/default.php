<?php
/*
  $Id: default.php 1497 2007-03-29 13:40:05Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $osC_Language->getTextDirection(); ?>" xml:lang="<?php echo $osC_Language->getCode(); ?>" lang="<?php echo $osC_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $osC_Language->getCharacterSet(); ?>" />

<?php
  if ($request_type == 'SSL') {
    echo '<link rel="shortcut icon" href="images/favicon_ssl.ico" type="image/x-icon" />';
  } else {
    echo '<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />';
  }
?>

<title><?php echo STORE_NAME . ': ' . $osC_Language->get('administration_title') . ($osC_Template->hasPageTitle() ? ': ' . $osC_Template->getPageTitle() : ''); ?></title>

<meta name="Generator" value="osCommerce" />
<meta name="robots" content="noindex,nofollow" />

<script language="javascript" src="../ext/jquery/jquery-1.3.2.min.js"></script>
<script language="javascript" src="../ext/jquery/jquery.cookie.js"></script>
<script language="javascript" src="../ext/jquery/jquery.json-1.3.min.js"></script>
<script language="javascript" src="../ext/jquery/jquery.tinysort.min.js"></script>
<script language="javascript" src="../ext/jquery/jquery.ocupload-1.1.2.packed.js"></script>

<link rel="stylesheet" type="text/css" href="../ext/jquery/ui/themes/smoothness/jquery-ui-1.7.custom.css" />
<script language="javascript" src="../ext/jquery/ui/jquery-ui-1.7.custom.min.js"></script>

<script language="javascript" src="../ext/alexei/sprintf.js"></script>

<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="js/datatable.js"></script>

<link rel="stylesheet" type="text/css" href="templates/default/stylesheet.css" />

<script language="javascript"><!--
  var pageURL = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()); ?>';
  var pageModule = '<?php echo $osC_Template->getModule(); ?>';

  var batchSize = parseInt('<?php echo MAX_DISPLAY_SEARCH_RESULTS; ?>');
  var batchTotalPagesText = '<?php echo addslashes($osC_Language->get('batch_results_number_of_entries')); ?>';
  var batchCurrentPageset = '<?php echo addslashes($osC_Language->get('result_set_current_page')); ?>';
  var batchIconNavigationBack = '<?php echo osc_icon('nav_back.png'); ?>';
  var batchIconNavigationBackGrey = '<?php echo osc_icon('nav_back_grey.png'); ?>';
  var batchIconNavigationForward = '<?php echo osc_icon('nav_forward.png'); ?>';
  var batchIconNavigationForwardGrey = '<?php echo osc_icon('nav_forward_grey.png'); ?>';
  var batchIconProgress = '<?php echo osc_icon('progress_ani.gif'); ?>';

  var taxDecimalPlaces = parseInt('<?php echo TAX_DECIMAL_PLACES; ?>');
//--></script>

</head>

<body>

<?php
  if ($osC_Template->hasPageHeader()) {
    include('templates/default/header.php');
  }
?>

<div class="pageContents">
  <?php require('includes/applications/' . $osC_Template->getModule() . '/pages/' . $osC_Template->getPageContentsFilename()); ?>
</div>

<?php
  if ($osC_Template->hasPageFooter()) {
    include('templates/default/footer.php');
  }
?>

</body>

</html>
