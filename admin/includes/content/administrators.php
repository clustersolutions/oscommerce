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
        $_page_title = HEADING_TITLE,
        $_page_contents = 'administrators.php';

/* Class constructor */

    function osC_Content_Administrators() {
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

    function sortAccessList($a, $b) {
      if ($a['group'] == $b['group']) {
        if ($a['text'] == $b['text']) {
          return 0;
        }

        return ($a['text'] < $b['text']) ? -1 : 1;
      }

      return ($a['group'] < $b['group']) ? -1 : 1;
    }

/* Private methods */

    function _save() {
      global $osC_Database, $osC_MessageStack;

      $error = false;

      $osC_Database->startTransaction();

      if (isset($_GET['aID']) && is_numeric($_GET['aID'])) {
        $Qadmin = $osC_Database->query('update :table_administrators set user_name = :user_name where id = :id');
        $Qadmin->bindInt(':id', $_GET['aID']);
      } else {
        $Qadmin = $osC_Database->query('insert into :table_administrators (user_name) values (:user_name)');
      }
      $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
      $Qadmin->bindValue(':user_name', $_POST['user_name']);
      $Qadmin->execute();

      if ( !$osC_Database->isError() ) {
        $id = (isset($_GET['aID']) && is_numeric($_GET['aID']) ? $_GET['aID'] : $osC_Database->nextID());

        if ( !empty($_POST['user_password']) ) {
          $Qadmin = $osC_Database->query('update :table_administrators set user_password = :user_password where id = :id');
          $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
          $Qadmin->bindValue(':user_password', osc_encrypt_string(trim($_POST['user_password'])));
          $Qadmin->bindInt(':id', $id);
          $Qadmin->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      } else {
        $error = true;
      }

      if ( $error === false ) {
        $modules_array = array();

        if ( isset($_POST['modules']) ) {
          if ( in_array( '*', $_POST['modules'] ) ) {
            $_POST['modules'] = array('*');
          }

          foreach ($_POST['modules'] as $module) {
            $modules_array[] = '\'' . $module . '\'';

            $Qcheck = $osC_Database->query('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
            $Qcheck->bindInt(':administrators_id', $id);
            $Qcheck->bindValue(':module', $module);
            $Qcheck->execute();

            if ( $Qcheck->numberOfRows() < 1 ) {
              $Qinsert = $osC_Database->query('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
              $Qinsert->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
              $Qinsert->bindInt(':administrators_id', $id);
              $Qinsert->bindValue(':module', $module);
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        $Qdel = $osC_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');

        if ( !empty($modules_array) ) {
          $Qdel->appendQuery('and module not in (:module)');
          $Qdel->bindRaw(':module', implode(',', $modules_array));
        }

        $Qdel->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
        $Qdel->bindInt(':administrators_id', $id);
        $Qdel->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        } else {
          if ($id == $_SESSION['admin']['id']) {
            $_SESSION['admin']['access'] = osC_Access::getUserLevels($id);
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
      } else {
        $osC_Database->rollbackTransaction();

        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . (isset($id) ? '&aID=' . $id : '')));
    }

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['aID']) && is_numeric($_GET['aID'])) {
        $osC_Database->startTransaction();

        $Qdel = $osC_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');
        $Qdel->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
        $Qdel->bindInt(':administrators_id', $_GET['aID']);
        $Qdel->execute();

        $Qdel = $osC_Database->query('delete from :table_administrators where id = :id');
        $Qdel->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qdel->bindInt(':id', $_GET['aID']);
        $Qdel->execute();

        $osC_Database->commitTransaction();

        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
    }
  }
?>
