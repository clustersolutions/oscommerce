<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Info_Contact extends osC_Template {

/* Private variables */

    var $_module = 'contact',
        $_group = 'info',
        $_page_title = HEADING_INFO_CONTACT,
        $_page_contents = 'info_contact.php';

/* Class constructor */

    function osC_Info_Contact() {
      global $osC_Services, $breadcrumb;

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add(BREADCRUMB_INFO_CONTACT, tep_href_link(FILENAME_INFO, $this->_module));
      }

      if ($_GET[$this->_module] == 'process') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $messageStack;

      $name = tep_sanitize_string($_POST['name']);
      $email_address = tep_sanitize_string($_POST['email']);
      $enquiry = tep_sanitize_string($_POST['enquiry']);

      if (tep_validate_email($email_address)) {
        tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, TEXT_INFO_CONTACT_EMAIL_SUBJECT, $enquiry, $name, $email_address);

        tep_redirect(tep_href_link(FILENAME_INFO, 'contact=success', 'AUTO'));
      } else {
        $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
      }
    }
  }
?>
