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
        $_page_title,
        $_page_contents = 'info_contact.php';

/* Class constructor */

    function osC_Info_Contact() {
      global $osC_Services, $osC_Language, $breadcrumb;

      $this->_page_title = $osC_Language->get('info_contact_heading');

      if ($osC_Services->isStarted('breadcrumb')) {
        $breadcrumb->add($osC_Language->get('breadcrumb_contact'), tep_href_link(FILENAME_INFO, $this->_module));
      }

      if ($_GET[$this->_module] == 'process') {
        $this->_process();
      }
    }

/* Private methods */

    function _process() {
      global $osC_Language, $messageStack;

      $name = tep_sanitize_string($_POST['name']);
      $email_address = tep_sanitize_string($_POST['email']);
      $enquiry = tep_sanitize_string($_POST['enquiry']);

      if (tep_validate_email($email_address)) {
        tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $osC_Language->get('contact_email_subject'), $enquiry, $name, $email_address);

        tep_redirect(tep_href_link(FILENAME_INFO, 'contact=success', 'AUTO'));
      } else {
        $messageStack->add('contact', $osC_Language->get('field_customer_email_address_check_error'));
      }
    }
  }
?>
