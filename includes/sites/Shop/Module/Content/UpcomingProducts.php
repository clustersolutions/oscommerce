<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
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
      global $osC_Database, $osC_Language, $osC_Currencies;

      $Qupcoming = $osC_Database->query('select p.products_id, pa.value as date_expected from :table_products p, :table_templates_boxes tb, :table_product_attributes pa where tb.code = :code and tb.id = pa.id and to_days(str_to_date(pa.value, "%Y-%m-%d")) >= to_days(now()) and pa.products_id = p.products_id and p.products_status = :products_status order by pa.value limit :max_display_upcoming_products');
      $Qupcoming->bindTable(':table_products', TABLE_PRODUCTS);
      $Qupcoming->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
      $Qupcoming->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
      $Qupcoming->bindValue(':code', 'date_available');
      $Qupcoming->bindInt(':products_status', 1);
      $Qupcoming->bindInt(':max_display_upcoming_products', MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY);

      if (MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE > 0) {
        $Qupcoming->setCache('upcoming_products-' . $osC_Language->getCode() . '-' . $osC_Currencies->getCode(), MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE);
      }

      $Qupcoming->execute();

      if ($Qupcoming->numberOfRows() > 0) {
        $this->_content = '<ol style="list-style: none;">';

        while ($Qupcoming->next()) {
          $osC_Product = new osC_Product($Qupcoming->valueInt('products_id'));

          $this->_content .= '<li>' . osC_DateTime::getLong($Qupcoming->value('date_expected')) . ': ' . osc_link_object(osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()), $osC_Product->getTitle()) . ' ' . $osC_Product->getPriceFormated(true) . '</li>';
        }

        $this->_content .= '</ol>';
      }

      $Qupcoming->freeResult();
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Entries To Display', 'MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', '10', 'Maximum number of upcoming products to display', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE', '1440', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE');
      }

      return $this->_keys;
    }

  }
?>
