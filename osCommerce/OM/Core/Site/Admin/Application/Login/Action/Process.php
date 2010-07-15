<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin\Application\Login\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\Access;
  use osCommerce\OM\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      if ( !empty($_POST['user_name']) && !empty($_POST['user_password']) ) {
        $Qadmin = Registry::get('Database')->query('select id, user_name, user_password from :table_administrators where user_name = :user_name limit 1');
        $Qadmin->bindValue(':user_name', $_POST['user_name']);
        $Qadmin->execute();

        if ( $Qadmin->numberOfRows() === 1 ) {
          if ( osc_validate_password($_POST['user_password'], $Qadmin->value('user_password')) ) {
            $_SESSION[OSCOM::getSite()]['id'] = $Qadmin->valueInt('id');
            $_SESSION[OSCOM::getSite()]['username'] = $Qadmin->value('user_name');
            $_SESSION[OSCOM::getSite()]['access'] = Access::getUserLevels($Qadmin->valueInt('id'));

            $to_application = OSCOM::getDefaultSiteApplication();

            if ( isset($_SESSION[OSCOM::getSite()]['redirect_origin']) ) {
              $to_application = $_SESSION[OSCOM::getSite()]['redirect_origin'];

              unset($_SESSION[OSCOM::getSite()]['redirect_origin']);
            }

            osc_redirect_admin(OSCOM::getLink(null, $to_application));
          }
        }
      }

      Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_error_login_invalid'), 'error');
    }
  }
?>
