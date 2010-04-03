<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Module_IndexModules_Orders extends OSCOM_Site_Admin_Application_Index_IndexModules {
    public function __construct() {
      OSCOM_Registry::get('osC_Language')->loadIniFile('modules/IndexModules/Orders.php');

      $this->_title = __('admin_indexmodules_orders_title');
      $this->_title_link = OSCOM::getLink(null, 'Orders');

      if ( osC_Access::hasAccess('orders') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . __('admin_indexmodules_orders_table_heading_orders') . '</th>' .
                       '      <th>' . __('admin_indexmodules_orders_table_heading_total') . '</th>' .
                       '      <th>' . __('admin_indexmodules_orders_table_heading_date') . '</th>' .
                       '      <th>' . __('admin_indexmodules_orders_table_heading_status') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        $Qorders = OSCOM_Registry::get('Database')->query('select o.orders_id, o.customers_name, greatest(o.date_purchased, ifnull(o.last_modified, "1970-01-01")) as date_last_modified, s.orders_status_name, ot.text as order_total from :table_orders o, :table_orders_total ot, :table_orders_status s where o.orders_id = ot.orders_id and ot.class = "total" and o.orders_status = s.orders_status_id and s.language_id = :language_id order by date_last_modified desc limit 6');
        $Qorders->bindTable(':table_orders', TABLE_ORDERS);
        $Qorders->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
        $Qorders->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
        $Qorders->bindInt(':language_id', OSCOM_Registry::get('osC_Language')->getID());
        $Qorders->execute();

        $counter = 0;

        while ( $Qorders->next() ) {
          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td>' . osc_link_object(OSCOM::getLink(null, 'Orders', 'oID=' . $Qorders->valueInt('orders_id') . '&action=save'), osc_icon('orders.png') . '&nbsp;' . $Qorders->valueProtected('customers_name')) . '</td>' .
                          '      <td>' . strip_tags($Qorders->value('order_total')) . '</td>' .
                          '      <td>' . $Qorders->value('date_last_modified') . '</td>' .
                          '      <td>' . $Qorders->value('orders_status_name') . '</td>' .
                          '    </tr>';

          $counter++;
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
