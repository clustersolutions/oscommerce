<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Cache extends osC_Template {

/* Private variables */

    var $_module = 'cache',
        $_page_title,
        $_page_contents = 'cache.php';

/* Class constructor */

    function osC_Content_Cache() {
      global $osC_MessageStack;

      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

// check if the cache directory exists
      if (is_dir(DIR_FS_WORK)) {
        if (!is_writeable(DIR_FS_WORK)) {
          $osC_MessageStack->add($this->_module, ERROR_CACHE_DIRECTORY_NOT_WRITEABLE, 'error');
        }
      } else {
        $osC_MessageStack->add($this->_module, ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST, 'error');
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'reset':
            $this->_reset();
            break;
        }
      }
    }

/* Private methods */

    function _reset() {
      if (isset($_GET[$this->_module]) && !empty($_GET[$this->_module])) {
        osC_Cache::clear($_GET[$this->_module]);
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }
  }
?>
