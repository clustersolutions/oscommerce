<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Info\Action\Contact;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $name = osc_sanitize_string($_POST['name']);
      $email_address = osc_sanitize_string($_POST['email']);
      $enquiry = osc_sanitize_string($_POST['enquiry']);

      if ( osc_validate_email_address($email_address) ) {
        osc_email(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, OSCOM::getDef('contact_email_subject'), $enquiry, $name, $email_address);

        osc_redirect(OSCOM::getLink(null, null, 'Contact&Success'));
      } else {
        $OSCOM_MessageStack->add('Contact', OSCOM::getDef('field_customer_email_address_check_error'));
      }
    }
  }
?>
