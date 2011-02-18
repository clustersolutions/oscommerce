<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Autoloader;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

// HPDL to delete ///////
  define('PAGE_PARSE_START_TIME', microtime());
  define('PROJECT_VERSION', 'osCommerce Online Merchant $osCommerce-SIG$');
  $request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) ? 'SSL' : 'NONSSL';
////////////////////

  define('OSCOM_TIMESTAMP_START', microtime());

  error_reporting(E_ALL | E_STRICT);

  require('osCommerce/OM/Core/Autoloader.php');
  $OSCOM_Autoloader = new Autoloader('osCommerce\OM');
  $OSCOM_Autoloader->register();

  OSCOM::initialize();

  require(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/templates/' . Registry::get('Template')->getCode() . '.php');

//  require('includes/application_bottom.php');
?>
