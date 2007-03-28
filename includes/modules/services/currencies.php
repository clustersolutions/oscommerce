<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_currencies {
    function start() {
      global $osC_Language, $osC_Currencies;

      include('includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies();

      if ((isset($_SESSION['currency']) == false) || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == '1') && ($osC_Currencies->getCode($osC_Language->getCurrencyID()) != $_SESSION['currency']) ) ) {
        if (isset($_GET['currency']) && $osC_Currencies->exists($_GET['currency'])) {
          $_SESSION['currency'] = $_GET['currency'];
        } else {
          $_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == '1') ? $osC_Currencies->getCode($osC_Language->getCurrencyID()) : DEFAULT_CURRENCY;
        }

        if ( isset($_SESSION['cartID']) ) {
          unset($_SESSION['cartID']);
        }
      }

      return true;
    }

    function stop() {
      return true;
    }
  }
?>
