<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Statistics extends osC_Template {

/* Private variables */

    var $_module = 'statistics',
        $_page_title,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Statistics() {
      global $osC_Language;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['module']) ) {
        $_GET['module'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['module']) && !file_exists('includes/modules/statistics/' . $_GET['module'] . '.php') ) {
        $_GET['module'] = '';
      }

      if ( empty($_GET['module']) ) {
        $this->_page_contents = 'listing.php';
      }
    }
  }
?>
