<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        $newsletter_error = false;

        if (!isset($_POST['module']) || (isset($_POST['module']) && empty($_POST['module']))) {
          $newsletter_error = true;

          $osC_MessageStack->add('header', ERROR_NEWSLETTER_MODULE, 'error');
        }

        if (!isset($_POST['title']) || (isset($_POST['title']) && empty($_POST['title']))) {
          $newsletter_error = true;

          $osC_MessageStack->add('header', ERROR_NEWSLETTER_TITLE, 'error');
        }

        if ($newsletter_error === false) {
          if (isset($_GET['nmID']) && is_numeric($_GET['nmID'])) {
            $Qemail = $osC_Database->query('update :table_newsletters set title = :title, content = :content, module = :module where newsletters_id = :newsletters_id');
            $Qemail->bindInt(':newsletters_id', $_GET['nmID']);
          } else {
            $Qemail = $osC_Database->query('insert into :table_newsletters (title, content, module, date_added, status) values (:title, :content, :module, now(), 0)');
          }
          $Qemail->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
          $Qemail->bindValue(':title', $_POST['title']);
          $Qemail->bindValue(':content', $_POST['content']);
          $Qemail->bindValue(':module', $_POST['module']);
          $Qemail->execute();

          if (isset($_GET['nmID']) && is_numeric($_GET['nmID'])) {
            $nmID = $_GET['nmID'];
          } else {
            $nmID = $osC_Database->nextID();
          }

          osc_redirect(osc_href_link_admin(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $nmID));
        } else {
          $action = 'nmEdit';
        }
        break;
      case 'deleteconfirm':
        if (isset($_GET['nmID']) && is_numeric($_GET['nmID'])) {
          $Qdelete = $osC_Database->query('delete from :table_newsletters where newsletters_id = :newsletters_id');
          $Qdelete->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
          $Qdelete->bindInt(':newsletters_id', $_GET['nmID']);
          $Qdelete->execute();

          $Qdelete = $osC_Database->query('delete from :table_newsletters_log where newsletters_id = :newsletters_id');
          $Qdelete->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
          $Qdelete->bindInt(':newsletters_id', $_GET['nmID']);
          $Qdelete->execute();
        }

        osc_redirect(osc_href_link_admin(FILENAME_NEWSLETTERS, 'page=' . $_GET['page']));
        break;
    }
  }

  switch ($action) {
    case 'nmPreview': $page_contents = 'newsletters_preview.php'; break;
    case 'nmEdit': $page_contents = 'newsletters_edit.php'; break;
    case 'nmSend':
    case 'nmConfirm':
    case 'nmSendConfirm': $page_contents = 'newsletters_send.php'; break;
    case 'nmLog': $page_contents = 'newsletters_log.php'; break;
    default: $page_contents = 'newsletters.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
