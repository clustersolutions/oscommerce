<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Checkout_Payment {

/* Public variables */

    var $page_contents = 'checkout_payment.php';

/* Private variables */

    var $_module = 'payment';

/* Class constructor */

    function osC_Checkout_Payment() {
      global $osC_Database, $osC_Session, $osC_Customer, $osC_Services, $breadcrumb, $cart, $total_weight, $total_count, $payment_modules;

      if ($cart->count_contents() < 1) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, '', 'SSL'));
      }

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_CHECKOUT_PAYMENT, tep_href_link(FILENAME_CHECKOUT, $this->_module, 'SSL'));
      }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
      if ($osC_Session->exists('shipping') == false) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
      }

// avoid hack attempts during the checkout procedure by checking the internal cartID
      if (isset($cart->cartID) && $osC_Session->exists('cartID')) {
        if ($cart->cartID != $osC_Session->value('cartID')) {
          tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'));
        }
      }

// Stock Check
      if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
        $products = $cart->get_products();
        for ($i=0, $n=sizeof($products); $i<$n; $i++) {
          if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
            tep_redirect(tep_href_link(FILENAME_CHECKOUT, 'SSL'));
            break;
          }
        }
      }

// redirect to the billing address page when no default address exists
      if ($osC_Customer->hasDefaultAddress() === false) {
        $this->page_contents = 'payment_address.php';
      }

// if no billing destination address was selected, use the customers own address as default
      if ($osC_Session->exists('billto') == false) {
        $osC_Session->set('billto', $osC_Customer->default_address_id);
      } else {
// verify the selected billing address
        $Qcheck = $osC_Database->query('select count(*) as total from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
        $Qcheck->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qcheck->bindInt(':customers_id', $osC_Customer->id);
        $Qcheck->bindInt(':address_book_id', $osC_Session->value('billto'));
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') != 1) {
          $osC_Session->set('billto', $osC_Customer->default_address_id);

          $osC_Session->remove('payment');
        }
      }

      $total_weight = $cart->show_weight();
      $total_count = $cart->count_contents();

// load all enabled payment modules
      require(DIR_WS_CLASSES . 'payment.php');
      $payment_modules = new payment;

      if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
        $messageStack->add('checkout_payment', $error['error'], 'error');
      }
    }

/* Public methods */

    function getPageContentsFile() {
      return $this->page_contents;
    }

/* Private methods */

    function _process() {
    }
  }
?>
