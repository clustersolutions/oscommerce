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

  class osC_Statistics_Products_Viewed extends osC_Statistics {

// Class constructor

    function osC_Statistics_Products_Viewed() {
      global $osC_Language;

      $osC_Language->loadConstants('modules/statistics/products_viewed.php');

      $this->_setIcon();
      $this->_setTitle();
    }

// Private methods

    function _setIcon() {
      $this->_icon = tep_icon('products.png', ICON_PRODUCTS, '16', '16');
    }

    function _setTitle() {
      $this->_title = MODULE_STATISTICS_PRODUCTS_VIEWED_TITLE;
    }

    function _setHeader() {
      $this->_header = array(MODULE_STATISTICS_PRODUCTS_VIEWED_TABLE_HEADING_PRODUCTS,
                             MODULE_STATISTICS_PRODUCTS_VIEWED_TABLE_HEADING_LANGUAGE,
                             MODULE_STATISTICS_PRODUCTS_VIEWED_TABLE_HEADING_VIEWED);
    }

    function _setData() {
      global $osC_Database;

      $this->_data = array();

      $this->_resultset = $osC_Database->query('select p.products_id, pd.products_name, pd.products_viewed, l.name, l.directory, l.image from :table_products p, :table_products_description pd, :table_languages l where p.products_id = pd.products_id and l.languages_id = pd.language_id order by pd.products_viewed desc');
      $this->_resultset->bindTable(':table_products', TABLE_PRODUCTS);
      $this->_resultset->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $this->_resultset->bindTable(':table_languages', TABLE_LANGUAGES);
      $this->_resultset->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      $this->_resultset->execute();

      while ($this->_resultset->next()) {
        $this->_data[] = array('<a href="' . tep_href_link(FILENAME_PRODUCTS, 'pID=' . $this->_resultset->valueInt('products_id') . '&action=new_product_preview&read=only') . '">' . $this->_icon . '&nbsp;' . $this->_resultset->value('products_name') . '</a>',
                               tep_image('../includes/languages/' . $this->_resultset->value('directory') . '/images/' . $this->_resultset->value('image'), $this->_resultset->value('name')),
                               $this->_resultset->valueInt('products_viewed'));
      }
    }
  }
?>
