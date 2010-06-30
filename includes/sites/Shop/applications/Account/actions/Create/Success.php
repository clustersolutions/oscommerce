<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Account\Action\Create;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\OSCOM;

  class Success {
    public static function execute(ApplicationAbstract $application) {
      $application->setPageTitle(OSCOM::getDef('create_account_success_heading'));
      $application->setPageContent('create_success.php');
    }
  }
?>
