<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Products_Tell_a_friend extends osC_Template {

/* Private variables */

    var $_module = 'tell_a_friend',
        $_group = 'products',
        $_page_title,
        $_page_contents = 'tell_a_friend.php',
        $_page_image = 'table_background_products_new.gif';

/* Class constructor */

    function osC_Products_Tell_a_friend() {
      global $osC_Services, $osC_Session, $osC_Language, $osC_Breadcrumb, $osC_Customer, $osC_NavigationHistory, $osC_Product;

      if ((ALLOW_GUEST_TO_TELL_A_FRIEND == '-1') && ($osC_Customer->isLoggedOn() === false)) {
        $osC_NavigationHistory->setSnapshot();

        osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      $counter = 0;
      foreach ($_GET as $key => $value) {
        $counter++;

        if ($counter < 2) {
          continue;
        }

        if ( (ereg('^[0-9]+(#?([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*)*$', $key) || ereg('^[a-zA-Z0-9 -_]*$', $key)) && ($key != $osC_Session->getName()) ) {
          if (osC_Product::checkEntry($key) === false) {
            $this->_page_title = $osC_Language->get('product_not_found_heading');
            $this->_page_contents = 'info_not_found.php';
          } else {
            $osC_Product = new osC_Product($key);

            $this->_page_title = $osC_Product->getTitle();

            if ($osC_Services->isStarted('breadcrumb')) {
              $osC_Breadcrumb->add($osC_Product->getTitle(), osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()));
              $osC_Breadcrumb->add($osC_Language->get('breadcrumb_tell_a_friend'), osc_href_link(FILENAME_PRODUCTS, $this->_module . '&' . $osC_Product->getKeyword()));
            }

            if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
              $this->_process();
            }
          }

          break;
        }
      }

      if ($counter < 2) {
        $this->_page_title = $osC_Language->get('product_not_found_heading');
        $this->_page_contents = 'info_not_found.php';
      }
    }

/* Private methods */

    function _process() {
      global $osC_Language, $osC_MessageStack, $osC_Product;

      if (empty($_POST['from_name'])) {
        $osC_MessageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_customers_name_empty'));
      }

      if (!osc_validate_email_address($_POST['from_email_address'])) {
        $osC_MessageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_invalid_customers_email_address'));
      }

      if (empty($_POST['to_name'])) {
        $osC_MessageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_friends_name_empty'));
      }

      if (!osc_validate_email_address($_POST['to_email_address'])) {
        $osC_MessageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_invalid_friends_email_address'));
      }

      if ($osC_MessageStack->size('tell_a_friend') < 1) {
        $email_subject = sprintf($osC_Language->get('email_tell_a_friend_subject'), osc_sanitize_string($_POST['from_name']), STORE_NAME);
        $email_body = sprintf($osC_Language->get('email_tell_a_friend_intro'), osc_sanitize_string($_POST['to_name']), osc_sanitize_string($_POST['from_name']), $osC_Product->getTitle(), STORE_NAME) . "\n\n";

        if (!empty($_POST['message'])) {
          $email_body .= osc_sanitize_string($_POST['message']) . "\n\n";
        }

        $email_body .= sprintf($osC_Language->get('email_tell_a_friend_link'), osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword(), 'NONSSL', false)) . "\n\n" .
                       sprintf($osC_Language->get('email_tell_a_friend_signature'), STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

        osc_email(osc_sanitize_string($_POST['to_name']), osc_sanitize_string($_POST['to_email_address']), $email_subject, $email_body, osc_sanitize_string($_POST['from_name']), osc_sanitize_string($_POST['from_email_address']));

        $osC_MessageStack->add('header', sprintf($osC_Language->get('success_tell_a_friend_email_sent'), $osC_Product->getTitle(), osc_output_string_protected($_POST['to_name'])), 'success');

        osc_redirect(osc_href_link(FILENAME_PRODUCTS, $osC_Product->getID()));
      }
    }
  }
?>
