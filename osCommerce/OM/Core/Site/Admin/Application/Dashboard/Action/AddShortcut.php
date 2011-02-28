<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\Dashboard\Dashboard;
  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\Registry;

  class AddShortcut {
    public static function execute(ApplicationAbstract $application) {
      if ( !empty($_GET['shortcut']) ) {
        $application = osc_sanitize_string($_GET['shortcut']);

        if ( OSCOM::siteApplicationExists($application) ) {
          if ( Dashboard::saveShortcut($_SESSION[OSCOM::getSite()]['id'], $application) ) {
            $_SESSION[OSCOM::getSite()]['access'] = Access::getUserLevels($_SESSION[OSCOM::getSite()]['id']);

            Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_success_shortcut_added'), 'success');

            osc_redirect_admin(OSCOM::getLink(null, $application));
          }
        }
      }

      osc_redirect_admin(OSCOM::getLink());
    }
  }
?>
