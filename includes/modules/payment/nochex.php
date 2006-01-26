<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class nochex {
    var $code, $title, $description, $sort_order, $enabled = false;

    function nochex() {
      global $osC_Language;

      $this->code = 'nochex';
      $this->title = $osC_Language->get('payment_nochex_title');
      $this->description = $osC_Language->get('payment_nochex_description');

      if (defined('MODULE_PAYMENT_NOCHEX_STATUS')) {
        $this->initialize();
      }
    }

    function initialize() {
      global $order;

      $this->sort_order = MODULE_PAYMENT_NOCHEX_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_NOCHEX_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.nochex.com/nochex.dll/checkout';
    }

    function update_status() {
      global $osC_Database, $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_NOCHEX_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_PAYMENT_NOCHEX_ZONE);
        $Qcheck->bindInt(':zone_country_id', $order->billing['country']['id']);
        $Qcheck->execute();

        while ($Qcheck->next()) {
          if ($Qcheck->valueInt('zone_id') < 1) {
            $check_flag = true;
            break;
          } elseif ($Qcheck->valueInt('zone_id') == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title);
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return false;
    }

    function process_button() {
      global $order, $osC_Currencies, $osC_Customer;

      $process_button_string = osc_draw_hidden_field('cmd', '_xclick') .
                               osc_draw_hidden_field('email', MODULE_PAYMENT_NOCHEX_ID) .
                               osc_draw_hidden_field('amount', number_format($order->info['total'] * $osC_Currencies->currencies['GBP']['value'], $osC_Currencies->currencies['GBP']['decimal_places'])) .
                               osc_draw_hidden_field('ordernumber', $osC_Customer->getID() . '-' . date('Ymdhis')) .
                               osc_draw_hidden_field('returnurl', tep_href_link(FILENAME_CHECKOUT, 'process', 'SSL')) .
                               osc_draw_hidden_field('cancel_return', tep_href_link(FILENAME_CHECKOUT, 'payment', 'SSL'));

      return $process_button_string;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      return false;
    }

    function output_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_PAYMENT_NOCHEX_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database, $osC_Language;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable NOCHEX Module', 'MODULE_PAYMENT_NOCHEX_STATUS', 'True', 'Do you want to accept NOCHEX payments?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('E-Mail Address', 'MODULE_PAYMENT_NOCHEX_ID', 'you@yourbuisness.com', 'The e-mail address to use for the NOCHEX service', '6', '4', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_NOCHEX_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_NOCHEX_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");

      foreach ($osC_Language->getAll() as $key => $value) {
        foreach ($osC_Language->extractDefinitions($key . '/modules/payment/' . $this->code . '.xml') as $def) {
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

      foreach ($osC_Language->extractDefinitions($osC_Language->getCode() . '/modules/payment/' . $this->code . '.xml') as $def) {
        $Qdel = $osC_Database->query('delete from :table_languages_definitions where definition_key = :definition_key and content_group = :content_group');
        $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdel->bindValue(':definition_key', $def['key']);
        $Qdel->bindValue(':content_group', $def['group']);
        $Qdel->execute();
      }

      osC_Cache::clear('languages');
    }

    function keys() {
      return array('MODULE_PAYMENT_NOCHEX_STATUS', 'MODULE_PAYMENT_NOCHEX_ID', 'MODULE_PAYMENT_NOCHEX_ZONE', 'MODULE_PAYMENT_NOCHEX_ORDER_STATUS_ID', 'MODULE_PAYMENT_NOCHEX_SORT_ORDER');
    }
  }
?>
