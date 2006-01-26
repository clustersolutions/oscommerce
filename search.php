<?php
/*
  $Id: account.php 65 2005-03-12 16:43:41Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $osC_Language->load('search');

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add($osC_Language->get('breadcrumb_search'), tep_href_link(FILENAME_SEARCH));
  }

  $osC_Template = osC_Template::setup('search');

  require('templates/' . $osC_Template->getCode() . '.php');

  require('includes/application_bottom.php');
?>
