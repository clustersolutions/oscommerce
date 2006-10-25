<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (!class_exists('osC_Summary')) {
    include('includes/classes/summary.php');
  }

  if (!defined('MODULE_SUMMARY_ORDERS_TITLE')) {
    $osC_Language->loadConstants('modules/summary/orders.php');
  }

  class osC_Summary_orders extends osC_Summary {

/* Class constructor */

    function osC_Summary_orders() {
      $this->_title = MODULE_SUMMARY_ORDERS_TITLE;
      $this->_title_link = osc_href_link_admin(FILENAME_DEFAULT, 'orders');

      $this->_setData();
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Language;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . MODULE_SUMMARY_ORDERS_HEADING_ORDERS . '</th>' .
                     '      <th>' . MODULE_SUMMARY_ORDERS_HEADING_TOTAL . '</th>' .
                     '      <th>' . MODULE_SUMMARY_ORDERS_HEADING_DATE . '</th>' .
                     '      <th>' . MODULE_SUMMARY_ORDERS_HEADING_STATUS . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qorders = $osC_Database->query('select o.orders_id, o.customers_name, greatest(o.date_purchased, ifnull(o.last_modified, 0)) as date_last_modified, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by date_last_modified desc limit 6');
      $Qorders->bindTable(':table_orders', TABLE_ORDERS);
      $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qorders->bindInt(':language_id', $osC_Language->getID());
      $Qorders->execute();

      while ($Qorders->next()) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'orders&oID=' . $Qorders->valueInt('orders_id') . '&action=oEdit'), osc_icon('orders.png', ICON_PREVIEW) . '&nbsp;' . $Qorders->valueProtected('customers_name')) . '</td>' .
                        '      <td>' . strip_tags($Qorders->value('order_total')) . '</td>' .
                        '      <td>' . $Qorders->value('date_last_modified') . '</td>' .
                        '      <td>' . $Qorders->value('orders_status_name') . '</td>' .
                        '    </tr>';
      }

      $Qorders->freeResult();

      $this->_data .= '  </tbody>' .
                      '</table>';
    }
  }
?>
