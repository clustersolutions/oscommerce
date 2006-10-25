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

  if (!defined('MODULE_SUMMARY_CUSTOMERS_TITLE')) {
    $osC_Language->loadConstants('modules/summary/customers.php');
  }

  class osC_Summary_customers extends osC_Summary {

/* Class constructor */

    function osC_Summary_customers() {
      $this->_title = MODULE_SUMMARY_CUSTOMERS_TITLE;
      $this->_title_link = osc_href_link(FILENAME_DEFAULT, 'customers');

      if ( osC_Access::hasAccess( 'customers' ) ) {
        $this->_setData();
      }
    }

/* Private methods */

    function _setData() {
      global $osC_Database;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . MODULE_SUMMARY_CUSTOMERS_HEADING_CUSTOMERS . '</th>' .
                     '      <th>' . MODULE_SUMMARY_CUSTOMERS_HEADING_DATE . '</th>' .
                     '      <th>' . MODULE_SUMMARY_CUSTOMERS_HEADING_STATUS . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qcustomers = $osC_Database->query('select customers_id, customers_lastname, customers_firstname, customers_status, date_account_created from :table_customers order by date_account_created desc limit 6');
      $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomers->execute();

      while ($Qcustomers->next()) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link(FILENAME_DEFAULT, 'customers&cID=' . $Qcustomers->valueInt('customers_id') . '&action=cEdit'), osc_icon('personal.png', ICON_PREVIEW) . '&nbsp;' . $Qcustomers->valueProtected('customers_firstname') . ' ' . $Qcustomers->valueProtected('customers_lastname')) . '</td>' .
                        '      <td>' . $Qcustomers->value('date_account_created') . '</td>' .
                        '      <td align="center">' . osc_icon(($Qcustomers->valueInt('customers_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' .
                        '    </tr>';
      }

      $Qcustomers->freeResult();

      $this->_data .= '  </tbody>' .
                      '</table>';
    }
  }
?>
