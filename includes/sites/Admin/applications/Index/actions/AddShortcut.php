<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_Index_Action_AddShortcut {
    public static function execute(OSCOM_ApplicationAbstract $application) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      if ( !empty($_GET['shortcut']) ) {
        $application = osc_sanitize_string($_GET['shortcut']);

        if ( OSCOM::siteApplicationExists($application) ) {
          $Qsc = $OSCOM_Database->query('insert into :table_administrator_shortcuts values (:administrators_id, :module)');
          $Qsc->bindInt(':administrators_id', $_SESSION[OSCOM::getSite()]['id']);
          $Qsc->bindValue(':module', $application);
          $Qsc->execute();

          if ( !$OSCOM_Database->isError() ) {
            $_SESSION[OSCOM::getSite()]['access'] = osC_Access::getUserLevels($_SESSION[OSCOM::getSite()]['id']);

            OSCOM_Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_success_shortcut_added'), 'success');

            osc_redirect_admin(OSCOM::getLink(null, $application));
          }
        }
      }

      osc_redirect_admin(OSCOM::getLink());
    }
  }
?>
