<?php
/*
  $Id:category_path.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_category_path {
    var $title = 'Category Path',
        $description = 'Parses the category path.',
        $uninstallable = false,
        $depends,
        $precedes;

    function start() {
      global $osC_CategoryTree;

      osC_Services_category_path::process();

      include('includes/classes/category_tree.php');
      $osC_CategoryTree = new osC_CategoryTree();

      return true;
    }

    function process($id = null) {
      global $cPath, $cPath_array, $current_category_id, $osC_CategoryTree;

      $cPath = '';
      $cPath_array = array();
      $current_category_id = 0;

      if (isset($_GET['cPath'])) {
        $cPath = $_GET['cPath'];
      } elseif (!empty($id)) {
        $cPath = $osC_CategoryTree->buildBreadcrumb($id);
      }

      if (!empty($cPath)) {
        $cPath_array = array_unique(array_filter(explode('_', $cPath), 'is_numeric'));
        $cPath = implode('_', $cPath_array);
        $current_category_id = end($cPath_array);
      }
    }

    function stop() {
      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Calculate Number Of Products In Each Category', 'SERVICES_CATEGORY_PATH_CALCULATE_PRODUCT_COUNT', '1', 'Recursively calculate how many products are in each category.', '6', '0', 'osc_cfg_use_get_boolean_value', 'osc_cfg_set_boolean_value(array(1, -1))', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('SERVICES_CATEGORY_PATH_CALCULATE_PRODUCT_COUNT');
    }
  }
?>
