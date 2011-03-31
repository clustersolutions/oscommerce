<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Info\Action\Contact;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Mail;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Process {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $name = HTML::sanitize($_POST['name']);
      $email_address = HTML::sanitize($_POST['email']);
      $enquiry = HTML::sanitize($_POST['enquiry']);

      if ( filter_var($email_address, FILTER_VALIDATE_EMAIL) ) {
        $email = new Mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $name, $email_address, OSCOM::getDef('contact_email_subject'));
        $email->setBodyPlain($enquiry);
        $email->send();

        OSCOM::redirect(OSCOM::getLink(null, null, 'Contact&Success'));
      } else {
        $OSCOM_MessageStack->add('Contact', OSCOM::getDef('field_customer_email_address_check_error'));
      }
    }
  }
?>
