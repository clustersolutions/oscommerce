<?php
/*
  $Id: banner.php,v 1.3 2004/11/28 18:32:34 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_banner {
    var $title = 'Banner',
        $description = 'Banner management features for the catalog.',
        $uninstallable = true,
        $depends,
        $preceeds;

    function start() {
      global $osC_Banner;

      require('includes/classes/banner.php');
      $osC_Banner = new osC_Banner();

      $osC_Banner->activateAll();
      $osC_Banner->expireAll();

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Duplicate Banners', 'SERVICE_BANNER_SHOW_DUPLICATE', 'False', 'Show duplicate banners in the same banner group on the same page?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('SERVICE_BANNER_SHOW_DUPLICATE');
    }
  }
?>
