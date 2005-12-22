<?php
/*
  $Id:payment.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Payment extends osC_Template {

/* Private variables */

    var $_module = 'payment',
        $_group = 'checkout',
        $_page_title = HEADING_TITLE_CHECKOUT_PAYMENT,
        $_page_contents = 'checkout_payment.php';

/* Class constructor */

    function osC_Checkout_Payment() {
      global $osC_Database, $osC_Session, $osC_Customer, $osC_Services, $osC_NavigationHistory, $breadcrumb, $order, $total_weight, $total_count, $payment_modules;

      if ($osC_Customer->isLoggedOn() === false) {
        $osC_NavigationHistory->setSnapshot();

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      if ($_SESSION['cart']->count_contents() < 1) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if (isset($_SESSION['shipping']) == false) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

// avoid hack attempts during the checkout procedure by checking the internal cartID
      if (isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID'])) {
        if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
          tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
        }
      }

// Stock Check
      if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
        $products = $_SESSION['cart']->get_products();
        for ($i=0, $n=sizeof($products); $i<$n; $i++) {
          if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
            tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'SSL'));
            break;
          }
        }
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_PAYMENT, tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

// redirect to the billing address page when no default address exists
      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->_page_title = HEADING_TITLE_CHECKOUT_PAYMENT_ADDRESS;
        $this->_page_contents = 'checkout_payment_address.php';

        $this->addJavascriptFilename('templates/' . $this->_template . '/javascript/checkout_payment_address.js');
        $this->addJavascriptPhpFilename('includes/form_check.js.php');
      } else {
        $this->addJavascriptFilename('templates/' . $this->_template . '/javascript/checkout_payment.js');
      }

// if no billing destination address was selected, use the customers own address as default
      if (isset($_SESSION['billto']) == false) {
        $_SESSION['billto'] = $osC_Customer->getDefaultAddressID();
      } else {
// verify the selected billing address
        $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
        $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
        $Qcheck->bindInt(':address_book_id', $_SESSION['billto']);
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') != 1) {
          $_SESSION['billto'] = $osC_Customer->getDefaultAddressID();

          unset($_SESSION['payment']);
        }
      }

      $order = new order;

      $total_weight = $_SESSION['cart']->show_weight();
      $total_count = $_SESSION['cart']->count_contents();

// load all enabled payment modules
      require('includes/classes/payment.php');
      $payment_modules = new payment;

      if ($this->_page_contents == 'checkout_payment.php') {
        $this->addJavascriptBlock($payment_modules->javascript_validation());
      }

      if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
        $messageStack->add('checkout_payment', $error['error'], 'error');
      }
    }
  }
?>
