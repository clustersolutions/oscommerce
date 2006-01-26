<?php
/*
  $Id: $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_upcoming_products extends osC_Modules {
    var $_title,
        $_code = 'upcoming_products',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'content';

/* Class constructor */

    function osC_Content_upcoming_products() {
      global $osC_Language;

      $this->_title = $osC_Language->get('upcoming_products_title');
    }

    function initialize() {
      $this->_content = 'dummy text';
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Entries To Display', 'MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', '10', 'Maximum number of upcoming products to display', '6', '0', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY');
      }

      return $this->_keys;
    }

  }
?>
