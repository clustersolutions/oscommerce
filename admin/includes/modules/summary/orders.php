<?php
/*
  $Id: orders.php,v 1.2 2004/08/24 00:56:43 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (!class_exists('osC_Summary')) {
    include('includes/classes/summary.php');
  }

  if (!defined('MODULE_SUMMARY_ORDERS_TITLE')) {
    include('includes/languages/' . $osC_Session->value('language') . '/modules/summary/orders.php');
  }

  class osC_Summary_orders extends osC_Summary {

/* Class constructor */

    function osC_Summary_orders() {
      $this->_title = MODULE_SUMMARY_ORDERS_TITLE;
      $this->_title_link = tep_href_link(FILENAME_ORDERS);

      $this->_setData();
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Session, $template;

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

      $Qorders = $osC_Database->query('select o.orders_id, o.customers_name, greatest(o.date_purchased, o.last_modified) as date_last_modified, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "ot_total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by date_last_modified desc limit 6');
      $Qorders->bindTable(':table_orders', TABLE_ORDERS);
      $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qorders->bindInt(':language_id', $osC_Session->value('languages_id'));
      $Qorders->execute();

      while ($Qorders->next()) {
        $this->_data .= '    <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);">' .
                        '      <td><a href="' . tep_href_link(FILENAME_ORDERS, 'oID=' . $Qorders->valueInt('orders_id') . '&action=oEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/orders.png', ICON_PREVIEW, '16', '16') . '&nbsp;' . $Qorders->valueProtected('customers_name') . '</a></td>' .
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
