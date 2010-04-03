<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

// to delete ///////
  define('PAGE_PARSE_START_TIME', microtime());
  define('PROJECT_VERSION', 'osCommerce Online Merchant $osCommerce-SIG$');
  $request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) ? 'SSL' : 'NONSSL';
////////////////////


  define('OSCOM_TIMESTAMP_START', microtime());

  error_reporting(E_ALL);

  define('OSCOM_BASE_DIRECTORY', dirname(dirname(__FILE__)));

  require('core/OSCOM.php');
  OSCOM::initialize();

// redirect to the installation module if DB_SERVER is empty
  if (strlen(DB_SERVER) < 1) {
    if (is_dir('install')) {
      header('Location: install/index.php');
    }
  }



  if ($request_type == 'NONSSL') {
    define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
  } else {
    define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
  }

// compatibility logic
  require('includes/functions/compatibility.php');

// include the list of project filenames
  require('includes/filenames.php');

// include the list of project database tables
  require('includes/database_tables.php');

// initialize the message stack for output messages
//  require('includes/classes/message_stack.php');
  $osC_MessageStack = new OSCOM_Core_MessageStack();

// initialize the cache class
//  require('includes/classes/cache.php');
  $osC_Cache = new OSCOM_Core_Cache();

// include the database class
//  require('includes/classes/database.php');

// make a connection to the database... now
  $osC_Database = OSCOM_Core_Database::connect();

// set the application parameters
  $Qcfg = $osC_Database->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
  $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qcfg->setCache('configuration');
  $Qcfg->execute();

  while ($Qcfg->next()) {
    define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
  }

  $Qcfg->freeResult();

// include functions
  require('includes/functions/general.php');
  require('includes/functions/html_output.php');

// include and start the services
//  require('includes/classes/services.php');
  $osC_Services = new OSCOM_Core_Services();
  $osC_Services->startServices();
?>
