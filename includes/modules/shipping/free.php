<?php
/*
  $Id: flat.php 421 2006-02-08 17:53:17Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Shipping_free extends osC_Shipping {
    var $icon;

    var $_title,
        $_code = 'free',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_status = false;

// class constructor
    function osC_Shipping_free() {
      global $osC_Language;

      $this->icon = '';

      $this->_title = $osC_Language->get('shipping_free_title');
      $this->_description = $osC_Language->get('shipping_free_description');
      $this->_status = (defined('MODULE_SHIPPING_FREE_STATUS') && (MODULE_SHIPPING_FREE_STATUS == 'True') ? true : false);
    }

    function initialize() {
      global $osC_Database, $osC_ShoppingCart;

      if ($osC_ShoppingCart->getTotal() >= MODULE_SHIPPING_FREE_MINIMUM_ORDER) {
        if ( ($this->_status === true) && ((int)MODULE_SHIPPING_FREE_ZONE > 0) ) {
          $check_flag = false;

          $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and (zone_country_id = :zone_country_id or zone_country_id = 0) order by zone_id');
          $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
          $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_FREE_ZONE);
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

          $this->_status = $check_flag;
        } else {
          $this->_status = false;
        }
      } else {
        $this->_status = false;
      }
    }

// class methods
    function quote() {
      global $osC_Language, $osC_Currencies;

      $this->quotes = array('id' => $this->_code,
                            'module' => $this->_title,
                            'methods' => array(array('id' => $this->_code,
                                                     'title' => sprintf($osC_Language->get('free_shipping_description'), $osC_Currencies->format(MODULE_SHIPPING_FREE_MINIMUM_ORDER)),
                                                     'cost' => 0)),
                            'tax_class_id' => 0);

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->_title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_SHIPPING_FREE_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Free Shipping', 'MODULE_SHIPPING_FREE_STATUS', 'True', 'Do you want to offer flat rate shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Shipping Cost', 'MODULE_SHIPPING_FREE_MINIMUM_ORDER', '20', 'The minimum order amount to apply free shipping to.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_FREE_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_SHIPPING_FREE_STATUS',
                             'MODULE_SHIPPING_FREE_MINIMUM_ORDER',
                             'MODULE_SHIPPING_FREE_ZONE');
      }

      return $this->_keys;
    }
  }
?>
