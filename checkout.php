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

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_CHECKOUT);

  $osC_Template->setPageTitle(HEADING_TITLE_CHECKOUT_SHOPPING_CART);
  $osC_Template->setPageContentsFilename('shopping_cart.php');

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add(NAVBAR_TITLE_CHECKOUT, tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
  }

  if (empty($_GET) === false) {
    $_checkout_module = tep_sanitize_string(basename(key(array_slice($_GET, 0, 1))));

    if (file_exists('includes/modules/checkout/' . $_checkout_module . '.php')) {
      if ($osC_Customer->isLoggedOn() == false) {
        $navigation->set_snapshot();

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      include('includes/modules/checkout/' . $_checkout_module . '.php');

      $_checkout_module_name = 'osC_Checkout_' . ucfirst($_checkout_module);
      $osC_Checkout_Module = new $_checkout_module_name();

      $osC_Template->setPageTitle($osC_Checkout_Module->getPageTitle());
      $osC_Template->setPageContentsFilename($osC_Checkout_Module->getPageContentsFilename());

      unset($osC_Checkout_Module);
    }
  }

  if ($osC_Template->getPageContentsFilename() == 'shopping_cart.php') {
    if ($osC_Services->isStarted('breadcrumb')) {
      $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_SHOPPING_CART, tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
    }
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
