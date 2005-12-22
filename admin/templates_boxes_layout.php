<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'templates';

  $set = (isset($_GET['set']) ? $_GET['set'] : '');

  switch ($set) {
    case 'content':
//      define('HEADING_TITLE', HEADING_TITLE_MODULES_CONTENT);
//      define('TABLE_HEADING_MODULES_TITLE', TABLE_HEADING_MODULES_CONTENT);
      break;

    case 'boxes':
    default:
      $set = 'boxes';
//      define('HEADING_TITLE', HEADING_TITLE_MODULES_BOXES);
//      define('TABLE_HEADING_MODULES_TITLE', TABLE_HEADING_MODULES_BOXES);
      break;
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $filter_template = (isset($_GET['filter']) ? $_GET['filter'] : DEFAULT_TEMPLATE);

  if (!empty($action)) {
    switch ($action) {
      case 'save':
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

        if ($osC_Database->isError() === false) {
          if ($Qlayout->affectedRows()) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

            osC_Cache::clear('templates_' . $set . '_layout');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
          tep_redirect(tep_href_link(FILENAME_TEMPLATES_BOXES_LAYOUT, 'set=' . $_GET['set'] . '&filter=' . $filter_template . '&lID=' . $_GET['lID']));
        } else {
          tep_redirect(tep_href_link(FILENAME_TEMPLATES_BOXES_LAYOUT, 'set=' . $_GET['set'] . '&filter=' . $filter_template));
        }

        break;
      case 'remove':
        if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
          $Qdel = $osC_Database->query('delete from :table_templates_boxes_to_pages where id = :id');
          $Qdel->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
          $Qdel->bindInt(':id', $_GET['lID']);
          $Qdel->execute();

          if ($osC_Database->isError() === false) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

            osC_Cache::clear('templates_' . $set . '_layout');
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }
        }

        tep_redirect(tep_href_link(FILENAME_TEMPLATES_BOXES_LAYOUT, 'set=' . $_GET['set'] . '&filter=' . $filter_template));
        break;
    }
  }

  $page_contents = 'templates_boxes_layout.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
