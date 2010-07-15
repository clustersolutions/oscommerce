<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Content\UpcomingProducts;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\DateTime;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'UpcomingProducts',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Content';

    public function __construct() {
      $this->_title = OSCOM::getDef('upcoming_products_title');
    }

    public function initialize() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Currencies = Registry::get('Currencies');

      $Qupcoming = $OSCOM_Database->query('select p.products_id, pa.value as date_expected from :table_products p, :table_templates_boxes tb, :table_product_attributes pa where tb.code = :code and tb.id = pa.id and to_days(str_to_date(pa.value, "%Y-%m-%d")) >= to_days(now()) and pa.products_id = p.products_id and p.products_status = :products_status order by pa.value limit :max_display_upcoming_products');
      $Qupcoming->bindValue(':code', 'DateAvailable');
      $Qupcoming->bindInt(':products_status', 1);
      $Qupcoming->bindInt(':max_display_upcoming_products', MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY);

      if ( MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE > 0 ) {
        $Qupcoming->setCache('upcoming_products-' . $OSCOM_Language->getCode() . '-' . $OSCOM_Currencies->getCode(), MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE);
      }

      $Qupcoming->execute();

      if ( $Qupcoming->numberOfRows() > 0 ) {
        $this->_content = '<ol style="list-style: none;">';

        while ( $Qupcoming->next() ) {
          $OSCOM_Product = new Product($Qupcoming->valueInt('products_id'));

          $this->_content .= '<li>' . DateTime::getLong($Qupcoming->value('date_expected')) . ': ' . osc_link_object(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword()), $OSCOM_Product->getTitle()) . ' ' . $OSCOM_Product->getPriceFormated(true) . '</li>';
        }

        $this->_content .= '</ol>';
      }

      $Qupcoming->freeResult();
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      parent::install();

      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Entries To Display', 'MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', '10', 'Maximum number of upcoming products to display', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE', '1440', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    public function getKeys() {
      if ( !isset($this->_keys) ) {
        $this->_keys = array('MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY', 'MODULE_CONTENT_UPCOMING_PRODUCTS_CACHE');
      }

      return $this->_keys;
    }

  }
?>
