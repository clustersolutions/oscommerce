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

  class osC_Summary_customers extends osC_Summary {

/* Class constructor */

    function osC_Summary_customers() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/summary/customers.php');

      $this->_title = $osC_Language->get('summary_customers_title');
      $this->_title_link = osc_href_link_admin(FILENAME_DEFAULT, 'customers');

      if ( osC_Access::hasAccess('customers') ) {
        $this->_setData();
      }
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Language;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . $osC_Language->get('summary_customers_table_heading_customers') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_customers_table_heading_date') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_customers_table_heading_status') . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qcustomers = $osC_Database->query('select customers_id, customers_gender, customers_lastname, customers_firstname, customers_status, date_account_created from :table_customers order by date_account_created desc limit 6');
      $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qcustomers->execute();

      while ( $Qcustomers->next() ) {
        $customer_icon = osc_icon('people.png');

        if ( ACCOUNT_GENDER > -1 ) {
          switch ( $Qcustomers->value('customers_gender') ) {
            case 'm':
              $customer_icon = osc_icon('user_male.png');

              break;

            case 'f':
              $customer_icon = osc_icon('user_female.png');

              break;
          }
        }

        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'customers&cID=' . $Qcustomers->valueInt('customers_id') . '&action=save'), $customer_icon . '&nbsp;' . $Qcustomers->valueProtected('customers_firstname') . ' ' . $Qcustomers->valueProtected('customers_lastname')) . '</td>' .
                        '      <td>' . $Qcustomers->value('date_account_created') . '</td>' .
                        '      <td align="center">' . osc_icon(($Qcustomers->valueInt('customers_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' .
                        '    </tr>';
      }

      $this->_data .= '  </tbody>' .
                      '</table>';

      $Qcustomers->freeResult();
    }
  }
?>
