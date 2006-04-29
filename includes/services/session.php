<?php
/*
  $Id:session.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_session {
    var $title = 'Session',
        $description = 'The session manager for guests, customers, and spider robots.',
        $uninstallable = false,
        $depends,
        $precedes;

    function start() {
      global $request_type, $osC_Session, $messageStack;

      include('includes/classes/session.php');
      $osC_Session = new osC_Session();

      if (SERVICE_SESSION_FORCE_COOKIE_USAGE == '1') {
        tep_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*90);

        if (isset($_COOKIE['cookie_test'])) {
          $osC_Session->start();
        }
      } elseif (SERVICE_SESSION_BLOCK_SPIDERS == '1') {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $spider_flag = false;

        if (empty($user_agent) === false) {
          $spiders = file('includes/spiders.txt');

          foreach ($spiders as $spider) {
            if (empty($spider) === false) {
              if (strpos($user_agent, trim($spider)) !== false) {
                $spider_flag = true;
                break;
              }
            }
          }
        }

        if ($spider_flag === false) {
          $osC_Session->start();
        }
      } else {
        $osC_Session->start();
      }

// verify the ssl_session_id
      if ( ($request_type == 'SSL') && (SERVICE_SESSION_CHECK_SSL_SESSION_ID == '1') && (ENABLE_SSL == true) ) {
        if (isset($_SERVER['SSL_SESSION_ID']) && ctype_xdigit($_SERVER['SSL_SESSION_ID'])) {
          if (isset($_SESSION['SESSION_SSL_ID']) === false) {
            $_SESSION['SESSION_SSL_ID'] = $_SERVER['SSL_SESSION_ID'];
          }

          if ($_SESSION['SESSION_SSL_ID'] != $_SERVER['SSL_SESSION_ID']) {
            $osC_Session->destroy();

            tep_redirect(tep_href_link(FILENAME_INFO, 'ssl_check', 'AUTO'));
          }
        }
      }

// verify the browser user agent
      if (SERVICE_SESSION_CHECK_USER_AGENT == '1') {
        $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if (isset($_SESSION['SESSION_USER_AGENT']) === false) {
          $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
        }

        if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
          $osC_Session->destroy();

          tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
        }
      }

// verify the IP address
      if (SERVICE_SESSION_CHECK_IP_ADDRESS == '1') {
        if (isset($_SESSION['SESSION_IP_ADDRESS']) === false) {
          $_SESSION['SESSION_IP_ADDRESS'] = tep_get_ip_address();
        }

        if ($_SESSION['SESSION_IP_ADDRESS'] != tep_get_ip_address()) {
          $osC_Session->destroy();

          tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
        }
      }

// add messages in the session to the message stack
      $messageStack->loadFromSession();

      return true;
    }

    function stop() {
      global $osC_Session;

      $osC_Session->close();

      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Force Cookie Usage', 'SERVICE_SESSION_FORCE_COOKIE_USAGE', '-1', 'Only start a session when cookies are enabled.', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Block Search Engine Spiders', 'SERVICE_SESSION_BLOCK_SPIDERS', '-1', 'Block search engine spider robots from starting a session.', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Check SSL Session ID', 'SERVICE_SESSION_CHECK_SSL_SESSION_ID', '-1', 'Check the SSL_SESSION_ID on every secure HTTPS page request.', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Check User Agent', 'SERVICE_SESSION_CHECK_USER_AGENT', '-1', 'Check the browser user agent on every page request.', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Check IP Address', 'SERVICE_SESSION_CHECK_IP_ADDRESS', '-1', 'Check the IP address on every page request.', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('Regenerate Session ID', 'SERVICE_SESSION_REGENERATE_ID', '-1', 'Regenerate the session ID when a customer logs on or creates an account (requires PHP >= 4.1).', '6', '0', 'osc_cfg_get_boolean_value', 'tep_cfg_select_option(array(1, -1), ', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('SERVICE_SESSION_FORCE_COOKIE_USAGE', 'SERVICE_SESSION_BLOCK_SPIDERS', 'SERVICE_SESSION_CHECK_SSL_SESSION_ID', 'SERVICE_SESSION_CHECK_USER_AGENT', 'SERVICE_SESSION_CHECK_IP_ADDRESS', 'SERVICE_SESSION_REGENERATE_ID');
    }
  }
?>
