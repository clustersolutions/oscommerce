<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (!class_exists('osC_Statistics')) {
    include('includes/classes/statistics.php');
  }

  class osC_Statistics_Low_Stock extends osC_Statistics {

// Class constructor

    function osC_Statistics_Low_Stock() {
      global $osC_Language;

      $osC_Language->load('modules/statistics/low_stock.php');

      $this->_setIcon();
      $this->_setTitle();
    }

// Private methods

    function _setIcon() {
      $this->_icon = tep_icon('products.png', ICON_PRODUCTS, '16', '16');
    }

    function _setTitle() {
      $this->_title = MODULE_STATISTICS_LOW_STOCK_TITLE;
    }

    function _setHeader() {
      $this->_header = array(MODULE_STATISTICS_LOW_STOCK_TABLE_HEADING_PRODUCTS,
                             MODULE_STATISTICS_LOW_STOCK_TABLE_HEADING_LEVEL);
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

      while ($this->_resultset->next()) {
        $this->_data[] = array('<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $this->_resultset->valueInt('products_id')) . '">' . $this->_icon . '&nbsp;' . $this->_resultset->value('products_name') . '</a>',
                               $this->_resultset->valueInt('products_quantity'));
      }
    }
  }
?>