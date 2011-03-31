<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class LogIn {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Session = Registry::get('Session');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

// redirect the customer to a friendly cookies-must-be-enabled page if cookies
// are disabled (or the session has not started)
      if ( $OSCOM_Session->hasStarted() === false ) {
        OSCOM::redirect(OSCOM::getLink(null, 'Info', 'Cookies'));
      }

      $application->setPageTitle(OSCOM::getDef('sign_in_heading'));
      $application->setPageContent('login.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_sign_in'), OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      }
    }
  }
?>
