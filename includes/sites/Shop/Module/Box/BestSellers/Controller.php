<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Box\BestSellers;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;

  class Controller extends \osCommerce\OM\Modules {
    var $_title,
        $_code = 'BestSellers',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_best_sellers_heading');
    }

    public function initialize() {
      global $current_category_id;

      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      if ( isset($current_category_id) && ($current_category_id > 0) ) {
        $Qbestsellers = $OSCOM_Database->query('select distinct p.products_id, pd.products_name, pd.products_keyword from :table_products p, :table_products_description pd, :table_products_to_categories p2c, :table_categories c where p.products_status = 1 and p.products_ordered > 0 and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and :current_category_id in (c.categories_id, c.parent_id) order by p.products_ordered desc, pd.products_name limit :max_display_bestsellers');
        $Qbestsellers->bindInt(':language_id', $OSCOM_Language->getID());
        $Qbestsellers->bindInt(':current_category_id', $current_category_id);
        $Qbestsellers->bindInt(':max_display_bestsellers', BOX_BEST_SELLERS_MAX_LIST);

        if ( BOX_BEST_SELLERS_CACHE > 0 ) {
          $Qbestsellers->setCache('box_best_sellers-' . $current_category_id . '-' . $OSCOM_Language->getCode(), BOX_BEST_SELLERS_CACHE);
        }

        $Qbestsellers->execute();
      } else {
        $Qbestsellers = $OSCOM_Database->query('select p.products_id, pd.products_name, pd.products_keyword from :table_products p, :table_products_description pd where p.products_status = 1 and p.products_ordered > 0 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_ordered desc, pd.products_name limit :max_display_bestsellers');
        $Qbestsellers->bindInt(':language_id', $OSCOM_Language->getID());
        $Qbestsellers->bindInt(':max_display_bestsellers', BOX_BEST_SELLERS_MAX_LIST);

        if ( BOX_BEST_SELLERS_CACHE > 0 ) {
          $Qbestsellers->setCache('box_best_sellers-0-' . $OSCOM_Language->getCode(), BOX_BEST_SELLERS_CACHE);
        }

        $Qbestsellers->execute();
      }

      if ( $Qbestsellers->numberOfRows() >= BOX_BEST_SELLERS_MIN_LIST ) {
        $this->_content = '<ol style="margin: 0; padding: 0 0 0 20px;">';

        while ( $Qbestsellers->next() ) {
          $this->_content .= '<li>' . osc_link_object(OSCOM::getLink(null, 'Products', $Qbestsellers->value('products_keyword')), $Qbestsellers->value('products_name')) . '</li>';
        }

        $this->_content .= '</ol>';
      }

      $Qbestsellers->freeResult();
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      parent::install();

      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Minimum List Size', 'BOX_BEST_SELLERS_MIN_LIST', '3', 'Minimum amount of products that must be shown in the listing', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum List Size', 'BOX_BEST_SELLERS_MAX_LIST', '10', 'Maximum amount of products to show in the listing', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'BOX_BEST_SELLERS_CACHE', '60', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    public function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_BEST_SELLERS_MIN_LIST',
                             'BOX_BEST_SELLERS_MAX_LIST',
                             'BOX_BEST_SELLERS_CACHE');
      }

      return $this->_keys;
    }
  }
?>
