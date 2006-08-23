<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('../includes/classes/currencies.php');
  $osC_Currencies = new osC_Currencies();

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $Qcurrency = $osC_Database->query('update :table_currencies set title = :title, code = :code, symbol_left = :symbol_left, symbol_right = :symbol_right, decimal_places = :decimal_places, value = :value where currencies_id = :currencies_id');
          $Qcurrency->bindInt(':currencies_id', $_GET['cID']);
        } else {
          $Qcurrency = $osC_Database->query('insert into :table_currencies (title, code, symbol_left, symbol_right, decimal_places, value) values (:title, :code, :symbol_left, :symbol_right, :decimal_places, :value)');
        }
        $Qcurrency->bindTable(':table_currencies', TABLE_CURRENCIES);
        $Qcurrency->bindValue(':title', $_POST['title']);
        $Qcurrency->bindValue(':code', $_POST['code']);
        $Qcurrency->bindValue(':symbol_left', $_POST['symbol_left']);
        $Qcurrency->bindValue(':symbol_right', $_POST['symbol_right']);
        $Qcurrency->bindInt(':decimal_places', $_POST['decimal_places']);
        $Qcurrency->bindValue(':value', $_POST['value']);
        $Qcurrency->execute();

        if ($osC_Database->isError() === false) {
          if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
            $currency_id = $_GET['cID'];
          } else {
            $currency_id = $osC_Database->nextID();
          }

          if ( (isset($_POST['default']) && ($_POST['default'] == 'on')) || (isset($_POST['is_default']) && ($_POST['is_default'] == 'true') && ($_POST['code'] != DEFAULT_CURRENCY)) ) {
            $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
            $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
            $Qupdate->bindValue(':configuration_value', $_POST['code']);
            $Qupdate->bindValue(':configuration_key', 'DEFAULT_CURRENCY');
            $Qupdate->execute();

            if ($Qupdate->affectedRows()) {
              osC_Cache::clear('configuration');
            }
          }

          osC_Cache::clear('currencies');

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $Qcheck = $osC_Database->query('select code from :table_currencies where currencies_id = :currencies_id');
          $Qcheck->bindTable(':table_currencies', TABLE_CURRENCIES);
          $Qcheck->bindInt(':currencies_id', $_GET['cID']);
          $Qcheck->execute();

          if ($Qcheck->value('code') != DEFAULT_CURRENCY) {
            $Qdelete = $osC_Database->query('delete from :table_currencies where currencies_id = :currencies_id');
            $Qdelete->bindTable(':table_currencies', TABLE_CURRENCIES);
            $Qdelete->bindInt(':currencies_id', $_GET['cID']);
            $Qdelete->execute();

            if ($osC_Database->isError() === false) {
              osC_Cache::clear('currencies');

              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
            }
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_CURRENCIES, 'page=' . $_GET['page']));
        break;
      case 'update_currencies':
        if (isset($_POST['service']) && (($_POST['service'] == 'oanda') || ($_POST['service'] == 'xe'))) {
          $quote_function = 'quote_' . $_POST['service'] . '_currency';

          $Qcurrencies = $osC_Database->query('select currencies_id, code, title from :table_currencies');
          $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
          $Qcurrencies->execute();

          while ($Qcurrencies->next()) {
            $rate = $quote_function($Qcurrencies->value('code'));

            if (!empty($rate)) {
              $Qupdate = $osC_Database->query('update :table_currencies set value = :value, last_updated = now() where currencies_id = :currencies_id');
              $Qupdate->bindTable(':table_currencies', TABLE_CURRENCIES);
              $Qupdate->bindValue(':value', $rate);
              $Qupdate->bindInt(':currencies_id', $Qcurrencies->valueInt('currencies_id'));
              $Qupdate->execute();

              $osC_MessageStack->add_session('header', sprintf(TEXT_INFO_CURRENCY_UPDATED, $Qcurrencies->value('title'), $Qcurrencies->value('code'), $_POST['service']), 'success');
            } else {
              $osC_MessageStack->add_session('header', sprintf(ERROR_CURRENCY_INVALID, $Qcurrencies->value('title'), $Qcurrencies->value('code'), $_POST['service']), 'error');
            }
          }

          osC_Cache::clear('currencies');

          osc_redirect(osc_href_link_admin(FILENAME_CURRENCIES));
        }
        break;
    }
  }

  $page_contents = 'currencies.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
