<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class RecentlyVisited {
    var $title,
        $description,
        $uninstallable = true,
        $depends = array('Session', 'CategoryPath'),
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/recently_visited.php');

      $this->title = OSCOM::getDef('services_recently_visited_title');
      $this->description = OSCOM::getDef('services_recently_visited_description');
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Display latest products', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCTS', '1', 'Display recently visited products.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Display product images', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES', '1', 'Display the product image.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Display product prices', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES', '1', 'Display the products price.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum products to show', 'SERVICE_RECENTLY_VISITED_MAX_PRODUCTS', '5', 'Maximum number of recently visited products to show', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Display latest categories', 'SERVICE_RECENTLY_VISITED_SHOW_CATEGORIES', '1', 'Display recently visited categories.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum categories to show', 'SERVICE_RECENTLY_VISITED_MAX_CATEGORIES', '3', 'Mazimum number of recently visited categories to show', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Display latest searches', 'SERVICE_RECENTLY_VISITED_SHOW_SEARCHES', '1', 'Show recent searches.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
      $OSCOM_Database->simpleQuery("insert into " . DB_TABLE_PREFIX . "configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum searches to show', 'SERVICE_RECENTLY_VISITED_MAX_SEARCHES', '3', 'Mazimum number of recent searches to display', '6', '0', now())");
    }

    public function remove() {
      $OSCOM_Database = Registry::get('Database');

      $OSCOM_Database->simpleQuery("delete from " . DB_TABLE_PREFIX . "configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    public function keys() {
      return array('SERVICE_RECENTLY_VISITED_SHOW_PRODUCTS',
                   'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES',
                   'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES',
                   'SERVICE_RECENTLY_VISITED_MAX_PRODUCTS',
                   'SERVICE_RECENTLY_VISITED_SHOW_CATEGORIES',
                   'SERVICE_RECENTLY_VISITED_MAX_CATEGORIES',
                   'SERVICE_RECENTLY_VISITED_SHOW_SEARCHES',
                   'SERVICE_RECENTLY_VISITED_MAX_SEARCHES');
    }
  }
?>
