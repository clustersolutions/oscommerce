<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/categories/classes/categories.php');
  require('includes/applications/products/classes/products.php');
  require('includes/classes/category_tree.php');

  class osC_Application_Categories extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'categories',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack, $current_category_id, $osC_CategoryTree;

      $this->_page_title = $osC_Language->get('heading_title');

      $current_category_id = 0;

      if ( is_numeric($_GET[$this->_module]) ) {
        $current_category_id = $_GET[$this->_module];
      }

      $osC_CategoryTree = new osC_CategoryTree_Admin();

// check if the categories image directory exists
      if ( is_dir(realpath('../images/categories')) ) {
        if ( !is_writeable(realpath('../images/categories')) ) {
          $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_image_directory_not_writable'), realpath('../images/categories')), 'error');
        }
      } else {
        $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_image_directory_non_existant'), realpath('../images/categories')), 'error');
      }
    }
  }
?>
