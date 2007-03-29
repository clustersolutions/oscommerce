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

  if ( !class_exists('osC_Statistics') ) {
    include('includes/classes/statistics.php');
  }

  class osC_Statistics_Low_Stock extends osC_Statistics {

// Class constructor

    function osC_Statistics_Low_Stock() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/statistics/low_stock.php');

      $this->_setIcon();
      $this->_setTitle();
    }

// Private methods

    function _setIcon() {
      $this->_icon = osc_icon('products.png');
    }

    function _setTitle() {
      global $osC_Language;

      $this->_title = $osC_Language->get('statistics_low_stock_title');
    }

    function _setHeader() {
      global $osC_Language;

      $this->_header = array($osC_Language->get('statistics_low_stock_table_heading_products'),
                             $osC_Language->get('statistics_low_stock_table_heading_level'));
    }

    function _setData() {
      global $osC_Database, $osC_Language;

      $this->_data = array();

      $this->_resultset = $osC_Database->query('select p.products_id, pd.products_name, products_quantity from :table_products p, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_quantity <= :stock_reorder_level order by p.products_quantity desc');
      $this->_resultset->bindTable(':table_products', TABLE_PRODUCTS);
      $this->_resultset->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $this->_resultset->bindInt(':language_id', $osC_Language->getID());
      $this->_resultset->bindInt(':stock_reorder_level', STOCK_REORDER_LEVEL);
      $this->_resultset->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      $this->_resultset->execute();

      while ( $this->_resultset->next() ) {
        $this->_data[] = array(osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'products&pID=' . $this->_resultset->valueInt('products_id') . '&action=preview'), $this->_icon . '&nbsp;' . $this->_resultset->value('products_name')),
                               $this->_resultset->valueInt('products_quantity'));
      }
    }
  }
?>
