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

  define('OSCOM_TIMESTAMP_START', microtime());

  error_reporting(E_ALL | E_STRICT);

  require('osCommerce/OM/Core/Autoloader.php');
  $OSCOM_Autoloader = new Autoloader('osCommerce\OM');
  $OSCOM_Autoloader->register();

  OSCOM::initialize();

  require($OSCOM_Template->getTemplateFile());
?>
