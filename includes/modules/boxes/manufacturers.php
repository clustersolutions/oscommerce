<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_manufacturers extends osC_Modules {
    var $_title,
        $_code = 'manufacturers',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_manufacturers() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_manufacturers_heading');
    }

    function initialize() {
      global $osC_Database, $osC_Language;

      $Qmanufacturers = $osC_Database->query('select manufacturers_id as id, manufacturers_name as text from :table_manufacturers order by manufacturers_name');
      $Qmanufacturers->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
      $Qmanufacturers->setCache('manufacturers');
      $Qmanufacturers->execute();

      $manufacturers_array = array(array('id' => '', 'text' => $osC_Language->get('pull_down_default')));

      while ($Qmanufacturers->next()) {
        $manufacturers_array[] = $Qmanufacturers->toArray();
      }

      $Qmanufacturers->freeResult();

      $this->_content = '<form name="manufacturers" action="' . tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false) . '" method="get">' . "\n" .
                        osc_draw_pull_down_menu('manufacturers', $manufacturers_array, '', 'onchange="this.form.submit();" size="' . BOX_MANUFACTURERS_LIST_SIZE . '" style="width: 100%"') . tep_hide_session_id() . "\n" .
                        '</form>' . "\n";
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Manufacturers List Size', 'BOX_MANUFACTURERS_LIST_SIZE', '1', 'The size of the manufacturers pull down menu listing.', '6', '0', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_MANUFACTURERS_LIST_SIZE');
      }

      return $this->_keys;
    }
  }
?>
