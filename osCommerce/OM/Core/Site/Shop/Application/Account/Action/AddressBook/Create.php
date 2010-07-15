<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\AddressBook;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;

  class Create {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_address_book_add_entry'), OSCOM::getLink(null, null, 'AddressBook&Create', 'SSL'));
      }

      $application->setPageTitle(OSCOM::getDef('address_book_add_entry_heading'));
      $application->setPageContent('address_book_process.php');

      $OSCOM_Template->addJavascriptPhpFilename('includes/form_check.js.php');

      if ( AddressBook::numberOfEntries() >= MAX_ADDRESS_BOOK_ENTRIES ) {
        $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('error_address_book_full'));

        $application->setPageTitle(OSCOM::getDef('address_book_heading'));
        $application->setPageContent('address_book.php');

        return true;
      }
    }
  }
?>
