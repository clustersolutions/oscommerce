<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (!class_exists('osC_Statistics')) {
    include('includes/classes/statistics.php');
  }

  class osC_Statistics_Orders extends osC_Statistics {

// Class constructor

    function osC_Statistics_Orders() {
      global $osC_Language, $osC_Currencies;

      $osC_Language->load('modules/statistics/orders.php');

      if (!isset($osC_Currencies)) {
        if (!class_exists('osC_Currencies')) {
          include('../includes/classes/currencies.php');
        }

        $osC_Currencies = new osC_Currencies();
      }

      $this->_setIcon();
      $this->_setTitle();
    }

// Private methods

    function _setIcon() {
      $this->_icon = tep_icon('orders.png', ICON_ORDERS, '16', '16');
    }

    function _setTitle() {
      $this->_title = MODULE_STATISTICS_ORDERS_TITLE;
    }

    function _setHeader() {
      $this->_header = array(MODULE_STATISTICS_ORDERS_TABLE_HEADING_CUSTOMER,
                             MODULE_STATISTICS_ORDERS_TABLE_HEADING_TOTAL);
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

      while ($this->_resultset->next()) {
        $this->_data[] = array('<a href="' . tep_href_link(FILENAME_ORDERS, 'oID=' . $this->_resultset->value('orders_id') . '&action=oEdit') . '">' . $this->_icon . '&nbsp;' . $this->_resultset->value('customers_name') . '</a>',
                               $osC_Currencies->format($this->_resultset->valueInt('value')));
      }
    }
  }
?>
