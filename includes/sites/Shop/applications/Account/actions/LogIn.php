<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Account\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Account;

  class LogIn {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Session = Registry::get('Session');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

// redirect the customer to a friendly cookies-must-be-enabled page if cookies
// are disabled (or the session has not started)
      if ( $OSCOM_Session->hasStarted() === false ) {
        osc_redirect(OSCOM::getLink(null, 'Info', 'Cookies'));
      }

      $application->setPageTitle(OSCOM::getDef('sign_in_heading'));
      $application->setPageContent('login.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_sign_in'), OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'process') ) {
        self::_process();
      }
    }

    protected static function _process() {
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( !empty($_POST['email_address']) && !empty($_POST['password']) && Account::logIn($_POST['email_address'], $_POST['password']) ) {
        $OSCOM_NavigationHistory->removeCurrentPage();

        if ( $OSCOM_NavigationHistory->hasSnapshot() ) {
          $OSCOM_NavigationHistory->redirectToSnapshot();
        } else {
          osc_redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication(), '', 'AUTO'));
        }
      }

      $OSCOM_MessageStack->add('Login', OSCOM::getDef('error_login_no_match'));
    }
  }
?>
