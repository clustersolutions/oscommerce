<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Success {

/* Public variables */

    var $page_contents = 'checkout_success.php';

/* Private variables */

    var $_module = 'success';

/* Class constructor */

    function osC_Checkout_Success() {
      global $osC_Services, $breadcrumb;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_SUCCESS, tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

      if ($_GET[$this->_module] == 'update') {
        $this->_process();
      }
    }

/* Public methods */

    function getPageContentsFile() {
      return $this->page_contents;
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

      tep_redirect(tep_href_link(FILENAME_DEFAULT, $notify_string));
    }
  }
?>
