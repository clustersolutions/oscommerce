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
      return defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS');
    }

    public function install() {
      parent::install();

      $data = array(array('title' => 'Display Shipping',
                          'key' => 'MODULE_ORDER_TOTAL_SHIPPING_STATUS',
                          'value' => 'true',
                          'description' => 'Do you want to display the order shipping cost?',
                          'group_id' => '6',
                          'set_function' => 'osc_cfg_set_boolean_value(array(\'true\', \'false\'))'),
                    array('title' => 'Sort Order',
                          'key' => 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER',
                          'value' => '2',
                          'description' => 'Sort order of display.',
                          'group_id' => '6')
                   );

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function getKeys() {
      return array('MODULE_ORDER_TOTAL_SHIPPING_STATUS',
                   'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER');
    }
  }
?>
