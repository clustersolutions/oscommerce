<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  if ( !class_exists('osC_Statistics') ) {
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
      $this->_icon = osc_icon('products.png', ICON_PRODUCTS);
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
      global $osC_Database, $osC_Language;

      $this->_data = array();

      $this->_resultset = $osC_Database->query('select p.products_id, pd.products_name, pd.products_viewed, l.name, l.code from :table_products p, :table_products_description pd, :table_languages l where p.products_id = pd.products_id and l.languages_id = pd.language_id order by pd.products_viewed desc');
      $this->_resultset->bindTable(':table_products', TABLE_PRODUCTS);
      $this->_resultset->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $this->_resultset->bindTable(':table_languages', TABLE_LANGUAGES);
      $this->_resultset->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      $this->_resultset->execute();

      while ( $this->_resultset->next() ) {
        $this->_data[] = array(osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'products&pID=' . $this->_resultset->valueInt('products_id') . '&action=preview'), $this->_icon . '&nbsp;' . $this->_resultset->value('products_name')),
                               $osC_Language->showImage($this->_resultset->value('code')),
                               $this->_resultset->valueInt('products_viewed'));
      }
    }
  }
?>
