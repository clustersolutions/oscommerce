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

  class Shipping extends \osCommerce\OM\Site\Shop\OrderTotal {
    var $output;

    var $_title,
        $_code = 'Shipping',
        $_status = false,
        $_sort_order;

    public function __construct() {
      $this->output = array();

      $this->_title = OSCOM::getDef('order_total_shipping_title');
      $this->_description = OSCOM::getDef('order_total_shipping_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_SHIPPING_STATUS') && (MODULE_ORDER_TOTAL_SHIPPING_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER') ? MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER : null);
    }

    function process() {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');
      $OSCOM_Tax = Registry::get('Tax');
      $OSCOM_Currencies = Registry::get('Currencies');

      if ( $OSCOM_ShoppingCart->hasShippingMethod() ) {
        $OSCOM_ShoppingCart->addToTotal($OSCOM_ShoppingCart->getShippingMethod('cost'));

        if ( $OSCOM_ShoppingCart->getShippingMethod('tax_class_id') > 0 ) {
          $tax = $OSCOM_Tax->getTaxRate($OSCOM_ShoppingCart->getShippingMethod('tax_class_id'), $OSCOM_ShoppingCart->getShippingAddress('country_id'), $OSCOM_ShoppingCart->getShippingAddress('zone_id'));
          $tax_description = $OSCOM_Tax->getTaxRateDescription($OSCOM_ShoppingCart->getShippingMethod('tax_class_id'), $OSCOM_ShoppingCart->getShippingAddress('country_id'), $OSCOM_ShoppingCart->getShippingAddress('zone_id'));

          $OSCOM_ShoppingCart->addTaxAmount($OSCOM_Tax->calculate($OSCOM_ShoppingCart->getShippingMethod('cost'), $tax));
          $OSCOM_ShoppingCart->addTaxGroup($tax_description, $OSCOM_Tax->calculate($OSCOM_ShoppingCart->getShippingMethod('cost'), $tax));

          if ( DISPLAY_PRICE_WITH_TAX == '1' ) {
            $OSCOM_ShoppingCart->addToTotal($OSCOM_Tax->calculate($OSCOM_ShoppingCart->getShippingMethod('cost'), $tax));
            $OSCOM_ShoppingCart->_shipping_method['cost'] += $OSCOM_Tax->calculate($OSCOM_ShoppingCart->getShippingMethod('cost'), $tax);
          }
        }

        $this->output[] = array('title' => $OSCOM_ShoppingCart->getShippingMethod('title') . ':',
                                'text' => $OSCOM_Currencies->format($OSCOM_ShoppingCart->getShippingMethod('cost')),
                                'value' => $OSCOM_ShoppingCart->getShippingMethod('cost'));
      }
    }
  }
?>
