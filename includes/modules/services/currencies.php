<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_currencies {
    var $title = 'Currencies',
        $description = 'Set the default or selected currency.',
        $uninstallable = false,
        $depends = 'language',
        $preceeds;

    function start() {
      if (PHP_VERSION < 4.1) {
        global $_GET;
      }

      global $osC_Currencies, $osC_Session;

      include('includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies;

      if (($osC_Session->exists('currency') == false) || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $osC_Session->value('currency')) ) ) {
        if (isset($_GET['currency']) && $osC_Currencies->exists($_GET['currency'])) {
          $currency = $_GET['currency'];
        } else {
          $currency = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
        }

        $osC_Session->set('currency', $currency);
      }

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use Default Language Currency', 'USE_DEFAULT_LANGUAGE_CURRENCY', 'false', 'Automatically use the currency set with the language (eg, German->Euro).', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
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
