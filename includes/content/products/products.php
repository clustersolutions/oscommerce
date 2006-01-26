<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Products_Products extends osC_Template {

/* Private variables */

    var $_module = 'products',
        $_group = 'products',
        $_page_title = HEADING_TITLE_INDEX,
        $_page_contents = 'info.php',
        $_page_image = 'table_background_list.gif';

/* Class constructor */

    function osC_Products_Products() {
      global $osC_Database, $osC_Services, $osC_Language, $breadcrumb, $osC_Product;

      if (empty($_GET) === false) {
        $id = false;

// PHP < 5.0.2; array_slice() does not preserve keys and will not work with numerical key values, so foreach() is used
        foreach ($_GET as $key => $value) {
          if (is_numeric($key) || ereg('[0-9]+[{[0-9]+}[0-9]+]*$', $key) || ereg('[a-zA-Z0-9 -_]*$', $key)) {
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

          if ($osC_Services->isStarted('breadcrumb')) {
            $breadcrumb->add($osC_Product->getTitle(), tep_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()));
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
