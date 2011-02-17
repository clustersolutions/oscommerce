<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Site\Admin\Application\Login\Login;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $data = array('username' => $_POST['user_name'],
                    'password' => $_POST['user_password']);

      if ( Login::isValidCredentials($data) ) {
        $admin = Login::getAdmin($data['username']);

        $_SESSION[OSCOM::getSite()]['id'] = (int)$admin['id'];
        $_SESSION[OSCOM::getSite()]['username'] = $admin['user_name'];
        $_SESSION[OSCOM::getSite()]['access'] = Access::getUserLevels($admin['id']);

        $to_application = OSCOM::getDefaultSiteApplication();

        if ( isset($_SESSION[OSCOM::getSite()]['redirect_origin']) ) {
          $to_application = $_SESSION[OSCOM::getSite()]['redirect_origin'];

          unset($_SESSION[OSCOM::getSite()]['redirect_origin']);
        }

        osc_redirect_admin(OSCOM::getLink(null, $to_application));
      } else {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_error_login_invalid'), 'error');
      }
    }
  }
?>
