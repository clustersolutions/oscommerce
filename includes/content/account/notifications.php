<?php
/*
  $Id:notifications.php 188 2005-09-15 02:25:52 +0200 (Do, 15 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Account_Notifications extends osC_Template {

/* Private variables */

    var $_module = 'notifications',
        $_group = 'account',
        $_page_title = HEADING_TITLE_NOTIFICATIONS,
        $_page_contents = 'account_notifications.php';

/* Class constructor */

    function osC_Account_Notifications() {
      global $osC_Services, $breadcrumb, $osC_Database, $osC_Customer, $Qglobal;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(NAVBAR_TITLE_NOTIFICATIONS, tep_href_link(FILENAME_ACCOUNT, $this->_module, 'SSL'));
      }

/////////////////////// HPDL /////// Should be moved to the customers class!
      $Qglobal = $osC_Database->query('select global_product_notifications from :table_customers_info where customers_info_id = :customers_info_id');
      $Qglobal->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
      $Qglobal->bindInt(':customers_info_id', $osC_Customer->getID());
      $Qglobal->execute();

      if ($_GET[$this->_module] == 'save') {
        $this->_process();
      }
    }

/* Public methods */

    function &getListing() {
      global $osC_Database, $osC_Session, $osC_Customer, $osC_Language;

      $Qproducts = $osC_Database->query('select pd.products_id, pd.products_name from :table_products_description pd, :table_products_notifications pn where pn.customers_id = :customers_id and pn.products_id = pd.products_id and pd.language_id = :language_id order by pd.products_name');
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
      $Qproducts->bindInt(':customers_id', $osC_Customer->getID());
      $Qproducts->bindInt(':language_id', $osC_Language->getID());
      $Qproducts->execute();

      return $Qproducts;
    }

    function hasCustomerProductNotifications($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select count(*) as total from :table_products_notifications where customers_id = :customers_id');
      $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
      $Qcheck->bindInt(':customers_id', $id);
      $Qcheck->execute();

      return ($Qcheck->valueInt('total') > 0);
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Database, $osC_Customer, $Qglobal;

      $updated = false;

      if (isset($_POST['product_global']) && is_numeric($_POST['product_global'])) {
        $product_global = $_POST['product_global'];
      } else {
        $product_global = '0';
      }

      if (isset($_POST['products'])) {
        (array)$products = $_POST['products'];
      } else {
        $products = array();
      }

      if ($product_global != $Qglobal->valueInt('global_product_notifications')) {
        $product_global = (($Qglobal->valueInt('global_product_notifications') == '1') ? '0' : '1');

        $Qupdate = $osC_Database->query('update :table_customers_info set global_product_notifications = :global_product_notifications where customers_info_id = :customers_info_id');
        $Qupdate->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
        $Qupdate->bindInt(':global_product_notifications', $product_global);
        $Qupdate->bindInt(':customers_info_id', $osC_Customer->getID());
        $Qupdate->execute();

        if ($Qupdate->affectedRows() == 1) {
          $updated = true;
        }
      } elseif (sizeof($products) > 0) {
        $products_parsed = tep_array_filter($products, 'is_numeric');

        if (sizeof($products_parsed) > 0) {
          $Qcheck = $osC_Database->query('select count(*) as total from :table_products_notifications where customers_id = :customers_id and products_id not in :products_id');
          $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
          $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
          $Qcheck->bindRaw(':products_id', '(' . implode(',', $products_parsed) . ')');
          $Qcheck->execute();

          if ($Qcheck->valueInt('total') > 0) {
            $Qdelete = $osC_Database->query('delete from :table_products_notifications where customers_id = :customers_id and products_id not in :products_id');
            $Qdelete->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
            $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
            $Qdelete->bindRaw(':products_id', '(' . implode(',', $products_parsed) . ')');
            $Qdelete->execute();

            if ($Qdelete->affectedRows() > 0) {
              $updated = true;
            }
          }
        }
      } else {
        $Qcheck = $osC_Database->query('select count(*) as total from :table_products_notifications where customers_id = :customers_id');
        $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
        $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') > 0) {
          $Qdelete = $osC_Database->query('delete from :table_products_notifications where customers_id = :customers_id');
          $Qdelete->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
          $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
          $Qdelete->execute();

          if ($Qdelete->affectedRows() > 0) {
            $updated = true;
          }
        }
      }

      if ($updated === true) {
        $messageStack->add_session('account', SUCCESS_NOTIFICATIONS_UPDATED, 'success');
      }

      tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
    }
  }
?>
