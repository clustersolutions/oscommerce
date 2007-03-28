<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/orders_status.php');

  class osC_Content_Orders_status extends osC_Template {

/* Private variables */

    var $_module = 'orders_status',
        $_page_title,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Orders_status() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            if ( isset($_GET['osID']) && is_numeric($_GET['osID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('name' => $_POST['name']);

              if ( osC_OrdersStatus_Admin::save((isset($_GET['osID']) ? $_GET['osID'] : null), $data, ( isset($_POST['default']) && ( $_POST['default'] == 'on' ) ? true : false )) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page' . $_GET['page']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_OrdersStatus_Admin::delete($_GET['osID']) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
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
                  if ( !osC_OrdersStatus_Admin::delete($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
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
