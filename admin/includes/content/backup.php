<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/backup.php');

  class osC_Content_Backup extends osC_Template {

/* Private variables */

    var $_module = 'backup',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Backup() {
      global $osC_MessageStack;

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

// check if the backup directory exists
      if ( !osc_empty(DIR_FS_BACKUP) && is_dir(DIR_FS_BACKUP) ) {
        if ( !is_writeable(DIR_FS_BACKUP) ) {
          $osC_MessageStack->add('header', ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE, 'error');
        }
      } else {
        $osC_MessageStack->add('header', ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST, 'error');
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'backup':
            $this->_page_contents = 'backup.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Backup_Admin::backup($_POST['compression'], (isset($_POST['download_only']) && ($_POST['download_only'] == 'yes') ? true : false)) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'restoreLocal':
            $this->_page_contents = 'restore_local.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Backup_Admin::restore() ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'restore':
            $this->_page_contents = 'restore.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Backup_Admin::restore($_GET['file']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'download':
            $filename = basename($_GET['file']);

            $extension = substr($filename, -3);

            if ( ( $extension == 'zip' ) || ( $extension == '.gz' ) || ( $extension == 'sql' ) ) {
              if ( file_exists(DIR_FS_BACKUP . $filename) ) {
                if ( $fp = fopen(DIR_FS_BACKUP . $filename, 'rb') ) {
                  $buffer = fread($fp, filesize(DIR_FS_BACKUP . $filename));
                  fclose($fp);

                  header('Content-type: application/x-octet-stream');
                  header('Content-disposition: attachment; filename=' . $filename);

                  echo $buffer;

                  exit;
                }
              }
            }

            $osC_MessageStack->add($this->_module, ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE, 'error');

            break;

          case 'forget':
            if ( osC_Backup_Admin::forget() ) {
              $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }

            osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Backup_Admin::delete($_GET['file']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Backup_Admin::delete($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
                }

                osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
              }
            }

            break;
        }
      }
    }
  }
?>
