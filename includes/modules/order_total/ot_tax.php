<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class ot_tax {
    var $title, $output;

    function ot_tax() {
      $this->code = 'ot_tax';
      $this->title = MODULE_ORDER_TOTAL_TAX_TITLE;
      $this->description = MODULE_ORDER_TOTAL_TAX_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_TAX_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_TAX_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $osC_Currencies;

      reset($order->info['tax_groups']);
      while (list($key, $value) = each($order->info['tax_groups'])) {
        if ($value > 0) {
          $this->output[] = array('title' => $key . ':',
                                  'text' => $osC_Currencies->format($value, $order->info['currency'], $order->info['currency_value']),
                                  'value' => $value);
        }
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_ORDER_TOTAL_TAX_STATUS');
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_TAX_STATUS', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER');
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Tax', 'MODULE_ORDER_TOTAL_TAX_STATUS', 'true', 'Do you want to display the order tax value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '3', 'Sort order of display.', '6', '2', now())");
    }

    function remove() {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
      $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qdel->bindRaw(':configuration_key', implode('", "', $this->keys()));
      $Qdel->execute();
    }
  }
?>
