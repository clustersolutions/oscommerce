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

  if ( !class_exists('osC_Statistics') ) {
    include('includes/classes/statistics.php');
  }

  class osC_Statistics_Orders extends osC_Statistics {

// Class constructor

    function osC_Statistics_Orders() {
      global $osC_Language, $osC_Currencies;

      $osC_Language->loadIniFile('modules/statistics/orders.php');

      if ( !isset($osC_Currencies) ) {
        if ( !class_exists('osC_Currencies') ) {
          include('../includes/classes/currencies.php');
        }

        $osC_Currencies = new osC_Currencies();
      }

      $this->_setIcon();
      $this->_setTitle();
    }

// Private methods

    function _setIcon() {
      $this->_icon = osc_icon('orders.png');
    }

    function _setTitle() {
      global $osC_Language;

      $this->_title = $osC_Language->get('statistics_orders_title');
    }

    function _setHeader() {
      global $osC_Language;

      $this->_header = array($osC_Language->get('statistics_orders_table_heading_customers'),
                             $osC_Language->get('statistics_orders_table_heading_total'));
    }

    function _setData() {
      global $osC_Database, $osC_Currencies;

      $this->_data = array();

      $this->_resultset = $osC_Database->query('select o.orders_id, o.customers_name, ot.value from :table_orders o, :table_orders_total ot where o.orders_id = ot.orders_id and ot.class = :class order by value desc');
      $this->_resultset->bindTable(':table_orders', TABLE_ORDERS);
      $this->_resultset->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $this->_resultset->bindValue(':class', 'total');
      $this->_resultset->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      $this->_resultset->execute();

      while ( $this->_resultset->next() ) {
        $this->_data[] = array(osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'orders&oID=' . $this->_resultset->value('orders_id') . '&action=save'), $this->_icon . '&nbsp;' . $this->_resultset->value('customers_name')),
                               $osC_Currencies->format($this->_resultset->valueInt('value')));
      }
    }
  }
?>
