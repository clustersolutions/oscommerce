<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $osC_Language->load('checkout');

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add($osC_Language->get('breadcrumb_checkout'), tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
  }

  $osC_Template = osC_Template::setup('cart');

  require('templates/' . $osC_Template->getCode() . '.php');

  require('includes/application_bottom.php');
?>