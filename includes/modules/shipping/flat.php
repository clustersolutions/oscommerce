<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class flat {
    var $code, $title, $description, $icon, $enabled;

// class constructor
    function flat() {
      global $osC_Database, $order;

      $this->code = 'flat';
      $this->title = MODULE_SHIPPING_FLAT_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_FLAT_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_FLAT_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_FLAT_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_FLAT_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_FLAT_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_FLAT_ZONE);
        $Qcheck->bindInt(':zone_country_id', $order->delivery['country']['id']);
        $Qcheck->execute();

        while ($Qcheck->next()) {
          if ($Qcheck->valueInt('zone_id') < 1) {
            $check_flag = true;
            break;
          } elseif ($Qcheck->valueInt('zone_id') == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

// class methods
    function quote($method = '') {
      global $osC_Tax, $order;

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_FLAT_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => MODULE_SHIPPING_FLAT_TEXT_WAY,
                                                     'cost' => MODULE_SHIPPING_FLAT_COST)),
                            'tax' => 0);

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = $osC_Tax->getTaxRate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_SHIPPING_FLAT_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Flat Shipping', 'MODULE_SHIPPING_FLAT_STATUS', 'True', 'Do you want to offer flat rate shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Shipping Cost', 'MODULE_SHIPPING_FLAT_COST', '5.00', 'The shipping cost for all orders using this shipping method.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_FLAT_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_FLAT_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_FLAT_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
    }

    function remove() {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
      $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qdel->bindRaw(':configuration_key', implode('", "', $this->keys()));
      $Qdel->execute();
    }

    function keys() {
      return array('MODULE_SHIPPING_FLAT_STATUS', 'MODULE_SHIPPING_FLAT_COST', 'MODULE_SHIPPING_FLAT_TAX_CLASS', 'MODULE_SHIPPING_FLAT_ZONE', 'MODULE_SHIPPING_FLAT_SORT_ORDER');
    }
  }
?>
