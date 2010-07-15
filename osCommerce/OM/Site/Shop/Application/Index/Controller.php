<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Index;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Products;

  class Controller extends \osCommerce\OM\Site\Shop\ApplicationAbstract {

    protected function initialize() {}

    protected function process() {
      $OSCOM_Category = Registry::get('Category');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $this->_page_title = sprintf(OSCOM::getDef('index_heading'), STORE_NAME);

      if ( $OSCOM_Category->getID() > 0 ) {
        if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
          $Qcategories = $OSCOM_Database->query('select categories_id, categories_name from :table_categories_description where categories_id in (:categories_id) and language_id = :language_id');
          $Qcategories->bindRaw(':categories_id', implode(',', $OSCOM_Category->getPathArray()));
          $Qcategories->bindInt(':language_id', $OSCOM_Language->getID());
          $Qcategories->execute();

          $categories = array();
          while ( $Qcategories->next() ) {
            $categories[$Qcategories->value('categories_id')] = $Qcategories->valueProtected('categories_name');
          }

          $Qcategories->freeResult();

          for ( $i=0, $n=sizeof($OSCOM_Category->getPathArray()); $i<$n; $i++ ) {
            $OSCOM_Breadcrumb->add($categories[$OSCOM_Category->getPathArray($i)], OSCOM::getLink(null, 'Index', 'cPath=' . implode('_', array_slice($OSCOM_Category->getPathArray(), 0, ($i+1)))));
          }
        }

        $this->_page_title = $OSCOM_Category->getTitle();

        if ( $OSCOM_Category->hasImage() ) {
          $this->_page_image = 'categories/' . $OSCOM_Category->getImage();
        }

        $Qproducts = $OSCOM_Database->query('select products_id from :table_products_to_categories where categories_id = :categories_id limit 1');
        $Qproducts->bindInt(':categories_id', $OSCOM_Category->getID());
        $Qproducts->execute();

        if ( $Qproducts->numberOfRows() > 0 ) {
          $this->_page_contents = 'product_listing.php';

          $this->_process();
        } else {
          $Qparent = $OSCOM_Database->query('select categories_id from :table_categories where parent_id = :parent_id limit 1');
          $Qparent->bindInt(':parent_id', $OSCOM_Category->getID());
          $Qparent->execute();

          if ( $Qparent->numberOfRows() > 0 ) {
            $this->_page_contents = 'category_listing.php';
          } else {
            $this->_page_contents = 'product_listing.php';

            $this->_process();
          }
        }
      }
    }

    protected function _process() {
      $OSCOM_Category = Registry::get('Category');

      Registry::set('Products', new Products($OSCOM_Category->getID()));
      $OSCOM_Products = Registry::get('Products');

      if ( isset($_GET['filter']) && is_numeric($_GET['filter']) && ($_GET['filter'] > 0) ) {
        $OSCOM_Products->setManufacturer($_GET['filter']);
      }

      if ( isset($_GET['sort']) && !empty($_GET['sort']) ) {
        if ( strpos($_GET['sort'], '|d') !== false ) {
          $OSCOM_Products->setSortBy(substr($_GET['sort'], 0, -2), '-');
        } else {
          $OSCOM_Products->setSortBy($_GET['sort']);
        }
      }
    }
  }
?>
