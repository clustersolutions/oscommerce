<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\LogIn;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Account;
  use osCommerce\OM\Core\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( !empty($_POST['email_address']) && !empty($_POST['password']) && Account::logIn($_POST['email_address'], $_POST['password']) ) {
        $OSCOM_NavigationHistory->removeCurrentPage();

        if ( $OSCOM_NavigationHistory->hasSnapshot() ) {
          $OSCOM_NavigationHistory->redirectToSnapshot();
        } else {
          OSCOM::redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication(), null, 'AUTO'));
        }
      }

      $OSCOM_MessageStack->add('LogIn', OSCOM::getDef('error_login_no_match'));
    }
  }
?>
