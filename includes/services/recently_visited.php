<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_recently_visited {
    var $title = 'Recently visited',
        $description = 'Enable Recently visited module.',
        $uninstallable = true,
        $depends = array('session', 'category_path'),
        $preceeds;

    function start() {
      global $osC_Services, $osC_RecentlyVisited;

      include('includes/classes/recently_visited.php');

      $osC_RecentlyVisited = new osC_RecentlyVisited();

      $osC_Services->addCallBeforePageContent('osC_RecentlyVisited', 'initialize');

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display latest products', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCTS', 'True', 'Display recently visited products.', '6', '0', 'tep_cfg_select_option(array(\'False\', \'True\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display product images', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES', 'True', 'Display the product image.', '6', '0', 'tep_cfg_select_option(array(\'False\', \'True\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display product prices', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES', 'True', 'Display the products price.', '6', '0', 'tep_cfg_select_option(array(\'False\', \'True\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum products to show', 'SERVICE_RECENTLY_VISITED_MAX_PRODUCTS', '5', 'Maximum number of recently visited products to show', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display latest categories', 'SERVICE_RECENTLY_VISITED_SHOW_CATEGORIES', 'True', 'Display recently visited categories.', '6', '0', 'tep_cfg_select_option(array(\'False\', \'True\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display category images', 'SERVICE_RECENTLY_VISITED_SHOW_CATEGORY_IMAGES', 'True', 'Display the category image.', '6', '0', 'tep_cfg_select_option(array(\'False\', \'True\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum categories to show', 'SERVICE_RECENTLY_VISITED_MAX_CATEGORIES', '3', 'Mazimum number of recently visited categories to show', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display latest searches', 'SERVICE_RECENTLY_VISITED_SHOW_SEARCHES', 'True', 'Show recent searches.', '6', '0', 'tep_cfg_select_option(array(\'False\', \'True\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum searches to show', 'SERVICE_RECENTLY_VISITED_MAX_SEARCHES', '3', 'Mazimum number of recent searches to display', '6', '0', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('SERVICE_RECENTLY_VISITED_SHOW_PRODUCTS', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES', 'SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES', 'SERVICE_RECENTLY_VISITED_MAX_PRODUCTS', 'SERVICE_RECENTLY_VISITED_SHOW_CATEGORIES', 'SERVICE_RECENTLY_VISITED_SHOW_CATEGORY_IMAGES', 'SERVICE_RECENTLY_VISITED_MAX_CATEGORIES', 'SERVICE_RECENTLY_VISITED_SHOW_SEARCHES', 'SERVICE_RECENTLY_VISITED_MAX_SEARCHES');
    }
  }
?>
