<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Statistics extends osC_Template {

/* Private variables */

    var $_module = 'statistics',
        $_page_title,
        $_page_contents = 'statistics.php';

/* Class constructor */

    function osC_Content_Statistics() {
      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['module'])) {
        $_GET['module'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET['module']) && !file_exists('includes/modules/statistics/' . $_GET['module'] . '.php')) {
        $_GET['module'] = '';
      }

      if (empty($_GET['module'])) {
        $this->_page_contents = 'statistics_listing.php';
      }
    }
  }
?>
