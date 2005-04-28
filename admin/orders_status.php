<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'localization';

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['osID']) && is_numeric($_GET['osID'])) {
          $orders_status_id = $_GET['osID'];
        } else {
          $Qstatus = $osC_Database->query('select max(orders_status_id) as orders_status_id from :table_orders_status');
          $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
          $Qstatus->execute();

          $orders_status_id = ($Qstatus->valueInt('orders_status_id') + 1);
        }

        $error = false;

        $osC_Database->startTransaction();

        foreach ($osC_Language->getAll() as $l) {
          if (isset($_GET['osID']) && is_numeric($_GET['osID'])) {
            $Qstatus = $osC_Database->query('update :table_orders_status set orders_status_name = :orders_status_name where orders_status_id = :orders_status_id and language_id = :language_id');
          } else {
            $Qstatus = $osC_Database->query('insert into :table_orders_status (orders_status_id, language_id, orders_status_name) values (:orders_status_id, :language_id, :orders_status_name)');
          }
          $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
          $Qstatus->bindInt(':orders_status_id', $orders_status_id);
          $Qstatus->bindValue(':orders_status_name', $_POST['orders_status_name'][$l['id']]);
          $Qstatus->bindInt(':language_id', $l['id']);
          $Qstatus->execute();

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if ($error === false) {
          if (isset($_POST['default']) && ($_POST['default'] == 'on') && (DEFAULT_ORDERS_STATUS_ID != $orders_status_id)) {
            $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
            $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
            $Qupdate->bindInt(':configuration_value', $orders_status_id);
            $Qupdate->bindValue(':configuration_key', 'DEFAULT_ORDERS_STATUS_ID');
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

          if (isset($_POST['default']) && ($_POST['default'] == 'on') && (DEFAULT_ORDERS_STATUS_ID != $orders_status_id)) {
            if ($clear_cache === true) {
              $osC_Cache->clear('configuration');
            }
          }

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&osID=' . $orders_status_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['osID']) && is_numeric($_GET['osID'])) {
          $Qorders = $osC_Database->query('select count(*) as total from :table_orders where orders_status = :orders_status');
          $Qorders->bindTable(':table_orders', TABLE_ORDERS);
          $Qorders->bindInt(':orders_status', $_GET['osID']);
          $Qorders->execute();

          $Qhistory = $osC_Database->query('select count(*) as total from :table_orders_status_history where orders_status_id = :orders_status_id group by orders_id');
          $Qhistory->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
          $Qhistory->bindInt(':orders_status_id', $_GET['osID']);
          $Qhistory->execute();

          if ( (DEFAULT_ORDERS_STATUS_ID == $_GET['osID']) || ($Qorders->valueInt('total') > 0) || ($Qhistory->valueInt('total') > 0) ) {
            if (DEFAULT_ORDERS_STATUS_ID == $_GET['osID']) {
              $osC_MessageStack->add_session('header', TEXT_INFO_DELETE_PROHIBITED, 'warning');
            }

            if ($Qorders->valueInt('total') > 0) {
              $osC_MessageStack->add_session('header', sprintf(TEXT_INFO_DELETE_PROHIBITED_ORDERS, $Qorders->valueInt('total')), 'warning');
            }

            if ($Qhistory->valueInt('total') > 0) {
              $osC_MessageStack->add_session('header', sprintf(TEXT_INFO_DELETE_PROHIBITED_HISTORY, $Qhistory->valueInt('total')), 'warning');
            }

            tep_redirect(tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&osID=' . $_GET['osID']));
          } else {
            $Qstatus = $osC_Database->query('delete from :table_orders_status where orders_status_id = :orders_status_id');
            $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
            $Qstatus->bindInt(':orders_status_id', $_GET['osID']);

            if ($osC_Database->isError() === false) {
              if ($Qstatus->affectedRows()) {
                $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
              } else {
                $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
              }
            } else {
              $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
            }

            tep_redirect(tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']));
          }
        }
        break;
    }
  }

  $page_contents = 'orders_status.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
