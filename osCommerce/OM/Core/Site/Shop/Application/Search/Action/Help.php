<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Search\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class Help {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');

// HPDL
      $OSCOM_Template->setHasHeader(false);
      $OSCOM_Template->setHasFooter(false);
      $OSCOM_Template->setHasBoxModules(false);
      $OSCOM_Template->setHasContentModules(false);
      $OSCOM_Template->setShowDebugMessages(false);

      $OSCOM_NavigationHistory->removeCurrentPage();

      $application->setPageTitle(OSCOM::getDef('search_heading'));
      $application->setPageContent('help.php');
    }
  }
?>
