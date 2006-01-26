<?php
/*
  $Id: languages.php 387 2006-01-18 16:49:58Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!empty($action)) {
    switch ($action) {
      case 'insert':
        $error = false;

        $Qdefinition = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
        $Qdefinition->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdefinition->bindInt(':languages_id', $_GET['lID']);
        $Qdefinition->bindValue(':content_group', (empty($_POST['group_new']) ? $_POST['group'] : $_POST['group_new']));
        $Qdefinition->bindValue(':definition_key', $_POST['key']);
        $Qdefinition->bindValue(':definition_value', $_POST['value']);
        $Qdefinition->execute();

        if ($error === false) {
          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

          $osC_Database->commitTransaction();

          osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET['lID']) . '-' . $_POST['group']);
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');

          $osC_Database->rollbackTransaction();
        }

        tep_redirect(tep_href_link(FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&content=' . $_POST['content']));
        break;
      case 'save':
        $error = false;

        $osC_Database->startTransaction();

        foreach ($_POST['def'] as $key => $value) {
          $Qupdate = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
          $Qupdate->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qupdate->bindValue(':definition_value', $value);
          $Qupdate->bindValue(':definition_key', $key);
          $Qupdate->bindInt(':languages_id', $_GET['lID']);
          $Qupdate->bindValue(':content_group', $_GET['group']);
          $Qupdate->execute();

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if ($error === false) {
          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

          $osC_Database->commitTransaction();

          osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET['lID']) . '-' . $_GET['group']);
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');

          $osC_Database->rollbackTransaction();
        }

        tep_redirect(tep_href_link(FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&content=' . $_GET['content']));
        break;
      case 'deleteconfirm':
        $error = false;

        $osC_Database->startTransaction();

        foreach ($_POST['defs'] as $value) {
          $Qdel = $osC_Database->query('delete from :table_languages_definitions where id = :id');
          $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qdel->bindValue(':id', $value);
          $Qdel->execute();

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if ($error === false) {
          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

          $osC_Database->commitTransaction();

          osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET['lID']) . '-' . $_GET['group']);
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');

          $osC_Database->rollbackTransaction();
        }

        tep_redirect(tep_href_link(FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID']));
        break;
    }
  }

  if ($action == 'lDefine') {
    $page_contents = 'languages_definitions_listing.php';
  } else {
    $page_contents = 'languages_definitions.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
