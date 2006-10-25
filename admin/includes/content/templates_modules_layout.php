<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Templates_modules_layout extends osC_Template {

/* Private variables */

    var $_module = 'templates_modules_layout',
        $_page_title,
        $_page_contents = 'templates_modules_layout.php';

/* Class constructor */

    function osC_Content_Templates_modules_layout() {
      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['set'])) {
        $_GET['set'] = 'boxes';
      }

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['filter'])) {
        $_GET['filter'] = DEFAULT_TEMPLATE;
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_save();
            break;

          case 'remove':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database, $osC_MessageStack;

      $link = explode('/', $_POST['content_page'], 2);

      if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
        $Qlayout = $osC_Database->query('update :table_templates_boxes_to_pages set content_page = :content_page, boxes_group = :boxes_group, sort_order = :sort_order, page_specific = :page_specific where id = :id');
        $Qlayout->bindInt(':id', $_GET['lID']);
      } else {
        $Qlayout = $osC_Database->query('insert into :table_templates_boxes_to_pages (templates_boxes_id, templates_id, content_page, boxes_group, sort_order, page_specific) values (:templates_boxes_id, :templates_id, :content_page, :boxes_group, :sort_order, :page_specific)');
        $Qlayout->bindInt(':templates_boxes_id', $_POST['box']);
        $Qlayout->bindInt(':templates_id', $link[0]);
      }
      $Qlayout->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
      $Qlayout->bindValue(':content_page', $link[1]);
      $Qlayout->bindValue(':boxes_group', (isset($_POST['group_new']) && !empty($_POST['group_new'])) ? $_POST['group_new'] : $_POST['group']);
      $Qlayout->bindInt(':sort_order', $_POST['sort_order']);
      $Qlayout->bindInt(':page_specific', (isset($_POST['page_specific']) && ($_POST['page_specific'] == '1')) ? '1' : '0');
      $Qlayout->execute();

      if (!$osC_Database->isError()) {
        if ($Qlayout->affectedRows()) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

          osC_Cache::clear('templates_' . $_GET['set'] . '_layout');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&lID=' . $_GET['lID']));
      } else {
        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
      }
    }

    function _remove() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
        $Qdel = $osC_Database->query('delete from :table_templates_boxes_to_pages where id = :id');
        $Qdel->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
        $Qdel->bindInt(':id', $_GET['lID']);
        $Qdel->execute();

        if (!$osC_Database->isError()) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

          osC_Cache::clear('templates_' . $_GET['set'] . '_layout');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']));
    }
  }
?>
