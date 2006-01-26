<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class item {
    var $code, $title, $description, $icon, $enabled;

// class constructor
    function item() {
      global $osC_Database, $osC_Language, $order;

      $this->code = 'item';
      $this->title = $osC_Language->get('shipping_item_title');
      $this->description = $osC_Language->get('shipping_item_description');
      $this->sort_order = MODULE_SHIPPING_ITEM_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_ITEM_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_ITEM_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_ITEM_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_ITEM_ZONE);
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
      global $osC_Language, $osC_Tax, $order, $total_count;

      $this->quotes = array('id' => $this->code,
                            'module' => $this->title,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => $osC_Language->get('shipping_item_method'),
                                                     'cost' => (MODULE_SHIPPING_ITEM_COST * $total_count) + MODULE_SHIPPING_ITEM_HANDLING)),
                            'tax' => 0);

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = $osC_Tax->getTaxRate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_SHIPPING_ITEM_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database, $osC_Languange;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Item Shipping', 'MODULE_SHIPPING_ITEM_STATUS', 'True', 'Do you want to offer per item rate shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Shipping Cost', 'MODULE_SHIPPING_ITEM_COST', '2.50', 'The shipping cost will be multiplied by the number of items in an order that uses this shipping method.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Handling Fee', 'MODULE_SHIPPING_ITEM_HANDLING', '0', 'Handling fee for this shipping method.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_ITEM_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_ITEM_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_ITEM_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");

      foreach ($osC_Language->getAll() as $key => $value) {
        foreach ($osC_Language->extractDefinitions($key . '/modules/shipping/' . $this->code . '.xml') as $def) {
          $Qcheck = $osC_Database->query('select id from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id limit 1');
          $Qcheck->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qcheck->bindValue(':definition_key', $def['key']);
          $Qcheck->bindValue(':content_group', $def['group']);
          $Qcheck->bindInt(':languages_id', $value['id']);
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() === 1) {
            $Qdef = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and content_group = :content_group and languages_id = :languages_id');
          } else {
            $Qdef = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
          }
          $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qdef->bindInt(':languages_id', $value['id']);
          $Qdef->bindValue(':content_group', $def['group']);
          $Qdef->bindValue(':definition_key', $def['key']);
          $Qdef->bindValue(':definition_value', $def['value']);
          $Qdef->execute();
        }
      }

      osC_Cache::clear('languages');
    }

    function remove() {
      global $osC_Database, $osC_Language;

      $Qdel = $osC_Database->query('delete from :table_configuration where configuration_key in (":configuration_key")');
      $Qdel->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qdel->bindRaw(':configuration_key', implode('", "', $this->keys()));
      $Qdel->execute();

      foreach ($osC_Language->extractDefinitions($osC_Language->getCode() . '/modules/shipping/' . $this->code . '.xml') as $def) {
        $Qdel = $osC_Database->query('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
        $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdel->bindValue(':definition_key', $def['key']);
        $Qdel->bindValue(':content_group', $def['group']);
        $Qdel->execute();
      }

      osC_Cache::clear('languages');
    }

    function keys() {
      return array('MODULE_SHIPPING_ITEM_STATUS', 'MODULE_SHIPPING_ITEM_COST', 'MODULE_SHIPPING_ITEM_HANDLING', 'MODULE_SHIPPING_ITEM_TAX_CLASS', 'MODULE_SHIPPING_ITEM_ZONE', 'MODULE_SHIPPING_ITEM_SORT_ORDER');
    }
  }
?>
