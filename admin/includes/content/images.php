<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/image.php');

  class osC_Content_Images extends osC_Template {

/* Private variables */

    var $_module = 'images',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Images() {
      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !isset($_GET['module']) ) {
        $_GET['module'] = '';
      }

      if ( !empty($_GET['module']) && !file_exists('includes/modules/image/' . $_GET['module'] . '.php') ) {
        $_GET['module'] = '';
      }

      if ( empty($_GET['module']) ) {
        $this->_page_contents = 'listing.php';
      }
    }
  }
?>