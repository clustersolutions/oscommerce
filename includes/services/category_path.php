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
      global $cPath, $cPath_array, $current_category_id, $osC_CategoryTree;

      if (isset($_GET['cPath'])) {
        $cPath = $_GET['cPath'];
      } elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
        $cPath = tep_get_product_path($_GET['products_id']);
      } else {
        $cPath = '';
      }

      if (!empty($cPath)) {
        $cPath_array = tep_parse_category_path($cPath);
        $cPath = implode('_', $cPath_array);

        $current_category_id = end($cPath_array);
      } else {
        $current_category_id = 0;
      }

      include('includes/classes/category_tree.php');
      $osC_CategoryTree = new osC_CategoryTree();

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Calculate Number Of Products In Each Category', 'SERVICES_CATEGORY_PATH_CALCULATE_PRODUCT_COUNT', '1', 'Recursively calculate how many products are in each category.', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
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
