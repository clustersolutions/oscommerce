<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Products_Products extends osC_Template {

/* Private variables */

    var $_module = 'products',
        $_group = 'products',
        $_page_title,
        $_page_contents = 'info.php',
        $_page_image = 'table_background_list.gif';

/* Class constructor */

    function osC_Products_Products() {
      global $osC_Database, $osC_Services, $osC_Session, $osC_Language, $osC_Breadcrumb, $osC_Product;

      if (empty($_GET) === false) {
        $id = false;

// PHP < 5.0.2; array_slice() does not preserve keys and will not work with numerical key values, so foreach() is used
        foreach ($_GET as $key => $value) {
          if ( (ereg('^[0-9]+(#?([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*)*$', $key) || ereg('^[a-zA-Z0-9 -_]*$', $key)) && ($key != $osC_Session->getName()) ) {
            $id = $key;
          }

          break;
        }

        if (($id !== false) && osC_Product::checkEntry($id)) {
          $osC_Product = new osC_Product($id);
          $osC_Product->incrementCounter();

          $this->addPageTags('keywords', $osC_Product->getTitle());
          $this->addPageTags('keywords', $osC_Product->getModel());

          if ($osC_Product->hasTags()) {
            $this->addPageTags('keywords', $osC_Product->getTags());
          }

          $this->addJavascriptFilename('templates/' . $this->getCode() . '/javascript/' . $this->_group . '/info.js');

          osC_Services_category_path::process($osC_Product->getCategoryID());

          if ($osC_Services->isStarted('breadcrumb')) {
            $osC_Breadcrumb->add($osC_Product->getTitle(), osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()));
          }

          $this->_page_title = $osC_Product->getTitle();
        } else {
          $this->_page_title = $osC_Language->get('product_not_found_heading');
          $this->_page_contents = 'info_not_found.php';
        }
      } else {
        $this->_page_title = $osC_Language->get('product_not_found_heading');
        $this->_page_contents = 'info_not_found.php';
      }
    }
  }
?>
