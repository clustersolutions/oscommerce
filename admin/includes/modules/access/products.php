<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Products extends osC_Access {
    var $_module = 'products',
        $_group = 'content',
        $_icon = 'products.png',
        $_title = ACCESS_PRODUCTS_TITLE,
        $_sort_order = 200;

    function osC_Access_Products() {
      $this->_subgroups = array(array('icon' => 'specials.png',
                                      'title' => ACCESS_PRODUCTS_NEW_TITLE,
                                      'identifier' => 'action=new'));
    }
  }
?>
