<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/sites/Admin/applications/error_log/classes/error_log.php');

  class OSCOM_Site_Admin_Module_IndexModules_ErrorLog extends OSCOM_Site_Admin_Application_Index_IndexModules {
    public function __construct() {
      OSCOM_Registry::get('osC_Language')->loadIniFile('modules/IndexModules/ErrorLog.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_errorlog_title');
      $this->_title_link = OSCOM::getLink(null, 'ErrorLog');

      if ( osC_Access::hasAccess('error_log') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_errorlog_table_heading_date') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_errorlog_table_heading_message') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        $counter = 0;

        foreach ( osc_toObjectInfo(osC_ErrorLog_Admin::getAll())->get('entries') as $log ) {
          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td style="white-space: nowrap;">' . osc_icon('error.png') . '&nbsp;' . osc_output_string_protected($log['date']) . '</td>' .
                          '      <td>' . osc_output_string_protected(substr($log['message'], 0, 60)) . '..</td>' .
                          '    </tr>';

          $counter++;

          if ( $counter == 6 ) {
            break;
          }
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
