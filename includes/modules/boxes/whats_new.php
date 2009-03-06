<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Boxes_whats_new extends osC_Modules {
    var $_title,
        $_code = 'whats_new',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function __construct() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_whats_new_heading');
    }

    function initialize() {
      global $osC_Cache, $osC_Database, $osC_Services, $osC_Currencies, $osC_Specials, $osC_Language, $osC_Image;

      $this->_title_link = osc_href_link(FILENAME_PRODUCTS, 'new');

      $data = array();

      if ( (BOX_WHATS_NEW_CACHE > 0) && $osC_Cache->read('box-whats_new-' . $osC_Language->getCode() . '-' . $osC_Currencies->getCode(), BOX_WHATS_NEW_CACHE) ) {
        $data = $osC_Cache->getCache();
      } else {
        $Qnew = $osC_Database->query('select products_id from :table_products where products_status = :products_status order by products_date_added desc limit :max_random_select_new');
        $Qnew->bindTable(':table_products', TABLE_PRODUCTS);
        $Qnew->bindInt(':products_status', 1);
        $Qnew->bindInt(':max_random_select_new', BOX_WHATS_NEW_RANDOM_SELECT);
        $Qnew->executeRandomMulti();

        if ( $Qnew->numberOfRows() ) {
          $osC_Product = new osC_Product($Qnew->valueInt('products_id'));

          $data = $osC_Product->getData();

          $data['display_price'] = $osC_Product->getPriceFormated(true);
          $data['display_image'] = $osC_Product->getImage();
        }

        $osC_Cache->write($data);
      }

      if ( !empty($data) ) {
        $this->_content = '';

        if ( !empty($data['display_image']) ) {
          $this->_content .= osc_link_object(osc_href_link(FILENAME_PRODUCTS, $data['keyword']), $osC_Image->show($data['display_image'], $data['name'])) . '<br />';
        }

        $this->_content .= osc_link_object(osc_href_link(FILENAME_PRODUCTS, $data['keyword']), $data['name']) . '<br />' . $data['display_price'];
      }
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Random New Product Selection', 'BOX_WHATS_NEW_RANDOM_SELECT', '10', 'Select a random new product from this amount of the newest products available', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'BOX_WHATS_NEW_CACHE', '1', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    function getKeys() {
      if ( !isset($this->_keys) ) {
        $this->_keys = array('BOX_WHATS_NEW_RANDOM_SELECT', 'BOX_WHATS_NEW_CACHE');
      }

      return $this->_keys;
    }
  }
?>
