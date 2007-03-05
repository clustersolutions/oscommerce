<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/file_manager.php');

  define('OSC_ADMIN_FILE_MANAGER_ROOT_PATH', realpath('../'));

  class osC_Content_File_manager extends osC_Template {

/* Private variables */

    var $_module = 'file_manager',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_File_manager() {
      global $osC_MessageStack;

      if ( !isset($_SESSION['fm_directory']) ) {
        $_SESSION['fm_directory'] = OSC_ADMIN_FILE_MANAGER_ROOT_PATH;
      }

      if ( isset($_GET['directory']) ) {
        $_SESSION['fm_directory'] .= '/' . $_GET['directory'];
      } elseif ( isset($_GET['goto']) ) {
        $_SESSION['fm_directory'] = OSC_ADMIN_FILE_MANAGER_ROOT_PATH . '/' . urldecode($_GET['goto']);
      }

      $_SESSION['fm_directory'] = realpath($_SESSION['fm_directory']);

      if ( ( substr($_SESSION['fm_directory'], 0, strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)) != OSC_ADMIN_FILE_MANAGER_ROOT_PATH ) || !is_dir($_SESSION['fm_directory']) ) {
        $_SESSION['fm_directory'] = OSC_ADMIN_FILE_MANAGER_ROOT_PATH;
      }

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'saveDirectory':
            $this->_page_contents = 'directory_new.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_FileManager_Admin::createDirectory($_POST['directory_name'], $_SESSION['fm_directory']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'save':
            if ( isset($_GET['entry']) && !empty($_GET['entry']) ) {
              $this->_page_contents = 'file_edit.php';
            } else {
              $this->_page_contents = 'file_new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_FileManager_Admin::saveFile($_POST['filename'], $_POST['contents'], $_SESSION['fm_directory']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'upload':
            $this->_page_contents = 'upload.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $error = false;

              for ( $i = 0; $i < 10; $i++ ) {
                if ( is_uploaded_file($_FILES['file_' . $i]['tmp_name']) ) {
                  if ( !osC_FileManager_Admin::storeFileUpload('file_' . $i, $_SESSION['fm_directory']) ) {
                    $error = true;
                    break;
                  }
                }
              }

              if ( $error === false ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_FileManager_Admin::delete($_GET['entry'], $_SESSION['fm_directory']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;


          case 'download':
            $filename = basename($_GET['entry']);

            if ( file_exists($_SESSION['fm_directory'] . '/' . $filename) ) {
              header('Content-type: application/x-octet-stream');
              header('Content-disposition: attachment; filename=' . urldecode($filename));

              readfile($_SESSION['fm_directory'] . '/' . $filename);

              exit;
            }

            $osC_MessageStack->add($this->_module, ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE, 'error');

            break;
        }
      }
    }
  }
?>
