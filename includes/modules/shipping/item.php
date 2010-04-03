<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Shipping_item extends osC_Shipping {
    protected $icon;
    protected $_title;
    protected $_code = 'item';
    protected $_status = false;
    protected $_sort_order;

    public function __construct() {
      $this->icon = '';

      $this->_title = __('shipping_item_title');
      $this->_description = __('shipping_item_description');
      $this->_status = (defined('MODULE_SHIPPING_ITEM_STATUS') && (MODULE_SHIPPING_ITEM_STATUS == 'True') ? true : false);
      $this->_sort_order = (defined('MODULE_SHIPPING_ITEM_SORT_ORDER') ? MODULE_SHIPPING_ITEM_SORT_ORDER : null);
    }

    public function initialize() {
      global $osC_Database, $osC_ShoppingCart;

      $this->tax_class = MODULE_SHIPPING_ITEM_TAX_CLASS;

      if ( ($this->_status === true) && ((int)MODULE_SHIPPING_ITEM_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_ITEM_ZONE);
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
      global $osC_ShoppingCart;

      $number_of_items = 0;

      foreach ( $osC_ShoppingCart->getProducts() as $product ) {
        $osC_Product = new osC_Product($product['id']);

        if ( $osC_Product->isTypeActionAllowed('apply_shipping_fees') ) {
          $number_of_items += $product['quantity'];
        }
      }

      $this->quotes = array('id' => $this->_code,
                            'module' => $this->_title,
                            'methods' => array(array('id' => $this->_code,
                                                     'title' => __('shipping_item_method'),
                                                     'cost' => (MODULE_SHIPPING_ITEM_COST * $number_of_items) + MODULE_SHIPPING_ITEM_HANDLING)),
                            'tax_class_id' => $this->tax_class);

      if (!empty($this->icon)) $this->quotes['icon'] = osc_image($this->icon, $this->_title);

      return $this->quotes;
    }
  }
?>
