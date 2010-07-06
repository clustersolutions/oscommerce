<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Products\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class Specials {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $application->setPageTitle(OSCOM::getDef('specials_heading'));
      $application->setPageContent('specials.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_specials'), OSCOM::getLink(null, null, 'Specials'));
      }
    }
  }
?>