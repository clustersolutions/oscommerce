<?php
/*
  $Id:currencies.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_currencies {
    var $title = 'Currencies',
        $description = 'Set the default or selected currency.',
        $uninstallable = false,
        $depends = 'language',
        $precedes;

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
      }

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Use Default Language Currency', 'USE_DEFAULT_LANGUAGE_CURRENCY', '-1', 'Automatically use the currency set with the language (eg, German->Euro).', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('USE_DEFAULT_LANGUAGE_CURRENCY');
    }
  }
?>
