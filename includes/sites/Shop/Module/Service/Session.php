<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Service;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class Session implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Session', \osCommerce\OM\Session::load());

      $OSCOM_Session = Registry::get('Session');

      if ( (SERVICE_SESSION_FORCE_COOKIE_USAGE == '1') || ((bool)ini_get('session.use_only_cookies') === true) ) {
        osc_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*90);

        if ( isset($_COOKIE['cookie_test']) ) {
          $OSCOM_Session->start();
        }
      } elseif ( SERVICE_SESSION_BLOCK_SPIDERS == '1' ) {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $spider_flag = false;

        if ( !empty($user_agent) ) {
          $spiders = file('includes/spiders.txt');

          foreach ( $spiders as $spider ) {
            if ( !empty($spider) ) {
              if ( strpos($user_agent, trim($spider)) !== false ) {
                $spider_flag = true;
                break;
              }
            }
          }
        }

        if ( $spider_flag === false ) {
          $OSCOM_Session->start();
        }
      } else {
        $OSCOM_Session->start();
      }

// verify the ssl_session_id
      if ( (OSCOM::getRequestType() == 'SSL') && (SERVICE_SESSION_CHECK_SSL_SESSION_ID == '1') && (ENABLE_SSL == true) ) {
        if ( isset($_SERVER['SSL_SESSION_ID']) && ctype_xdigit($_SERVER['SSL_SESSION_ID']) ) {
          if ( !isset($_SESSION['SESSION_SSL_ID']) ) {
            $_SESSION['SESSION_SSL_ID'] = $_SERVER['SSL_SESSION_ID'];
          }

          if ( $_SESSION['SESSION_SSL_ID'] != $_SERVER['SSL_SESSION_ID'] ) {
            $OSCOM_Session->destroy();

            osc_redirect(osc_href_link(FILENAME_INFO, 'ssl_check', 'AUTO'));
          }
        }
      }

// verify the browser user agent
      if ( SERVICE_SESSION_CHECK_USER_AGENT == '1' ) {
        $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if ( !isset($_SESSION['SESSION_USER_AGENT']) ) {
          $_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
        }

        if ( $_SESSION['SESSION_USER_AGENT'] != $http_user_agent ) {
          $OSCOM_Session->destroy();

          osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
        }
      }

// verify the IP address
      if ( SERVICE_SESSION_CHECK_IP_ADDRESS == '1' ) {
        if ( !isset($_SESSION['SESSION_IP_ADDRESS']) ) {
          $_SESSION['SESSION_IP_ADDRESS'] = osc_get_ip_address();
        }

        if ( $_SESSION['SESSION_IP_ADDRESS'] != osc_get_ip_address() ) {
          $OSCOM_Session->destroy();

          osc_redirect(osc_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
        }
      }

      Registry::get('MessageStack')->loadFromSession();

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
