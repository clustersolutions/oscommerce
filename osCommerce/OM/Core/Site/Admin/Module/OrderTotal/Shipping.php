<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\OrderTotal;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Shipping extends \osCommerce\OM\Core\Site\Admin\OrderTotal {
    var $_title,
        $_code = 'Shipping',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false,
        $_sort_order;

    public function __construct() {
      $this->_title = OSCOM::getDef('order_total_shipping_title');
      $this->_description = OSCOM::getDef('order_total_shipping_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS') && (MODULE_ORDER_TOTAL_SHIPPING_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER') ? MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER : null);
    }

    public function isInstalled() {
      return (bool)defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS');
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      parent::install();

      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Shipping', 'MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true', 'Do you want to display the order shipping cost?', '6', '1', 'osc_cfg_set_boolean_value(array(\'true\', \'false\'))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '2', 'Sort order of display.', '6', '2', now())");
    }

    public function getKeys() {
      if ( !isset($this->_keys) ) {
        $this->_keys = array('MODULE_ORDER_TOTAL_SHIPPING_STATUS',
                             'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER');
      }

      return $this->_keys;
    }
  }
?>
