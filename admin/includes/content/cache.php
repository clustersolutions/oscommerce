<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Cache extends osC_Template {

/* Private variables */

    var $_module = 'cache',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Cache() {
      global $osC_MessageStack;

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

// check if the cache directory exists
      if ( is_dir(DIR_FS_WORK) ) {
        if ( !is_writeable(DIR_FS_WORK) ) {
          $osC_MessageStack->add('header', ERROR_CACHE_DIRECTORY_NOT_WRITEABLE, 'error');
        }
      } else {
        $osC_MessageStack->add('header', ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST, 'error');
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'delete':
/*HPDL
            if ( osC_Cache::clear($_GET['block']) ) {
              $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
            }
*/

            osC_Cache::clear($_GET['block']);

            osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              foreach ($_POST['batch'] as $id) {
                osC_Cache::clear($id);
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;
        }
      }
    }
  }
?>
