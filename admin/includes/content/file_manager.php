<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  define('OSC_ADMIN_FILE_MANAGER_ROOT_PATH', realpath('../'));

  class osC_Content_File_manager extends osC_Template {

/* Private variables */

    var $_module = 'file_manager',
        $_page_title,
        $_page_contents = 'file_manager.php';

/* Class constructor */

    function osC_Content_File_manager() {
      global $current_path, $goto_array;

      $this->_page_title = HEADING_TITLE;

      if (isset($_SESSION['fm_directory'])) {
        $current_path = $_SESSION['fm_directory'];
      } else {
        $current_path = OSC_ADMIN_FILE_MANAGER_ROOT_PATH;
        $_SESSION['fm_directory'] = $current_path;
      }

      if (isset($_GET['directory'])) {
        $current_path .= '/' . $_GET['directory'];
        $_SESSION['fm_directory'] = $current_path;
      } elseif (isset($_GET['goto'])) {
        $current_path = OSC_ADMIN_FILE_MANAGER_ROOT_PATH . '/' . urldecode($_GET['goto']);
        $_SESSION['fm_directory'] = $current_path;
      }

      $current_path = realpath($current_path);

      if ( (substr($current_path, 0, strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)) != OSC_ADMIN_FILE_MANAGER_ROOT_PATH) || (is_dir($current_path) === false) ) {
        $current_path = OSC_ADMIN_FILE_MANAGER_ROOT_PATH;
        $_SESSION['fm_directory'] = $current_path;
      }

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      $goto_array = array(array('id' => '', 'text' => '--TOP--'));

      if ($current_path != OSC_ADMIN_FILE_MANAGER_ROOT_PATH) {
        $path_array = explode('/', substr($current_path, strlen(OSC_ADMIN_FILE_MANAGER_ROOT_PATH)+1));

        foreach ($path_array as $value) {
          if (sizeof($goto_array) < 2) {
            $goto_array[] = array('id' => $value, 'text' => $value);
          } else {
            $parent = end($goto_array);
            $goto_array[] = array('id' => $parent['id'] . '/' . $value, 'text' => $parent['id'] . '/' . $value);
          }
        }
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'reset':
            $this->_reset();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;

          case 'new_directory':
            $this->_newDirectory();
            break;

          case 'save':
            $this->_save();
            break;

          case 'processuploads':
            $this->_processUploads();
            break;

          case 'download':
            $this->_download();
            break;

          case 'fmEdit':
            $this->_page_contents = 'file_manager_edit.php';
            break;
        }
      }
    }

/* Private methods */

    function _reset() {
      unset($_SESSION['fm_directory']);

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }

    function _delete() {
      global $osC_MessageStack, $current_path;

      if (isset($_GET['entry']) && !empty($_GET['entry'])) {
        $target = $current_path . '/' . basename($_GET['entry']);

        if (is_writeable($target)) {
          osc_remove($target);
        } else {
          if (is_file($target)) {
            $osC_MessageStack->add_session($this->_module, sprintf(ERROR_FILE_NOT_WRITEABLE, $target), 'error');
          } else {
            $osC_MessageStack->add_session($this->_module, sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $target), 'error');
          }
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, 'file_manager'));
    }

    function _newDirectory() {
      global $osC_MessageStack, $current_path;

      if (isset($_POST['directory_name']) && !empty($_POST['directory_name'])) {
        if (is_writeable($current_path)) {
          $new_directory = $current_path . '/' . basename($_POST['directory_name']);

          if (file_exists($new_directory) === false) {
            if (mkdir($new_directory, 0777)) {
              osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module, '&entry=' . urlencode(basename($_POST['directory_name']))));
            }
          } else {
            $osC_MessageStack->add($this->_module, sprintf(ERROR_DIRECTORY_EXISTS, $new_directory), 'error');
          }
        } else {
          $osC_MessageStack->add_session($this->_module, sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path), 'error');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }

    function _save() {
      global $current_path;

      if ( (isset($_GET['entry']) && !empty($_GET['entry'])) || (isset($_POST['filename']) && !empty($_POST['filename'])) ) {
        if (isset($_GET['entry']) && !empty($_GET['entry'])) {
          $filename = basename($_GET['entry']);
        } elseif (isset($_POST['filename']) && !empty($_POST['filename'])) {
          $filename = basename($_POST['filename']);
        }

        if ($fp = fopen($current_path . '/' . $filename, 'w+')) {
          fputs($fp, $_POST['contents']);
          fclose($fp);
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&entry=' . $filename));
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }

    function _processUploads() {
      global $osC_MessageStack, $current_path;

      if (is_writeable($current_path)) {
        for ($i=0; $i<10; $i++) {
          $file = new upload('file_' . $i, $current_path);

          if ($file->exists()) {
            $file->parse();
            $file->save();
          }
        }
      } else {
        $osC_MessageStack->add_session($this->_module, sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $current_path), 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }

    function _download() {
      global $current_path;

      if (isset($_GET['entry']) && !empty($_GET['entry'])) {
        $target = $current_path . '/' . basename($_GET['entry']);

        if (file_exists($target)) {
          header('Content-type: application/x-octet-stream');
          header('Content-disposition: attachment; filename=' . urldecode(basename($_GET['entry'])));

          readfile($target);
          exit;
        }
      }
    }
  }
?>
