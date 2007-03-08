<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Login extends osC_Template {

/* Private variables */

    var $_module = 'login',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Login() {
      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'process':
            $this->_process();

            break;

          case 'logoff':
            $this->_logoff();

            break;
        }
      }
    }

/* Private methods */

    function _process() {
      global $osC_Database, $osC_MessageStack;

      if ( !empty($_POST['user_name']) && !empty($_POST['user_password']) ) {
        $Qadmin = $osC_Database->query('select id, user_name, user_password from :table_administrators where user_name = :user_name');
        $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qadmin->bindValue(':user_name', $_POST['user_name']);
        $Qadmin->execute();

        if ( $Qadmin->numberOfRows() ) {
          if ( osc_validate_password($_POST['user_password'], $Qadmin->value('user_password')) ) {
            $_SESSION['admin'] = array('id' => $Qadmin->valueInt('id'),
                                       'username' => $Qadmin->value('user_name'),
                                       'access' => osC_Access::getUserLevels($Qadmin->valueInt('id')));

            if ( isset($_SESSION['redirect_origin']) ) {
              $goto_module = $_SESSION['redirect_origin']['module'];
              $get_string = http_build_query($_SESSION['redirect_origin']['get']);

              unset($_SESSION['redirect_origin']);

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $goto_module . '&' . $get_string));
            } else {
              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT));
            }
          }
        }
      }

      $osC_MessageStack->add('header', 'Error logging in.', 'error');
    }

    function _logoff() {
      unset($_SESSION['admin']);

      osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT));
    }
  }
?>
