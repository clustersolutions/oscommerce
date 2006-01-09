<?php
/*
  $Id: account.php 65 2005-03-12 16:43:41Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/languages/' . $osC_Language->getDirectory() . '/' . FILENAME_SEARCH);

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add(BREADCRUMB_SEARCH, tep_href_link(FILENAME_SEARCH));
  }

  $osC_Template = osC_Template::setup('search');

  require('templates/' . $osC_Template->getCode() . '.php');

  require('includes/application_bottom.php');
?>
