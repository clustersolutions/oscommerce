<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

  define('OSC_IN_ADMIN', true);

// set the level of error reporting to E_ALL except E_NOTICE
  error_reporting(E_ALL ^ E_NOTICE);

// set the local configuration parameters - mainly for developers
  if ( file_exists('../includes/local/configure.php') ) {
    include('../includes/local/configure.php');
  }

// include server parameters
  require('../includes/configure.php');

// set the level of error reporting to E_ALL
  error_reporting(E_ALL);

  ini_set('log_errors', true);
  ini_set('error_log', DIR_FS_WORK . 'oscommerce_errors.log');

// Define the project version
  define('PROJECT_VERSION', 'osCommerce Online Merchant v3.0a5');

// set the type of request (secure or not)
  $request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) ? 'SSL' : 'NONSSL';

  if ($request_type == 'NONSSL') {
    define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
  } else {
    define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
  }

// compatibility work-around logic for PHP4
  require('../includes/functions/compatibility.php');
  require('includes/functions/compatibility.php');

// include the list of project filenames
  require('includes/filenames.php');

// include the list of project database tables
  require('../includes/database_tables.php');

// initialize the cache class
  require('../includes/classes/cache.php');
  $osC_Cache = new osC_Cache();

// include the administrators log class
  if ( file_exists('includes/applications/administrators_log/classes/administrators_log.php') ) {
    include('includes/applications/administrators_log/classes/administrators_log.php');
  }

// include the database class
  require('../includes/classes/database.php');

  $osC_Database = osC_Database::connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
  $osC_Database->selectDatabase(DB_DATABASE);

// set application wide parameters
  $Qcfg = $osC_Database->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
  $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qcfg->setCache('configuration');
  $Qcfg->execute();

  while ($Qcfg->next()) {
    define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
  }

  $Qcfg->freeResult();

// define our general functions used application-wide
  require('../includes/functions/general.php');
  require('includes/functions/general.php');

  require('../includes/functions/html_output.php');
  require('includes/functions/html_output.php');

// include session class
  require('../includes/classes/session.php');
  $osC_Session = osC_Session::load('osCAdminID');
  $osC_Session->start();

  if ( !isset($_SESSION['admin']) && (basename($_SERVER['PHP_SELF']) != FILENAME_RPC) ) {
    $redirect = false;

    if ( empty($_GET) ) {
      $redirect = true;
    } else {
      $first_array = array_slice($_GET, 0, 1);
      $_module = osc_sanitize_string(basename(key($first_array)));

      if ( $_module != 'login' ) {
        if ( !isset($_SESSION['redirect_origin']) ) {
          $_SESSION['redirect_origin'] = array('module' => $_module,
                                               'get' => $_GET);
        }

        $redirect = true;
      }
    }

    if ( $redirect === true ) {
      osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, 'login'));
    }

    unset($redirect);
  }

  require('includes/classes/directory_listing.php');
  require('includes/classes/access.php');

  require('../includes/classes/address.php');
  require('../includes/classes/weight.php');
  require('../includes/classes/xml.php');
  require('../includes/classes/datetime.php');

// set the language
  require('includes/classes/language.php');
  $osC_Language = new osC_Language_Admin();

  if (isset($_GET['language']) && !empty($_GET['language'])) {
    $osC_Language->set($_GET['language']);
  }

  $osC_Language->loadIniFile();

  header('Content-Type: text/html; charset=' . $osC_Language->getCharacterSet());

  osc_setlocale(LC_TIME, explode(',', $osC_Language->getLocale()));

// define our localization functions
  require('includes/functions/localization.php');

// initialize the message stack for output messages
  require('../includes/classes/message_stack.php');
  $osC_MessageStack = new osC_MessageStack();

// entry/item info classes
  require('includes/classes/object_info.php');

// email class
  require('../includes/classes/mail.php');

// file uploading class
  require('includes/classes/upload.php');

// check if a default currency is set
  if (!defined('DEFAULT_CURRENCY')) {
    $osC_MessageStack->add('header', $osC_Language->get('ms_error_no_default_currency'), 'error');
  }

// check if a default language is set
  if (!defined('DEFAULT_LANGUAGE')) {
    $osC_MessageStack->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
  }

  if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false) ) {
    $osC_MessageStack->add('header', $osC_Language->get('ms_warning_uploads_disabled'), 'warning');
  }
?>
