<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Box\OrderHistory;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;

  class Controller extends \osCommerce\OM\Modules {
    var $_title,
        $_code = 'OrderHistory',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_order_history_heading');
    }

    public function initialize() {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_Language = Registry::get('Language');

      if ( $OSCOM_Customer->isLoggedOn() ) {
        $Qorders = $OSCOM_Database->query('select distinct op.products_id from :table_orders o, :table_orders_products op, :table_products p where o.customers_id = :customers_id and o.orders_id = op.orders_id and op.products_id = p.products_id and p.products_status = 1 group by products_id order by o.date_purchased desc limit :limit');
        $Qorders->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qorders->bindInt(':limit', BOX_ORDER_HISTORY_MAX_LIST);
        $Qorders->execute();

        if ( $Qorders->numberOfRows() ) {
          $product_ids = '';

          while ( $Qorders->next() ) {
            $product_ids .= $Qorders->valueInt('products_id') . ',';
          }

          $product_ids = substr($product_ids, 0, -1);

          $Qproducts = $OSCOM_Database->query('select products_id, products_name, products_keyword from :table_products_description where products_id in (:products_id) and language_id = :language_id order by products_name');
          $Qproducts->bindRaw(':products_id', $product_ids);
          $Qproducts->bindInt(':language_id', $OSCOM_Language->getID());
          $Qproducts->execute();

          $this->_content = '<ol style="list-style: none; margin: 0; padding: 0;">';

          while ( $Qproducts->next() ) {
            $this->_content .= '<li>' . osc_link_object(OSCOM::getLink(null, 'Products', $Qproducts->value('products_keyword')), $Qproducts->value('products_name')) . '</li>';
          }

          $this->_content .= '</ol>';
        }
      }
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      parent::install();

      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum List Size', 'BOX_ORDER_HISTORY_MAX_LIST', '5', 'Maximum amount of products to show in the listing', '6', '0', now())");
    }

    public function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_ORDER_HISTORY_MAX_LIST');
      }

      return $this->_keys;
    }
  }
?>
