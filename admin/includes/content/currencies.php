<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/currencies.php');

  class osC_Content_Currencies extends osC_Template {

/* Private variables */

    var $_module = 'currencies',
        $_page_title,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Currencies() {
      global $osC_Language, $osC_Currencies, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      $osC_Currencies = new osC_Currencies_Admin();

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
              $this->_page_contents = 'edit.php';
            } else {
              $this->_page_contents = 'new.php';
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('title' => $_POST['title'],
                            'code' => $_POST['code'],
                            'symbol_left' => $_POST['symbol_left'],
                            'symbol_right' => $_POST['symbol_right'],
                            'decimal_places' => $_POST['decimal_places'],
                            'value' => $_POST['value']);

              if ( osC_Currencies_Admin::save((isset($_GET['cID']) && is_numeric($_GET['cID']) ? $_GET['cID'] : null), $data, ((isset($_POST['default']) && ($_POST['default'] == 'on')) || (isset($_POST['is_default']) && ($_POST['is_default'] == 'true') && ($_POST['code'] != DEFAULT_CURRENCY)))) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Currencies_Admin::delete($_GET['cID']) ) {
                $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;

          case 'updateRates':
            $this->_page_contents = 'update_rates.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( isset($_POST['service']) && (($_POST['service'] == 'oanda') || ($_POST['service'] == 'xe')) ) {
                $results = osC_Currencies_Admin::updateRates($_POST['service']);

                foreach ($results[0] as $result) {
                  $osC_MessageStack->add_session($this->_module, sprintf($osC_Language->get('ms_error_invalid_currency'), $result['title'], $result['code']), 'error');
                }

                foreach ($results[1] as $result) {
                  $osC_MessageStack->add_session($this->_module, sprintf($osC_Language->get('ms_success_currency_updated'), $result['title'], $result['code']), 'success');
                }
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
                  if ( !osC_Currencies_Admin::delete($id) ) {
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
