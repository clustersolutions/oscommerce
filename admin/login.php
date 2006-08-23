<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/functions/password_funcs.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!empty($action)) {
    switch ($action) {
      case 'process':
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {
          $Qadmin = $osC_Database->query('select user_name, user_password from :table_administrators where user_name = :user_name');
          $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
          $Qadmin->bindValue(':user_name', $_POST['user_name']);
          $Qadmin->execute();

          if ($Qadmin->numberOfRows()) {
            if (tep_validate_password($_POST['user_password'], $Qadmin->value('user_password'))) {
              $_SESSION['admin'] = $Qadmin->value('user_name');

              tep_redirect(osc_href_link_admin(FILENAME_DEFAULT));
            }
          }
        }

        $osC_MessageStack->add('header', 'Error logging in.', 'error');

        break;

      case 'logoff':
        unset($_SESSION['admin']);

        tep_redirect(osc_href_link_admin(FILENAME_DEFAULT));

        break;
    }
  }

  $page_contents = 'login.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
