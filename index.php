<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\OSCOM;

  define('OSCOM_TIMESTAMP_START', microtime());

  error_reporting(E_ALL | E_STRICT);

  define('OSCOM_PUBLIC_BASE_DIRECTORY', __DIR__ . '/');

  require('osCommerce/OM/Core/Autoloader.php');
  spl_autoload_register('osCommerce\\OM\\Core\\Autoloader::load');

  OSCOM::initialize();

  echo $OSCOM_Template->getContent();
?>
