<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\OrderTotal;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;

  class Tax extends \osCommerce\OM\Site\Shop\OrderTotal {
    var $output;

    var $_title,
        $_code = 'Tax',
        $_status = false,
        $_sort_order;

    public function __construct() {
      $this->output = array();

      $this->_title = OSCOM::getDef('order_total_tax_title');
      $this->_description = OSCOM::getDef('order_total_tax_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_TAX_STATUS') && (MODULE_ORDER_TOTAL_TAX_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_TAX_SORT_ORDER') ? MODULE_ORDER_TOTAL_TAX_SORT_ORDER : null);
    }

    function process() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Currencies = Registry::get('Currencies');

      foreach ( $OSCOM_ShoppingCart->getTaxGroups() as $key => $value ) {
        if ( $value > 0 ) {
          if ( DISPLAY_PRICE_WITH_TAX == '1' ) {
            $OSCOM_ShoppingCart->addToTotal($value);
          }

          $this->output[] = array('title' => $key . ':',
                                  'text' => $OSCOM_Currencies->format($value),
                                  'value' => $value);
        }
      }
    }
  }
?>
