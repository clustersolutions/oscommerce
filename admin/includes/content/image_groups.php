<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Image_groups extends osC_Template {

/* Private variables */

    var $_module = 'image_groups',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'image_groups.php';

/* Class constructor */

    function osC_Content_Image_groups() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
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

      if (isset($_GET['gID']) && is_numeric($_GET['gID'])) {
        $id = $_GET['gID'];
      } else {
        $Qgroup = $osC_Database->query('select max(id) as id from :table_products_images_groups');
        $Qgroup->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
        $Qgroup->execute();

        $id = ($Qgroup->valueInt('id') + 1);
      }

      $error = false;

      $osC_Database->startTransaction();

      foreach ($osC_Language->getAll() as $l) {
        if (isset($_GET['gID']) && is_numeric($_GET['gID'])) {
          $Qgroup = $osC_Database->query('update :table_products_images_groups set title = :title, code = :code, size_width = :size_width, size_height = :size_height, force_size = :force_size where id = :id and language_id = :language_id');
        } else {
          $Qgroup = $osC_Database->query('insert into :table_products_images_groups (id, language_id, title, code, size_width, size_height, force_size) values (:id, :language_id, :title, :code, :size_width, :size_height, :force_size)');
        }
        $Qgroup->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
        $Qgroup->bindInt(':id', $id);
        $Qgroup->bindValue(':title', $_POST['title'][$l['id']]);
        $Qgroup->bindValue(':code', $_POST['code']);
        $Qgroup->bindInt(':size_width', $_POST['width']);
        $Qgroup->bindInt(':size_height', $_POST['height']);
        $Qgroup->bindInt(':force_size', (isset($_POST['force_size']) && ($_POST['force_size'] == '1')) ? 1 : 0);
        $Qgroup->bindInt(':language_id', $l['id']);
        $Qgroup->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        if (isset($_POST['default']) && ($_POST['default'] == 'on') && (DEFAULT_IMAGE_GROUP_ID != $id)) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindInt(':configuration_value', $id);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_IMAGE_GROUP_ID');
          $Qupdate->execute();

          if ($osC_Database->isError() === false) {
            $clear_cache = ($Qupdate->affectedRows() ? true : false);
          } else {
            $error = true;
          }
        }
      }

      if ($error === false) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('images_groups');

        if (isset($_POST['default']) && ($_POST['default'] == 'on') && (DEFAULT_IMAGE_GROUP_ID != $id)) {
          if ($clear_cache === true) {
            osC_Cache::clear('configuration');
          }
        }

        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
      } else {
        $osC_Database->rollbackTransaction();

        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&gID=' . $id));
    }

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['gID']) && is_numeric($_GET['gID'])) {
        if (DEFAULT_IMAGE_GROUP_ID == $_GET['gID']) {
          $osC_MessageStack->add_session($this->_module, TEXT_INFO_DELETE_PROHIBITED, 'warning');

          osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&gID=' . $_GET['gID']));
        } else {
          $Qdel = $osC_Database->query('delete from :table_products_images_groups where id = :id');
          $Qdel->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
          $Qdel->bindInt(':id', $_GET['gID']);
          $Qdel->execute();

          if ($osC_Database->isError() === false) {
            if ($Qdel->affectedRows()) {
              $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }
          } else {
            $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
        }
      }
    }
  }
?>
