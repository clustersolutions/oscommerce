<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if ($osC_Customer->isLoggedOn() == false) {
    $navigation->set_snapshot();

    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require('includes/classes/order.php');

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_CHECKOUT);

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add(NAVBAR_TITLE_CHECKOUT, tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
  }

  if (empty($_GET) === false) {
    $_checkout_module = tep_sanitize_string(basename(key(array_slice($_GET, 0, 1))));

    if (file_exists('includes/modules/checkout/' . $_checkout_module . '.php')) {
      include('includes/modules/checkout/' . $_checkout_module . '.php');

      $_checkout_module_name = 'osC_Checkout_' . ucfirst($_checkout_module);
      $osC_Checkout_Module = new $_checkout_module_name();

      $page_contents = $osC_Checkout_Module->getPageContentsFile();
    }
  }

  if (isset($page_contents) === false) {
    if ($osC_Services->isStarted('breadcrumb')) {
      $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_SHOPPING_CART, tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
    }

    $page_contents = 'shopping_cart.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
