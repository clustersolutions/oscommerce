<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\AddressBook\Delete;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( AddressBook::deleteEntry($_GET['Delete']) ) {
        $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('success_address_book_entry_deleted'), 'success');
      }

      osc_redirect(OSCOM::getLink(null, null, 'AddressBook', 'SSL'));
    }
  }
?>
