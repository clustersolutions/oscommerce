<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin\Module\IndexModules;

  use osCommerce\OM\Site\Admin\Application\Index\IndexModules;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Access;
  use osCommerce\OM\ErrorHandler;
  use osCommerce\OM\DateTime;

  class ErrorLog extends IndexModules {
    public function __construct() {
      Registry::get('Language')->loadIniFile('modules/IndexModules/ErrorLog.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_errorlog_title');
      $this->_title_link = OSCOM::getLink(null, 'ErrorLog');

      if ( Access::hasAccess(OSCOM::getSite(), 'ErrorLog') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_errorlog_table_heading_date') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_errorlog_table_heading_message') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        if ( ErrorHandler::getTotalEntries() > 0 ) {
          $counter = 0;

          foreach ( ErrorHandler::getAll(6) as $row ) {
            $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                            '      <td style="white-space: nowrap;">' . Registry::get('Template')->getIcon(16, 'errorlog.png') . '&nbsp;' . DateTime::getShort(DateTime::fromUnixTimestamp($row['timestamp']), true) . '</td>' .
                            '      <td>' . osc_output_string_protected(substr($row['message'], 0, 60)) . '..</td>' .
                            '    </tr>';

            $counter++;
          }
        } else {
          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');">' .
                          '      <td colspan="2">' . osc_icon('tick.png') . '&nbsp;' . OSCOM::getDef('admin_indexmodules_errorlog_no_errors_found') . '</td>' .
                          '    </tr>';
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
