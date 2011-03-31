<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Account\Action\Password;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Account;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( !isset($_POST['password_current']) || (strlen(trim($_POST['password_current'])) < ACCOUNT_PASSWORD) ) {
        $OSCOM_MessageStack->add('Password', sprintf(OSCOM::getDef('field_customer_password_current_error'), ACCOUNT_PASSWORD));
      } elseif ( !isset($_POST['password_new']) || (strlen(trim($_POST['password_new'])) < ACCOUNT_PASSWORD) ) {
        $OSCOM_MessageStack->add('Password', sprintf(OSCOM::getDef('field_customer_password_new_error'), ACCOUNT_PASSWORD));
      } elseif ( !isset($_POST['password_confirmation']) || (trim($_POST['password_new']) != trim($_POST['password_confirmation'])) ) {
        $OSCOM_MessageStack->add('Password', OSCOM::getDef('field_customer_password_new_mismatch_with_confirmation_error'));
      }

      if ( $OSCOM_MessageStack->size('Password') === 0 ) {
        if ( Account::checkPassword(trim($_POST['password_current'])) ) {
          if ( Account::savePassword(trim($_POST['password_new'])) ) {
            $OSCOM_MessageStack->add('Account', OSCOM::getDef('success_password_updated'), 'success');

            OSCOM::redirect(OSCOM::getLink(null, null, null, 'SSL'));
          } else {
            $OSCOM_MessageStack->add('Password', sprintf(OSCOM::getDef('field_customer_password_new_error'), ACCOUNT_PASSWORD));
          }
        } else {
          $OSCOM_MessageStack->add('Password', OSCOM::getDef('error_current_password_not_matching'));
        }
      }
    }
  }
?>
