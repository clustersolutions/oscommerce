<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  if ( !class_exists('osC_Summary') ) {
    include('includes/classes/summary.php');
  }

  class osC_Summary_orders extends osC_Summary {

/* Class constructor */

    function osC_Summary_orders() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/summary/orders.php');

      $this->_title = $osC_Language->get('summary_orders_title');
      $this->_title_link = osc_href_link_admin(FILENAME_DEFAULT, 'orders');

      if ( osC_Access::hasAccess('orders') ) {
        $this->_setData();
      }
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Language;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . $osC_Language->get('summary_orders_table_heading_orders') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_orders_table_heading_total') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_orders_table_heading_date') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_orders_table_heading_status') . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qorders = $osC_Database->query('select o.orders_id, o.customers_name, greatest(o.date_purchased, ifnull(o.last_modified, "1970-01-01")) as date_last_modified, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by date_last_modified desc limit 6');
      $Qorders->bindTable(':table_orders', TABLE_ORDERS);
      $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
      $Qorders->bindInt(':language_id', $osC_Language->getID());
      $Qorders->execute();

      while ( $Qorders->next() ) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'orders&oID=' . $Qorders->valueInt('orders_id') . '&action=save'), osc_icon('orders.png') . '&nbsp;' . $Qorders->valueProtected('customers_name')) . '</td>' .
                        '      <td>' . strip_tags($Qorders->value('order_total')) . '</td>' .
                        '      <td>' . $Qorders->value('date_last_modified') . '</td>' .
                        '      <td>' . $Qorders->value('orders_status_name') . '</td>' .
                        '    </tr>';
      }

      $this->_data .= '  </tbody>' .
                      '</table>';

      $Qorders->freeResult();
    }
  }
?>
