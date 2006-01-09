<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/classes/order.php');

  require('includes/languages/' . $osC_Language->getDirectory() . '/' . FILENAME_CHECKOUT);

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add(NAVBAR_TITLE_CHECKOUT, tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
  }

  $osC_Template = osC_Template::setup('cart');

  require('templates/' . $osC_Template->getCode() . '.php');

  require('includes/application_bottom.php');
?>
