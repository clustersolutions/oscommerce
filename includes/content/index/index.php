<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Index_Index extends osC_Template {

/* Private variables */

    var $_module = 'index',
        $_group = 'index',
        $_page_title,
        $_page_contents = 'index.php',
        $_page_image = 'table_background_default.gif';

/* Class constructor */

    function osC_Index_Index() {
      global $osC_Database, $osC_Services, $osC_Language, $osC_Breadcrumb, $cPath, $cPath_array, $current_category_id, $osC_Category;

      $this->_page_title = sprintf($osC_Language->get('index_heading'), STORE_NAME);

      if (isset($cPath) && (empty($cPath) === false)) {
        if ($osC_Services->isStarted('breadcrumb')) {
          $Qcategories = $osC_Database->query('select categories_id, categories_name from :table_categories_description where categories_id in (:categories_id) and language_id = :language_id');
          $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
          $Qcategories->bindTable(':categories_id', implode(',', $cPath_array));
          $Qcategories->bindInt(':language_id', $osC_Language->getID());
          $Qcategories->execute();

          $categories = array();
          while ($Qcategories->next()) {
            $categories[$Qcategories->value('categories_id')] = $Qcategories->valueProtected('categories_name');
          }

          $Qcategories->freeResult();

          for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) {
            $osC_Breadcrumb->add($categories[$cPath_array[$i]], osc_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
          }
        }

        $osC_Category = new osC_Category($current_category_id);

        $this->_page_title = $osC_Category->getTitle();

        if ( $osC_Category->hasImage() ) {
          $this->_page_image = 'categories/' . $osC_Category->getImage();
        }

        $Qproducts = $osC_Database->query('select products_id from :table_products_to_categories where categories_id = :categories_id limit 1');
        $Qproducts->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
        $Qproducts->bindInt(':categories_id', $current_category_id);
        $Qproducts->execute();

        if ($Qproducts->numberOfRows() > 0) {
          $this->_page_contents = 'product_listing.php';

          $this->_process();
        } else {
          $Qparent = $osC_Database->query('select categories_id from :table_categories where parent_id = :parent_id limit 1');
          $Qparent->bindTable(':table_categories', TABLE_CATEGORIES);
          $Qparent->bindInt(':parent_id', $current_category_id);
          $Qparent->execute();

          if ($Qparent->numberOfRows() > 0) {
            $this->_page_contents = 'category_listing.php';
          } else {
            $this->_page_contents = 'product_listing.php';

            $this->_process();
          }
        }
      }
    }

/* Private methods */

    function _process() {
      global $current_category_id, $osC_Products;

      include('includes/classes/products.php');
      $osC_Products = new osC_Products($current_category_id);

      if (isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0)) {
        $osC_Products->setManufacturer($_GET['filter']);
      }

      if (isset($_GET['sort']) && !empty($_GET['sort'])) {
        if (strpos($_GET['sort'], '|d') !== false) {
          $osC_Products->setSortBy(substr($_GET['sort'], 0, -2), '-');
        } else {
          $osC_Products->setSortBy($_GET['sort']);
        }
      }
    }
  }
?>
