<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Dashboard;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Access;

  class Customers extends \osCommerce\OM\Core\Site\Admin\IndexModulesAbstract {
    public function __construct() {
      Registry::get('Language')->loadIniFile('modules/Dashboard/Customers.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_customers_title');
      $this->_title_link = OSCOM::getLink(null, 'Customers');

      if ( Access::hasAccess(OSCOM::getSite(), 'Customers') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_customers_table_heading_customers') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_customers_table_heading_date') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_customers_table_heading_status') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        $Qcustomers = Registry::get('PDO')->query('select customers_id, customers_gender, customers_lastname, customers_firstname, customers_status, date_account_created from :table_customers order by date_account_created desc limit 6');
        $Qcustomers->execute();

        $counter = 0;

        while ( $Qcustomers->fetch() ) {
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

          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td>' . osc_link_object(OSCOM::getLink(null, 'Customers', 'cID=' . $Qcustomers->valueInt('customers_id') . '&action=save'), $customer_icon . '&nbsp;' . $Qcustomers->valueProtected('customers_firstname') . ' ' . $Qcustomers->valueProtected('customers_lastname')) . '</td>' .
                          '      <td>' . $Qcustomers->value('date_account_created') . '</td>' .
                          '      <td align="center">' . osc_icon(($Qcustomers->valueInt('customers_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' .
                          '    </tr>';

          $counter++;
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
