<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/manufacturers.php');
  require('includes/classes/products.php');
  require('includes/classes/image.php');

  class osC_Content_Manufacturers extends osC_Template {

/* Private variables */

    var $_module = 'manufacturers',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Manufacturers() {
      global $osC_MessageStack, $osC_Image;

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

// check if the manufacturers image directory exists
      if ( is_dir(realpath('../images/manufacturers')) ) {
        if ( !is_writeable(realpath('../images/manufacturers')) ) {
          $osC_MessageStack->add('header', ERROR_MANUFACTURERS_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
        }
      } else {
        $osC_MessageStack->add('header', ERROR_MANUFACTURERS_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
      }

      $osC_Image = new osC_Image_Admin();

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            if ( isset($_GET['mID']) && is_numeric($_GET['mID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('name' => $_POST['manufacturers_name'],
                            'url' => $_POST['manufacturers_url']);

              if ( osC_Manufacturers_Admin::save((isset($_GET['mID']) && is_numeric($_GET['mID']) ? $_GET['mID'] : null), $data) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Manufacturers_Admin::delete($_GET['mID'], (isset($_POST['delete_image']) && ($_POST['delete_image'] == 'on') ? true : false), (isset($_POST['delete_products']) && ($_POST['delete_products'] == 'on') ? true : false)) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Manufacturers_Admin::delete($id, (isset($_POST['delete_image']) && ($_POST['delete_image'] == 'on') ? true : false), (isset($_POST['delete_products']) && ($_POST['delete_products'] == 'on') ? true : false)) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
              }
            }

            break;
        }
      }
    }
  }
?>
