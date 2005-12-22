<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Products_Tell_a_friend extends osC_Template {

/* Private variables */

    var $_module = 'tell_a_friend',
        $_group = 'products',
        $_page_title = HEADING_TITLE_NEW_PRODUCTS,
        $_page_contents = 'tell_a_friend.php',
        $_page_image = 'table_background_products_new.gif';

/* Class constructor */

    function osC_Products_Tell_a_friend() {
      global $osC_Services, $breadcrumb, $osC_Customer, $osC_NavigationHistory, $osC_Product;

      if ((ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') && ($osC_Customer->isLoggedOn() === false)) {
        $osC_NavigationHistory->setSnapshot();

        tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
      }

      $counter = 0;
      foreach ($_GET as $key => $value) {
        $counter++;

        if ($counter < 2) {
          continue;
        }

        if (is_numeric($key) || ereg('[0-9]+[{[0-9]+}[0-9]+]*$', $key) || ereg('[a-zA-Z0-9 -_]*$', $key)) {
          if (osC_Product::checkEntry($key) === false) {
            $this->_page_contents = 'info_not_found.php';
          } else {
            $osC_Product = new osC_Product($key);

            $this->_page_title = $osC_Product->getTitle();

            if ($osC_Services->isStarted('breadcrumb')) {
              $breadcrumb->add($osC_Product->getTitle(), tep_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()));
              $breadcrumb->add(NAVBAR_TITLE_TELL_A_FRIEND, tep_href_link(FILENAME_PRODUCTS, $this->_module . '&' . $osC_Product->getKeyword()));
            }

            if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
              $this->_process();
            }
          }

          break;
        }
      }

      if ($counter < 2) {
        $this->_page_contents = 'info_not_found.php';
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Product;

      if (empty($_POST['from_name'])) {
        $messageStack->add('tell_a_friend', ERROR_FROM_NAME);
      }

      if (!tep_validate_email($_POST['from_email_address'])) {
        $messageStack->add('tell_a_friend', ERROR_FROM_ADDRESS);
      }

      if (empty($_POST['to_name'])) {
        $messageStack->add('tell_a_friend', ERROR_TO_NAME);
      }

      if (!tep_validate_email($_POST['to_email_address'])) {
        $messageStack->add('tell_a_friend', ERROR_TO_ADDRESS);
      }

      if ($messageStack->size('tell_a_friend') < 1) {
        $email_subject = sprintf(TEXT_EMAIL_SUBJECT, tep_sanitize_string($_POST['from_name']), STORE_NAME);
        $email_body = sprintf(TEXT_EMAIL_INTRO, tep_sanitize_string($_POST['to_name']), tep_sanitize_string($_POST['from_name']), $osC_Product->getTitle(), STORE_NAME) . "\n\n";

        if (!empty($_POST['message'])) {
          $email_body .= tep_sanitize_string($_POST['message']) . "\n\n";
        }

        $email_body .= sprintf(TEXT_EMAIL_LINK, tep_href_link(FILENAME_PRODUCTS, $osC_Product->getID())) . "\n\n" .
                       sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

        tep_mail(tep_sanitize_string($_POST['to_name']), tep_sanitize_string($_POST['to_email_address']), $email_subject, $email_body, tep_sanitize_string($_POST['from_name']), tep_sanitize_string($_POST['from_email_address']));

        $messageStack->add_session('header', sprintf(SUCCESS_EMAIL_SENT, $osC_Product->getTitle(), tep_output_string_protected($_POST['to_name'])), 'success');

        tep_redirect(tep_href_link(FILENAME_PRODUCTS, $osC_Product->getID()));
      }
    }
  }
?>
