<?php
/*
  $Id:specials.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_specials {
    var $title = 'Specials',
        $description = 'Enable Product Specials.',
        $uninstallable = true,
        $depends,
        $precedes;

    function start() {
      global $osC_Specials;

      require('includes/classes/specials.php');
      $osC_Specials = new osC_Specials();

      $osC_Specials->activateAll();
      $osC_Specials->expireAll();

      return true;
    }

    function stop() {
      return true;
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
