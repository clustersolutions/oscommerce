<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Languages extends osC_Template {

/* Private variables */

    var $_module = 'languages',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'languages.php';

/* Class constructor */

    function osC_Content_Languages() {
      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module])) {
        $this->_page_title = HEADING_TITLE_DEFINITIONS;
        $this->_page_contents = 'languages_definitions.php';
      }

      include('../includes/classes/currencies.php');

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'lDefine':
            $this->_page_contents = 'languages_definitions_listing.php';
            break;

          case 'import':
            $this->_import();
            break;

          case 'export':
            $this->_export();
            break;

          case 'save':
            $this->_save();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;

          case 'insert_definition':
            $this->_insertDefinition();
            break;

          case 'save_definition':
            $this->_saveDefinition();
            break;

          case 'deleteconfirm_definition':
            $this->_deleteDefinition();
            break;
        }
      }
    }

/* Private methods */

    function _import() {
      global $osC_Language, $osC_Currencies, $osC_MessageStack;

      $osC_Currencies = new osC_Currencies();

      if ($osC_Language->import($_POST['language_import'], $_POST['import_type'])) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }

    function _export() {
      global $osC_Database;

      $osC_Currencies = new osC_Currencies();

      $export_array = array();

      $Qlanguage = $osC_Database->query('select * from :table_languages where languages_id = :languages_id');
      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->bindInt(':languages_id', $_GET['lID']);
      $Qlanguage->execute();

      if ($_POST['include_data'] == 'on') {
        $export_array['language']['data'] = array('title-CDATA' => $Qlanguage->value('name'),
                                                  'code-CDATA' => $Qlanguage->value('code'),
                                                  'locale-CDATA' => $Qlanguage->value('locale'),
                                                  'character_set-CDATA' => $Qlanguage->value('charset'),
                                                  'text_direction-CDATA' => $Qlanguage->value('text_direction'),
                                                  'date_format_short-CDATA' => $Qlanguage->value('date_format_short'),
                                                  'date_format_long-CDATA' => $Qlanguage->value('date_format_long'),
                                                  'time_format-CDATA' => $Qlanguage->value('time_format'),
                                                  'default_currency-CDATA' => $osC_Currencies->getCode($Qlanguage->valueInt('currencies_id')),
                                                  'numerical_decimal_separator-CDATA' => $Qlanguage->value('numeric_separator_decimal'),
                                                  'numerical_thousands_separator-CDATA' => $Qlanguage->value('numeric_separator_thousands'));
      }

      $Qdefs = $osC_Database->query('select content_group, definition_key, definition_value from :table_languages_definitions where languages_id = :languages_id and content_group in (:content_group) order by content_group, definition_key');
      $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qdefs->bindInt(':languages_id', $_GET['lID']);
      $Qdefs->bindRaw(':content_group', '"' . implode('", "', $_POST['groups']) . '"');
      $Qdefs->execute();

      while ($Qdefs->next()) {
        $export_array['language']['definitions']['definition'][] = array('key' => $Qdefs->value('definition_key'),
                                                                         'value-CDATA' => $Qdefs->value('definition_value'),
                                                                         'group' => $Qdefs->value('content_group'));
      }

      $osC_XML = new osC_XML($export_array, $Qlanguage->value('charset'));
      $xml = $osC_XML->toXML();

      header('Content-disposition: attachment; filename=' . $Qlanguage->value('code') . '.xml');
      header('Content-Type: application/force-download');
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: ' . strlen($xml));
      header('Pragma: no-cache');
      header('Expires: 0');

      echo $xml;

      exit;
    }

    function _save() {
      global $osC_Language, $osC_MessageStack;

      $default = (isset($_POST['default']) && ($_POST['default'] == 'on')) ? true : false;

      if ($osC_Language->update($_GET['lID'], $_POST, $default)) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&lID=' . $_GET['lID']));
    }

    function _delete() {
      global $osC_Language, $osC_MessageStack;

      if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
        if ($osC_Language->remove($_GET['lID'])) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
    }

    function _insertDefinition() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($osC_Language->getAll() as $l) {
        $Qdefinition = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
        $Qdefinition->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdefinition->bindInt(':languages_id', $l['id']);
        $Qdefinition->bindValue(':content_group', (empty($_POST['group_new']) ? $_POST['group'] : $_POST['group_new']));
        $Qdefinition->bindValue(':definition_key', $_POST['key']);
        $Qdefinition->bindValue(':definition_value', $_POST['value'][$l['id']]);
        $Qdefinition->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET[$this->_module]) . '-' . $_POST['group']);
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');

        $osC_Database->rollbackTransaction();
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&group=' . $_POST['group']));
    }

    function _saveDefinition() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($_POST['def'] as $key => $value) {
        $Qupdate = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
        $Qupdate->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qupdate->bindValue(':definition_value', $value);
        $Qupdate->bindValue(':definition_key', $key);
        $Qupdate->bindInt(':languages_id', $_GET[$this->_module]);
        $Qupdate->bindValue(':content_group', $_GET['group']);
        $Qupdate->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET[$this->_module]) . '-' . $_GET['group']);
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');

        $osC_Database->rollbackTransaction();
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&group=' . $_GET['group']));
    }

    function _deleteDefinition() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      if (isset($_POST['defs'])) {
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
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

          $osC_Database->commitTransaction();

          osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET[$this->_module]) . '-' . $_GET['group']);
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');

          $osC_Database->rollbackTransaction();
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module]));
    }
  }
?>
