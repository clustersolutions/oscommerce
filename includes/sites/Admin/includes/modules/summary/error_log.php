<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/error_log/classes/error_log.php');

  if ( !class_exists('osC_Summary') ) {
    include('includes/classes/summary.php');
  }

  class osC_Summary_error_log extends osC_Summary {

/* Class constructor */

    function __construct() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/summary/error_log.php');

      $this->_title = $osC_Language->get('summary_error_log_title');
      $this->_title_link = osc_href_link_admin(FILENAME_DEFAULT, 'error_log');

      if ( osC_Access::hasAccess('error_log') ) {
        $this->_setData();
      }
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Language;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . $osC_Language->get('summary_error_log_table_heading_date') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_error_log_table_heading_message') . '</th>' .
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
?>
