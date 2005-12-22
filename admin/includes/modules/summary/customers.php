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
    $osC_Language->load('modules/summary/customers.php');
  }

  class osC_Summary_customers extends osC_Summary {

/* Class constructor */

    function osC_Summary_customers() {
      $this->_title = MODULE_SUMMARY_CUSTOMERS_TITLE;
      $this->_title_link = tep_href_link(FILENAME_CUSTOMERS);

      $this->_setData();
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $template;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . MODULE_SUMMARY_CUSTOMERS_HEADING_CUSTOMERS . '</th>' .
                     '      <th>' . MODULE_SUMMARY_CUSTOMERS_HEADING_DATE . '</th>' .
                     '      <th>' . MODULE_SUMMARY_CUSTOMERS_HEADING_STATUS . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qcustomers = $osC_Database->query('select c.customers_id, c.customers_lastname, c.customers_firstname, c.customers_status, ci.customers_info_date_account_created from :table_customers c, :table_customers_info ci where c.customers_id = ci.customers_info_id order by ci.customers_info_date_account_created desc limit 6');
      $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomers->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
      $Qcustomers->execute();

      while ($Qcustomers->next()) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td><a href="' . tep_href_link(FILENAME_CUSTOMERS, 'cID=' . $Qcustomers->valueInt('customers_id') . '&action=cEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/personal.png', ICON_PREVIEW, '16', '16') . '&nbsp;' . $Qcustomers->valueProtected('customers_firstname') . ' ' . $Qcustomers->valueProtected('customers_lastname') . '</a></td>' .
                        '      <td>' . $Qcustomers->value('customers_info_date_account_created') . '</td>' .
                        '      <td align="center">' . tep_image('templates/' . $template . '/images/icons/' . (($Qcustomers->valueInt('customers_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')) . '</td>' .
                        '    </tr>';
      }

      $Qcustomers->freeResult();

      $this->_data .= '  </tbody>' .
                      '</table>';
    }
  }
?>
