<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\Dashboard;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\ErrorHandler;
  use osCommerce\OM\Core\DateTime;

  class ErrorLog extends \osCommerce\OM\Core\Site\Admin\IndexModulesAbstract {
    public function __construct() {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Template = Registry::get('Template');

      $OSCOM_Language->loadIniFile('modules/Dashboard/ErrorLog.php');

      $this->_title = OSCOM::getDef('admin_dashboard_module_errorlog_title');
      $this->_title_link = OSCOM::getLink(null, 'ErrorLog');

      if ( Access::hasAccess(OSCOM::getSite(), 'ErrorLog') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_dashboard_module_errorlog_table_heading_date') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_dashboard_module_errorlog_table_heading_message') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        if ( ErrorHandler::getTotalEntries() > 0 ) {
          $counter = 0;

          foreach ( ErrorHandler::getAll(6) as $row ) {
            $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                            '      <td style="white-space: nowrap;">' . $OSCOM_Template->getIcon(16, 'errorlog.png') . '&nbsp;' . DateTime::getShort(DateTime::fromUnixTimestamp($row['timestamp']), true) . '</td>' .
                            '      <td>' . osc_output_string_protected(substr($row['message'], 0, 60)) . '..</td>' .
                            '    </tr>';

            $counter++;
          }
        } else {
          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');">' .
                          '      <td colspan="2">' . osc_icon('tick.png') . '&nbsp;' . OSCOM::getDef('admin_dashboard_module_errorlog_no_errors_found') . '</td>' .
                          '    </tr>';
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
