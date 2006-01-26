<?php
/*
  $Id:orders.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/order.php');

  class osC_Account_Orders extends osC_Template {

/* Private variables */

    var $_module = 'orders',
        $_group = 'account',
        $_page_title,
        $_page_contents = 'account_history.php';

/* Class constructor */

    function osC_Account_Orders() {
      global $osC_Services, $osC_Language, $osC_Customer, $breadcrumb;

      $this->_page_title = $osC_Language->get('orders_heading');

      $osC_Language->load('order');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_my_orders'), tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));

        if (is_numeric($_GET[$this->_module])) {
          $breadcrumb->add(sprintf($osC_Language->get('breadcrumb_order_information'), $_GET[$this->_module]), tep_href_link(FILENAME_ACCOUNT, $this->_module . '=' . $_GET[$this->_module], 'SSL'));
        }
      }

      if (is_numeric($_GET[$this->_module])) {
        if (order::getCustomerID($_GET[$this->_module]) !== $osC_Customer->getID()) {
          tep_redirect(tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
        }

        $this->_page_title = sprintf($osC_Language->get('order_information_heading'), $_GET[$this->_module]);
        $this->_page_contents = 'account_history_info.php';
      }
    }
  }
?>
