<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Orders extends osC_Template {

/* Private variables */

    var $_module = 'orders',
        $_page_title = HEADING_TITLE,
        $_page_contents = 'orders.php';

/* Class constructor */

    function osC_Content_Orders() {
      global $osC_Database, $osC_Language, $osC_Currencies, $orders_statuses, $orders_status_array;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['section'])) {
        $_GET['section'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      include('includes/classes/order.php');

      include('../includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies();

      $orders_statuses = array();
      $orders_status_array = array();

      $Qstatuses = $osC_Database->query('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id');
      $Qstatuses->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qstatuses->bindInt(':language_id', $osC_Language->getID());
      $Qstatuses->execute();

      while ($Qstatuses->next()) {
        $orders_statuses[] = array('id' => $Qstatuses->valueInt('orders_status_id'),
                                   'text' => $Qstatuses->value('orders_status_name'));

        $orders_status_array[$Qstatuses->valueInt('orders_status_id')] = $Qstatuses->value('orders_status_name');
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'oEdit':
            $this->_page_contents = 'orders_edit.php';
            break;

          case 'update_transaction':
            $this->_updateTransaction();
            break;

          case 'update_order':
            $this->_update();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _updateTransaction() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['oID']) && is_numeric($_GET['oID'])) {
        if (isset($_POST['transaction'])) {
          $Qorder = $osC_Database->query('select payment_module from :table_orders where orders_id = :orders_id limit 1');
          $Qorder->bindTable(':table_orders', TABLE_ORDERS);
          $Qorder->bindInt(':orders_id', $_GET['oID']);
          $Qorder->execute();

          if ( ($Qorder->numberOfRows() === 1) && !osc_empty($Qorder->value('payment_module'))) {
            if (file_exists('includes/modules/payment/' . $Qorder->value('payment_module') . '.php')) {
              include('includes/classes/payment.php');
              include('includes/modules/payment/' . $Qorder->value('payment_module') . '.php');

              if (is_callable(array('osC_Payment_' . $Qorder->value('payment_module'), $_POST['transaction']))) {
                $payment_module = 'osC_Payment_' . $Qorder->value('payment_module');
                $payment_module = new $payment_module();
                $payment_module->$_POST['transaction']($_GET['oID']);
// HPDL - the following static call won't work due to using $this->_gateway_url in the class method
//                call_user_func(array('osC_Payment_' . $Qorder->value('payment_module'), $_POST['transaction']), $_GET['oID']);
              }
            }
          }
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=oEdit&section=transactionHistory'));
    }

    function _update() {
      global $osC_Database, $osC_MessageStack, $orders_status_array;

      if (isset($_GET['oID']) && is_numeric($_GET['oID'])) {
        $Qorder = $osC_Database->query('select customers_name, customers_email_address, orders_status, date_purchased from :table_orders where orders_id = :orders_id');
        $Qorder->bindTable(':table_orders', TABLE_ORDERS);
        $Qorder->bindInt(':orders_id', $_GET['oID']);
        $Qorder->execute();

        if ($Qorder->numberOfRows() === 1) {
          if (($_POST['status'] != $Qorder->valueInt('orders_status')) || !empty($_POST['comment'])) {
            $Qupdate = $osC_Database->query('update :table_orders set orders_status = :orders_status, last_modified = now() where orders_id = :orders_id');
            $Qupdate->bindTable(':table_orders', TABLE_ORDERS);
            $Qupdate->bindInt(':orders_status', $_POST['status']);
            $Qupdate->bindInt(':orders_id', $_GET['oID']);
            $Qupdate->execute();

            if ($Qupdate->affectedRows()) {
              if (isset($_POST['notify_customer']) && ($_POST['notify_customer'] == 'on')) {
                $email_body = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $_GET['oID'] . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . osc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $_GET['oID'], 'SSL', false, false, true) . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . osC_DateTime::getLong($Qorder->value('date_purchased')) . "\n\n";

                if (isset($_POST['append_comment']) && ($_POST['append_comment'] == 'on')) {
                  $email_body .= sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $_POST['comment']) . "\n\n";
                }

                $email_body .= sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$_POST['status']]);

                osc_email($Qorder->value('customers_name'), $Qorder->value('customers_email_address'), EMAIL_TEXT_SUBJECT, $email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
              }

              $Qupdate = $osC_Database->query('insert into :table_orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) values (:orders_id, :orders_status_id, now(), :customer_notified, :comments)');
              $Qupdate->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
              $Qupdate->bindInt(':orders_id', $_GET['oID']);
              $Qupdate->bindInt(':orders_status_id', $_POST['status']);
              $Qupdate->bindInt(':customer_notified', (isset($_POST['notify_customer']) && ($_POST['notify_customer'] == 'on') ? '1' : '0'));
              $Qupdate->bindValue(':comments', $_POST['comment']);
              $Qupdate->execute();

              $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
              $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
            }
          } else {
            $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=oEdit&section=statusHistory'));
    }

    function _delete() {
      osC_Order::delete($_GET['oID'], (isset($_POST['restock']) && ($_POST['restock'] == 'on') ? true : false));

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page']));
    }
  }
?>
