<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('../includes/classes/currencies.php');

  class osC_Content_Languages extends osC_Template {

/* Private variables */

    var $_module = 'languages',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Languages() {
      global $osC_MessageStack, $osC_Currencies;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module])) {
        $this->_page_title .= ': ' . osC_Language_Admin::getData($_GET[$this->_module], 'name');
        $this->_page_contents = 'groups.php';
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'definitions':
            $this->_page_title .= ': ' . $_GET['group'];
            $this->_page_contents = 'definitions_edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if (osC_Language_Admin::saveDefinitions($_GET[$this->_module], $_GET['group'], $_POST['def'])) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page'] . '&group=' . $_GET['group']));
            }

            break;

          case 'insertDefinition':
            $this->_page_contents = 'definitions_new.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('key' => $_POST['key'],
                            'value' => $_POST['value']);

              if ( osC_Language_Admin::insertDefinition((!empty($_POST['group_new']) ? $_POST['group_new'] : $_POST['group']), $data) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
            }

            break;

          case 'deleteDefinitions':
            $this->_page_contents = 'definitions_delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Language_Admin::deleteDefinitions($_GET[$this->_module], $_GET['group'], $_POST['defs']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $_GET[$this->_module] . '&page=' . $_GET['page']));
            }

            break;

          case 'import':
            $this->_page_contents = 'import.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $osC_Currencies = new osC_Currencies();

              if (osC_Language_Admin::import($_POST['language_import'], $_POST['import_type'])) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module));
            }

            break;

          case 'export':
            $this->_page_contents = 'export.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $osC_Currencies = new osC_Currencies();

              osC_Language_Admin::export($_GET['lID'], $_POST['groups'], (isset($_POST['include_data']) && ($_POST['include_data'] == 'on')));
            }

            break;

          case 'save':
            $this->_page_contents = 'edit.php';

            $osC_Currencies = new osC_Currencies();

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('name' => $_POST['name'],
                            'code' => $_POST['code'],
                            'locale' => $_POST['locale'],
                            'charset' => $_POST['charset'],
                            'date_format_short' => $_POST['date_format_short'],
                            'date_format_long' => $_POST['date_format_long'],
                            'time_format' => $_POST['time_format'],
                            'text_direction' => $_POST['text_direction'],
                            'currencies_id' => $_POST['currencies_id'],
                            'numeric_separator_decimal' => $_POST['numeric_separator_decimal'],
                            'numeric_separator_thousands' => $_POST['numeric_separator_thousands'],
                            'sort_order' => $_POST['sort_order']);

              if ( osC_Language_Admin::update($_GET['lID'], $data, (isset($_POST['default']) && ($_POST['default'] == 'on'))) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Language_Admin::remove($_GET['lID']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Language_Admin::remove($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
              }
            }

            break;
        }
      }
    }
  }
?>
