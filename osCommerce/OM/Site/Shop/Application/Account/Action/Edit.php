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

  class Edit {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( $OSCOM_Customer->isLoggedOn() === false ) {
        $OSCOM_NavigationHistory->setSnapshot();

        osc_redirect(OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      }

      $application->setPageTitle(OSCOM::getDef('account_edit_heading'));
      $application->setPageContent('edit.php');

      $OSCOM_Template->addJavascriptPhpFilename('includes/form_check.js.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_edit_account'), OSCOM::getLink(null, null, 'Edit', 'SSL'));
      }
    }
  }
?>
