<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Administrators extends osC_Template {

/* Private variables */

    var $_module = 'administrators',
        $_page_title,
        $_page_contents = 'administrators.php';

/* Class constructor */

    function osC_Content_Administrators() {
      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_save();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database, $osC_MessageStack;

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
        $Qadmin->bindValue(':user_password', osc_encrypt_string(trim($_POST['user_password'])));
        $Qadmin->bindInt(':id', $id);
        $Qadmin->execute();
      }

      if ($osC_Database->isError() === false) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&aID=' . $id));
    }

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['aID']) && is_numeric($_GET['aID'])) {
        $Qdel = $osC_Database->query('delete from :table_administrators where id = :id');
        $Qdel->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qdel->bindInt(':id', $_GET['aID']);
        $Qdel->execute();

        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
    }
  }
?>
