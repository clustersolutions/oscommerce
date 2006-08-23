<?php
/*
  $Id:success.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Success extends osC_Template {

/* Private variables */

    var $_module = 'success',
        $_group = 'checkout',
        $_page_title,
        $_page_contents = 'checkout_success.php';

/* Class constructor */

    function osC_Checkout_Success() {
      global $osC_Services, $osC_Language, $osC_Customer, $osC_NavigationHistory, $breadcrumb;

      $this->_page_title = $osC_Language->get('success_heading');

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_checkout_success'), osc_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'update') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      $notify_string = 'action=notify&';
      $notify = (isset($_POST['notify']) ? $_POST['notify'] : array());

      if (!is_array($notify)) $notify = array($notify);

      for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
        $notify_string .= 'notify[]=' . $notify[$i] . '&';
      }

      if (strlen($notify_string) > 0) $notify_string = substr($notify_string, 0, -1);

      osc_redirect(osc_href_link(FILENAME_DEFAULT, $notify_string, 'AUTO'));
    }
  }
?>
