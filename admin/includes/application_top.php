<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// set the local configuration parameters - mainly for developers
  if (file_exists('../includes/local/configure.php')) include('../includes/local/configure.php');

// include server parameters
  require('../includes/configure.php');

// set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);

// Define the project version
  define('PROJECT_VERSION', 'osCommerce 3.0a1');

// set the type of request (secure or not)
  $request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) ? 'SSL' : 'NONSSL';

  if ($request_type == 'NONSSL') {
    define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
  } else {
    define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
  }

// Used in the "Backup Manager" to compress backups
  define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
  define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
  define('LOCAL_EXE_ZIP', '/usr/local/bin/zip');
  define('LOCAL_EXE_UNZIP', '/usr/local/bin/unzip');

// compatibility work-around logic for PHP4
  require('includes/functions/compatibility.php');

// include the list of project filenames
  require('includes/filenames.php');

// include the list of project database tables
  require('../includes/database_tables.php');

// initialize the cache class
  require('../includes/classes/cache.php');
  $osC_Cache = new osC_Cache();

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
  require('includes/functions/general.php');
  require('includes/functions/html_output.php');

// include session class
  include('../includes/classes/session.php');
  $osC_Session = new osC_Session('osCAdminID');
  $osC_Session->start();

  if (isset($_SESSION['admin']) === false) {
    if (basename($_SERVER['SCRIPT_FILENAME']) != 'login.php') {
      tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
  }

// set the language
  require('includes/classes/language.php');
  $osC_Language = new osC_Language_Admin();

  if (isset($_GET['language']) && !empty($_GET['language'])) {
    $osC_Language->set($_GET['language']);
  }

  $osC_Language->load();

  header('Content-Type: text/html; charset=' . CHARSET);
  setlocale(LC_TIME, LANGUAGE_LOCALE);

  $osC_Language->load(basename($_SERVER['SCRIPT_FILENAME']));

// define our localization functions
  require('includes/functions/localization.php');

// Include validation functions (right now only email address)
  require('includes/functions/validations.php');

// initialize the message stack for output messages
  require('../includes/classes/message_stack.php');
  $osC_MessageStack = new messageStack();
  $osC_MessageStack->loadFromSession();

// entry/item info classes
  require('includes/classes/object_info.php');

// email classes
  require('includes/classes/mime.php');
  require('includes/classes/email.php');

// file uploading class
  require('includes/classes/upload.php');

// check if a default currency is set
  if (!defined('DEFAULT_CURRENCY')) {
    $osC_MessageStack->add('header', ERROR_NO_DEFAULT_CURRENCY_DEFINED, 'error');
  }

// check if a default language is set
  if (!defined('DEFAULT_LANGUAGE')) {
    $osC_MessageStack->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
  }

  if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false) ) {
    $osC_MessageStack->add('header', WARNING_FILE_UPLOADS_DISABLED, 'warning');
  }
?>
