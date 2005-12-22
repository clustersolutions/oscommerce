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

  class osC_Statistics_Products_Purchased extends osC_Statistics {

// Class constructor

    function osC_Statistics_Products_Purchased() {
      global $osC_Language;

      $osC_Language->load('modules/statistics/products_purchased.php');

      $this->_setIcon();
      $this->_setTitle();
    }

// Private methods

    function _setIcon() {
      $this->_icon = tep_icon('products.png', ICON_PRODUCTS, '16', '16');
    }

    function _setTitle() {
      $this->_title = MODULE_STATISTICS_PRODUCTS_PURCHASED_TITLE;
    }

    function _setHeader() {
      $this->_header = array(MODULE_STATISTICS_PRODUCTS_PURCHASED_TABLE_HEADING_PRODUCTS,
                             MODULE_STATISTICS_PRODUCTS_PURCHASED_TABLE_HEADING_PURCHASED);
    }

    function _setData() {
      global $osC_Database, $osC_Language;

      $this->_data = array();

      $this->_resultset = $osC_Database->query('select p.products_id, p.products_ordered, pd.products_name from :table_products p, :table_products_description pd where p.products_ordered > 0 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_ordered desc, pd.products_name');
      $this->_resultset->bindTable(':table_products', TABLE_PRODUCTS);
      $this->_resultset->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $this->_resultset->bindInt(':language_id', $osC_Language->getID());
      $this->_resultset->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      $this->_resultset->execute();

      while ($this->_resultset->next()) {
        $this->_data[] = array('<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $this->_resultset->valueInt('products_id')) . '">' . $this->_icon . '&nbsp;' . $this->_resultset->value('products_name') . '</a>',
                               $this->_resultset->valueInt('products_ordered'));
      }
    }
  }
?>
