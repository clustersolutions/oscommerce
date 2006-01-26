<?php
/*
  $Id: account.php 65 2005-03-12 16:43:41Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $osC_Language->load('info');

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add($osC_Language->get('breadcrumb_information'), tep_href_link(FILENAME_INFO));
  }

  $osC_Template = osC_Template::setup('info');

  require('templates/' . $osC_Template->getCode() . '.php');

  require('includes/application_bottom.php');
?>
