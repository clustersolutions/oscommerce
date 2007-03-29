<?php
/*
  $Id: $

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

  class osC_Summary_administrators_log extends osC_Summary {

/* Class constructor */

    function osC_Summary_administrators_log() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/summary/administrators_log.php');

      $this->_title = $osC_Language->get('summary_administrators_log_title');
      $this->_title_link = osc_href_link_admin(FILENAME_DEFAULT, 'administrators_log');

      if ( osC_Access::hasAccess('administrators_log') ) {
        $this->_setData();
      }
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Language;

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . $osC_Language->get('summary_administrators_log_table_heading_users') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_administrators_log_table_heading_module') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_administrators_log_table_heading_date') . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qlog = $osC_Database->query('select count(al.id) as total, al.id, al.module, a.user_name, al.datestamp from :table_administrators_log al, :table_administrators a where al.module in (":modules") and al.administrators_id = a.id group by al.id order by al.id desc limit 6');
      $Qlog->bindTable(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
      $Qlog->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
      $Qlog->bindRaw(':modules', implode('", "', $_SESSION['admin']['access']));
      $Qlog->execute();

      while ( $Qlog->next() ) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'administrators_log&lID=' . $Qlog->valueInt('id') . '&action=info'), osc_icon('log.png') . '&nbsp;' . $Qlog->valueProtected('user_name')) . '</td>' .
                        '      <td>' . $Qlog->value('module') . ' (' . $Qlog->valueInt('total') . ')</td>' .
                        '      <td>' . $Qlog->value('datestamp') . '</td>' .
                        '    </tr>';
      }

      $this->_data .= '  </tbody>' .
                      '</table>';

      $Qlog->freeResult();
    }
  }
?>
