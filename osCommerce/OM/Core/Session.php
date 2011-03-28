<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\OSCOM;

/**
 * The Session class initializes the session storage handler
 */

  class Session {

/**
 * Loads the session storage handler
 *
 * @param string $name The name of the session
 * @access public
 */

    public static function load($name = null) {
      $class_name = 'osCommerce\\OM\\Core\\Session\\' . OSCOM::getConfig('store_sessions');

      if ( class_exists($class_name) ) {
        return new $class_name($name);
      }

      trigger_error('Session Handler \'' . $class_name . '\' does not exist, using default \'osCommerce\\OM\\Core\\Session\\File\'', E_USER_ERROR);

      return new Session\File($name);
    }
  }
?>
