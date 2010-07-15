<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Shipping_table extends osC_Shipping {
    protected $icon;
    protected $_title;
    protected $_code = 'table';
    protected $_status = false;
    protected $_sort_order;

    public function __construct() {
      $this->icon = '';

      $this->_title = __('shipping_table_title');
      $this->_description = __('shipping_table_description');
      $this->_status = (defined('MODULE_SHIPPING_TABLE_STATUS') && (MODULE_SHIPPING_TABLE_STATUS == 'True') ? true : false);
      $this->_sort_order = (defined('MODULE_SHIPPING_TABLE_SORT_ORDER') ? MODULE_SHIPPING_TABLE_SORT_ORDER : null);
    }

    public function initialize() {
      global $osC_Database, $osC_ShoppingCart;

      $this->tax_class = MODULE_SHIPPING_TABLE_TAX_CLASS;

      if ( ($this->_status === true) && ((int)MODULE_SHIPPING_TABLE_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_TABLE_ZONE);
        $Qcheck->bindInt(':zone_country_id', $osC_ShoppingCart->getShippingAddress('country_id'));
        $Qcheck->execute();

        while ($Qcheck->next()) {
          if ($Qcheck->valueInt('zone_id') < 1) {
            $check_flag = true;
            break;
          } elseif ($Qcheck->valueInt('zone_id') == $osC_ShoppingCart->getShippingAddress('zone_id')) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->_status = false;
        }
      }
    }

    public function quote() {
      global $osC_Currencies, $osC_Tax, $osC_ShoppingCart, $osC_Weight;

      $order_total = 0;

      if (MODULE_SHIPPING_TABLE_MODE == 'price') {
        foreach ( $osC_ShoppingCart->getProducts() as $product ) {
          $osC_Product = new osC_Product($product['id']);

          if ( $osC_Product->isTypeActionAllowed('apply_shipping_fees') ) {
            $tax = $osC_Tax->getTaxRate($product['tax_class_id'], $osC_ShoppingCart->getTaxingAddress('country_id'), $osC_ShoppingCart->getTaxingAddress('zone_id'));

            $order_total += $osC_Currencies->addTaxRateToPrice($product['price'], $tax, $product['quantity']);
          }
        }
      } else {
        foreach ( $osC_ShoppingCart->getProducts() as $product ) {
          $osC_Product = new osC_Product($product['id']);

          if ( $osC_Product->isTypeActionAllowed('apply_shipping_fees') ) {
            $order_total += $osC_Weight->convert($product['weight'], $product['weight_class_id'], MODULE_SHIPPING_TABLE_WEIGHT_UNIT) * $product['quantity'];
          }
        }
      }

      $table_cost = preg_split("/[:,]/" , MODULE_SHIPPING_TABLE_COST);
      $size = sizeof($table_cost);
      for ($i=0, $n=$size; $i<$n; $i+=2) {
        if ($order_total <= $table_cost[$i]) {
          $shipping = $table_cost[$i+1];
          break;
        }
      }

      if (MODULE_SHIPPING_TABLE_MODE == 'weight') {
        $shipping = $shipping * $osC_ShoppingCart->numberOfShippingBoxes();
      }

      $this->quotes = array('id' => $this->_code,
                            'module' => $this->_title,
                            'methods' => array(array('id' => $this->_code,
                                                     'title' => __('shipping_table_method'),
                                                     'cost' => $shipping + MODULE_SHIPPING_TABLE_HANDLING)),
                            'tax_class_id' => $this->tax_class);

      if (!empty($this->icon)) $this->quotes['icon'] = osc_image($this->icon, $this->_title);

      return $this->quotes;
    }
  }
?>
