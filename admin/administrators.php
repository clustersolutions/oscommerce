<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/functions/password_funcs.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        $id = 0;

        if (isset($_GET['aID']) && is_numeric($_GET['aID'])) {
          $Qadmin = $osC_Database->query('update :table_administrators set user_name = :user_name where id = :id');
          $Qadmin->bindInt(':id', $_GET['aID']);
        } else {
          $Qadmin = $osC_Database->query('insert into :table_administrators (user_name) values (:user_name)');
        }
        $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qadmin->bindValue(':user_name', $_POST['user_name']);
        $Qadmin->execute();

        if (($osC_Database->isError() === false) && !empty($_POST['user_password'])) {
          $id = ((isset($_GET['aID']) && is_numeric($_GET['aID'])) ? $_GET['aID'] : $osC_Database->nextID());

          $Qadmin = $osC_Database->query('update :table_administrators set user_password = :user_password where id = :id');
          $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
          $Qadmin->bindValue(':user_password', tep_encrypt_password(trim($_POST['user_password'])));
          $Qadmin->bindInt(':id', $id);
          $Qadmin->execute();
        }

        if ($osC_Database->isError() === false) {
          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(tep_href_link(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page'] . '&aID=' . $id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['aID']) && is_numeric($_GET['aID'])) {
          $Qdel = $osC_Database->query('delete from :table_administrators where id = :id');
          $Qdel->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
          $Qdel->bindInt(':id', $_GET['aID']);
          $Qdel->execute();

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page']));
        break;
    }
  }

  $page_contents = 'administrators.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
