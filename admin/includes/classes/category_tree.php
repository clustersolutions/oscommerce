<?php
/*
  $Id: category_tree.php 733 2006-08-20 15:32:32Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('../includes/classes/category_tree.php');

  class osC_CategoryTree_Admin extends osC_CategoryTree {
    protected $_show_total_products = true;

    function __constructor() {
      global $osC_Database, $osC_Language;

      $Qcategories = $osC_Database->query('select c.categories_id, c.parent_id, c.categories_image, cd.categories_name from :table_categories c, :table_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = :language_id order by c.parent_id, c.sort_order, cd.categories_name');
      $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
      $Qcategories->bindInt(':language_id', $osC_Language->getID());
      $Qcategories->execute();

      $this->_data = array();

      while ($Qcategories->next()) {
        $this->_data[$Qcategories->valueInt('parent_id')][$Qcategories->valueInt('categories_id')] = array('name' => $Qcategories->value('categories_name'), 'image' => $Qcategories->value('categories_image'), 'count' => 0);
      }

      $Qcategories->freeResult();

      if ($this->_show_total_products === true) {
        $this->_calculateProductTotals(false);
      }
    }

    function getPath($category_id, $level = 0, $separator = ' ') {
      $path = '';

      foreach ($this->_data as $parent => $categories) {
        foreach ($categories as $id => $info) {
          if ($id == $category_id) {
            if ($level < 1) {
              $path = $info['name'];
            } else {
              $path = $info['name'] . $separator . $path;
            }

            if ($parent != $this->root_category_id) {
              $path = $this->getPath($parent, $level+1, $separator) . $path;
            }
          }
        }
      }

      return $path;
    }

    function getPathArray($category_id) {
      static $path = array();

      foreach ( $this->_data as $parent => $categories ) {
        foreach ( $categories as $id => $info ) {
          if ( $id == $category_id ) {
            $path[] = array('id' => $id,
                            'name' => $info['name']);

            if ( $parent != $this->root_category_id ) {
              $this->getPathArray($parent);
            }
          }
        }
      }

      return array_reverse($path);
    }
  }
?>
