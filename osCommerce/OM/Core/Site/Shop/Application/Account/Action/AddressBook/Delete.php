<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\AddressBook;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\AddressBook;

  class Delete {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_MessageStack = Registry::get('MessageStack');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( $_GET['Delete'] == $OSCOM_Customer->getDefaultAddressID() ) {
        $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('warning_primary_address_deletion'), 'warning');
      } else {
        if ( AddressBook::checkEntry($_GET['Delete']) === false ) {
          $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('error_address_book_entry_non_existing'), 'error');
        }
      }

      if ( $OSCOM_MessageStack->size('AddressBook') > 0 ) {
        OSCOM::redirect(OSCOM::getLink(null, null, 'AddressBook', 'SSL'));
      }

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_address_book_delete_entry'), OSCOM::getLink(null, null, 'AddressBook&Delete=' . $_GET['Delete'], 'SSL'));
      }

      $application->setPageTitle(OSCOM::getDef('address_book_delete_entry_heading'));
      $application->setPageContent('address_book_delete.php');
    }
  }
?>
