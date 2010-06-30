<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Account\Action\PasswordForgotten;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Account;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $Qcheck = $OSCOM_Database->query('select customers_id, customers_firstname, customers_lastname, customers_gender, customers_email_address, customers_password from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
      $Qcheck->execute();

      if ( $Qcheck->numberOfRows() === 1 ) {
        $password = osc_create_random_string(ACCOUNT_PASSWORD);

        if ( Account::savePassword($password, $Qcheck->valueInt('customers_id')) ) {
          if ( ACCOUNT_GENDER > -1 ) {
             if ( $Qcheck->value('customers_gender') == 'm' ) {
               $email_text = sprintf(OSCOM::getDef('email_addressing_gender_male'), $Qcheck->valueProtected('customers_lastname')) . "\n\n";
             } else {
               $email_text = sprintf(OSCOM::getDef('email_addressing_gender_female'), $Qcheck->valueProtected('customers_lastname')) . "\n\n";
             }
          } else {
            $email_text = sprintf(OSCOM::getDef('email_addressing_gender_unknown'), $Qcheck->valueProtected('customers_firstname') . ' ' . $Qcheck->valueProtected('customers_lastname')) . "\n\n";
          }

          $email_text .= sprintf(OSCOM::getDef('email_password_reminder_body'), osc_get_ip_address(), STORE_NAME, $password, STORE_OWNER_EMAIL_ADDRESS);

          osc_email($Qcheck->valueProtected('customers_firstname') . ' ' . $Qcheck->valueProtected('customers_lastname'), $Qcheck->valueProtected('customers_email_address'), sprintf(OSCOM::getDef('email_password_reminder_subject'), STORE_NAME), $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

          $OSCOM_MessageStack->add('LogIn', OSCOM::getDef('success_password_forgotten_sent'), 'success');
        }

        osc_redirect(OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      } else {
        $OSCOM_MessageStack->add('PasswordForgotten', OSCOM::getDef('error_password_forgotten_no_email_address_found'));
      }
    }
  }
?>
