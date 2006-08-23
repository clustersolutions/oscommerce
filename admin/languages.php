<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/classes/directory_listing.php');
  require('../includes/classes/currencies.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!empty($action)) {
    switch ($action) {
      case 'import':
        if ($osC_Language->import($_POST['language_import'], $_POST['import_type'])) {
          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(osc_href_link_admin(FILENAME_LANGUAGES));
        break;
        case 'export':
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
          break;
        case 'save':
          $default = (isset($_POST['default']) && ($_POST['default'] == 'on')) ? true : false;

          if ($osC_Language->update($_GET['lID'], $_POST, $default)) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }

          tep_redirect(osc_href_link_admin(FILENAME_LANGUAGES, 'lID=' . $_GET['lID']));
          break;
      case 'deleteconfirm':
        if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
          if ($osC_Language->remove($_GET['lID'])) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }
        }

        tep_redirect(osc_href_link_admin(FILENAME_LANGUAGES));
        break;
    }
  }

  $page_contents = 'languages.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
