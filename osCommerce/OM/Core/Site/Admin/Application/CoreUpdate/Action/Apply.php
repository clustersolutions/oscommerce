<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Apply {
    public static function execute(ApplicationAbstract $application) {
      if ( !isset($_GET['v']) || !CoreUpdate::packageExists($_GET['v']) ) {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_select_version_to_view'), 'error');

        osc_redirect_admin(OSCOM::getLink());
      }

      if ( CoreUpdate::localPackageExists() && (CoreUpdate::getPackageInfo('version_to') != $_GET['v']) ) {
        CoreUpdate::deletePackage();
      }

      if ( !CoreUpdate::localPackageExists() && !CoreUpdate::downloadPackage($_GET['v']) ) {
        Registry::get('MessageStack')->add(null, OSCOM::getDef('ms_error_local_update_package_does_not_exist'), 'error');

        osc_redirect_admin(OSCOM::getLink());
      }

      $application->setPageContent('package_contents.php');
      $application->setPageTitle(sprintf(OSCOM::getDef('action_heading_apply'), CoreUpdate::getPackageInfo('version_from'), CoreUpdate::getPackageInfo('version_to')));
    }
  }
?>
