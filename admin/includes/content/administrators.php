<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/administrators.php');

  class osC_Content_Administrators extends osC_Template {

/* Private variables */

    var $_module = 'administrators',
        $_page_title,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Administrators() {
      global $osC_Language,$osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            if ( isset($_GET['aID']) && is_numeric($_GET['aID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('username' => $_POST['user_name'],
                            'password' => $_POST['user_password']);

              switch ( osC_Administrators_Admin::save((isset($_GET['aID']) && is_numeric($_GET['aID']) ? $_GET['aID'] : null), $data, (isset($_POST['modules']) ? $_POST['modules'] : null)) ) {
                case 1:
                  if ( isset($_GET['aID']) && is_numeric($_GET['aID']) && ($_GET['aID'] == $_SESSION['admin']['id']) ) {
                    $_SESSION['admin']['access'] = osC_Access::getUserLevels($_GET['aID']);
                  }

                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

                  osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));

                  break;

                case -1:
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');

                  osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));

                  break;

                case -2:
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_username_already_exists'), 'error');

                  break;
              }
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Administrators_Admin::delete($_GET['aID']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'batchSave':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_edit.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Administrators_Admin::setAccessLevels($id, $_POST['modules'], $_POST['mode']) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

                  if ( in_array($_SESSION['admin']['id'], $_POST['batch']) ) {
                    $_SESSION['admin']['access'] = osC_Access::getUserLevels($_SESSION['admin']['id']);
                  }
                } else {
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
              }
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Administrators_Admin::delete($id) ) {
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
