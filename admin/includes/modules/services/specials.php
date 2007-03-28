<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_specials_Admin {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes;

    function osC_Services_specials_Admin() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/services/specials.php');

      $this->title = $osC_Language->get('services_specials_title');
      $this->description = $osC_Language->get('services_specials_description');
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Special Products', 'MAX_DISPLAY_SPECIAL_PRODUCTS', '9', 'Maximum number of products on special to display', '6', '0', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MAX_DISPLAY_SPECIAL_PRODUCTS');
    }
  }
?>
