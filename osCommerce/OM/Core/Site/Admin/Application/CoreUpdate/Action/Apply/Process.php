<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Action\Apply;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      if ( !CoreUpdate::localPackageExists() || (CoreUpdate::getPackageInfo('version_from') != OSCOM::getVersion()) ) {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_wrong_version_to_update_from'), 'error');

        OSCOM::redirect(OSCOM::getLink());
      }

      if ( CoreUpdate::canApplyPackage() ) {
        if ( CoreUpdate::applyPackage() ) {
          CoreUpdate::deletePackage();

          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_success_action_performed'), 'success');
        } else {
          Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_action_not_performed'), 'error');
        }
      } else {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_check_target_permissions'), 'error');

        OSCOM::redirect(OSCOM::getLink(null, null, 'Apply&v=' . $_GET['v']));
      }

      OSCOM::redirect(OSCOM::getLink());
    }
  }
?>
