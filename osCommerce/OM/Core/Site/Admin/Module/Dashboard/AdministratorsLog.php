<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\IndexModules;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Access;

  class AdministratorsLog extends \osCommerce\OM\Core\Site\Admin\IndexModulesAbstract {
    public function __construct() {
      Registry::get('Language')->loadIniFile('modules/IndexModules/AdministratorsLog.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_administratorslog_title');
      $this->_title_link = OSCOM::getLink(null, 'AdministratorsLog');

      if ( Access::hasAccess(OSCOM::getSite(), 'AdministratorsLog') ) {
        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_administratorslog_table_heading_users') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_administratorslog_table_heading_module') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_administratorslog_table_heading_date') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        $Qlog = Registry::get('Database')->query('select count(al.id) as total, al.id, al.module, a.user_name, al.datestamp from :table_administrators_log al, :table_administrators a where al.module in (":modules") and al.administrators_id = a.id group by al.id order by al.id desc limit 6');
        $Qlog->bindRaw(':modules', implode('", "', array_keys($_SESSION[OSCOM::getSite()]['access'])));
        $Qlog->execute();

        $counter = 0;

        while ( $Qlog->next() ) {
          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td>' . osc_link_object(OSCOM::getLink(null, 'AdministratorsLog&lID=' . $Qlog->valueInt('id') . '&action=info'), osc_icon('log.png') . '&nbsp;' . $Qlog->valueProtected('user_name')) . '</td>' .
                          '      <td>' . $Qlog->value('module') . ' (' . $Qlog->valueInt('total') . ')</td>' .
                          '      <td>' . $Qlog->value('datestamp') . '</td>' .
                          '    </tr>';

          $counter++;
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>
