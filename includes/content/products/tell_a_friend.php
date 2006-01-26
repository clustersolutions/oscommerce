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
        $_page_title,
        $_page_contents = 'tell_a_friend.php',
        $_page_image = 'table_background_products_new.gif';

/* Class constructor */

    function osC_Products_Tell_a_friend() {
      global $osC_Services, $osC_Language, $breadcrumb, $osC_Customer, $osC_NavigationHistory, $osC_Product;

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
            $this->_page_title = $osC_Language->get('product_not_found_heading');
            $this->_page_contents = 'info_not_found.php';
          } else {
            $osC_Product = new osC_Product($key);

            $this->_page_title = $osC_Product->getTitle();

            if ($osC_Services->isStarted('breadcrumb')) {
              $breadcrumb->add($osC_Product->getTitle(), tep_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()));
              $breadcrumb->add($osC_Language->get('breadcrumb_tell_a_friend'), tep_href_link(FILENAME_PRODUCTS, $this->_module . '&' . $osC_Product->getKeyword()));
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
      global $osC_Language, $messageStack, $osC_Product;

      if (empty($_POST['from_name'])) {
        $messageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_customers_name_empty'));
      }

      if (!tep_validate_email($_POST['from_email_address'])) {
        $messageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_invalid_customers_email_address'));
      }

      if (empty($_POST['to_name'])) {
        $messageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_friends_name_empty'));
      }

      if (!tep_validate_email($_POST['to_email_address'])) {
        $messageStack->add('tell_a_friend', $osC_Language->get('error_tell_a_friend_invalid_friends_email_address'));
      }

      if ($messageStack->size('tell_a_friend') < 1) {
        $email_subject = sprintf($osC_Language->get('email_tell_a_friend_subject'), tep_sanitize_string($_POST['from_name']), STORE_NAME);
        $email_body = sprintf($osC_Language->get('email_tell_a_friend_intro'), tep_sanitize_string($_POST['to_name']), tep_sanitize_string($_POST['from_name']), $osC_Product->getTitle(), STORE_NAME) . "\n\n";

        if (!empty($_POST['message'])) {
          $email_body .= tep_sanitize_string($_POST['message']) . "\n\n";
        }

        $email_body .= sprintf($osC_Language->get('email_tell_a_friend_link'), tep_href_link(FILENAME_PRODUCTS, $osC_Product->getID())) . "\n\n" .
                       sprintf($osC_Language->get('email_tell_a_friend_signature'), STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

        tep_mail(tep_sanitize_string($_POST['to_name']), tep_sanitize_string($_POST['to_email_address']), $email_subject, $email_body, tep_sanitize_string($_POST['from_name']), tep_sanitize_string($_POST['from_email_address']));

        $messageStack->add_session('header', sprintf($osC_Language->get('success_tell_a_friend_email_sent'), $osC_Product->getTitle(), tep_output_string_protected($_POST['to_name'])), 'success');

        tep_redirect(tep_href_link(FILENAME_PRODUCTS, $osC_Product->getID()));
      }
    }
  }
?>
