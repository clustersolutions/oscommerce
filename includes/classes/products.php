<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Products {
    var $_category,
        $_recursive = true,
        $_manufacturer,
        $_sql_query,
        $_sort_by,
        $_sort_by_direction;

/* Class constructor */

    function osC_Products($id = null) {
      if (is_numeric($id)) {
        $this->_category = $id;
      }
    }

/* Public methods */

    function hasCategory() {
      return isset($this->_category) && !empty($this->_category);
    }

    function isRecursive() {
      return $this->_recursive;
    }

    function hasManufacturer() {
      return isset($this->_manufacturer) && !empty($this->_manufacturer);
    }

    function setCategory($id, $recursive = true) {
      $this->_category = $id;

      if ($recursive === false) {
        $this->_recursive = false;
      }
    }

    function setManufacturer($id) {
      $this->_manufacturer = $id;
    }

    function setSortBy($field, $direction = '+') {
      switch ($field) {
        case 'model':
          $this->_sort_by = 'pd.products_model';
          break;
        case 'manufacturer':
          $this->_sort_by = 'm.manufacturers_name';
          break;
        case 'quantity':
          $this->_sort_by = 'p.products_quantity';
          break;
        case 'weight':
          $this->_sort_by = 'p.products_weight';
          break;
        case 'price':
          $this->_sort_by = 'final_price';
          break;
      }

      $this->_sort_by_direction = ($direction == '-') ? '-' : '+';
    }

    function setSortByDirection($direction) {
      $this->_sort_by_direction = ($direction == '-') ? '-' : '+';
    }

    function &execute() {
      global $osC_Database, $osC_Language, $osC_CategoryTree, $osC_Image;

      $Qlisting = $osC_Database->query('select distinct p.*, pd.*, m.*, if(s.status, s.specials_new_products_price, null) as specials_new_products_price, if(s.status, s.specials_new_products_price, p.products_price) as final_price, i.image from :table_products p left join :table_manufacturers m using(manufacturers_id) left join :table_specials s on (p.products_id = s.products_id) left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd, :table_categories c, :table_products_to_categories p2c where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id');
      $Qlisting->bindTable(':table_products', TABLE_PRODUCTS);
      $Qlisting->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
      $Qlisting->bindTable(':table_specials', TABLE_SPECIALS);
      $Qlisting->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qlisting->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qlisting->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qlisting->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
      $Qlisting->bindInt(':default_flag', 1);
      $Qlisting->bindInt(':language_id', $osC_Language->getID());

      if ($this->hasCategory()) {
        if ($this->isRecursive()) {
          $subcategories_array = array($this->_category);

          $Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and p2c.categories_id in (:categories_id)');
          $Qlisting->bindRaw(':categories_id', implode(',', $osC_CategoryTree->getChildren($this->_category, $subcategories_array)));
        } else {
          $Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = :language_id and p2c.categories_id = :categories_id');
          $Qlisting->bindInt(':language_id', $osC_Language->getID());
          $Qlisting->bindInt(':categories_id', $this->_category);
        }
      }

      if ($this->hasManufacturer()) {
        $Qlisting->appendQuery('and m.manufacturers_id = :manufacturers_id');
        $Qlisting->bindInt(':manufacturers_id', $this->_manufacturer);
      }

      $Qlisting->appendQuery('order by');

      if (isset($this->_sort_by)) {
        $Qlisting->appendQuery(':order_by :order_by_direction, pd.products_name');
        $Qlisting->bindRaw(':order_by', $this->_sort_by);
        $Qlisting->bindRaw(':order_by_direction', (($this->_sort_by_direction == '-') ? 'desc' : ''));
      } else {
        $Qlisting->appendQuery('pd.products_name :order_by_direction');
        $Qlisting->bindRaw(':order_by_direction', (($this->_sort_by_direction == '-') ? 'desc' : ''));
      }

      $Qlisting->setBatchLimit((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_SEARCH_RESULTS);
      $Qlisting->execute();

      return $Qlisting;
    }
  }
?>
