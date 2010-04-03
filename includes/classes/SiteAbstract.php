<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  abstract class OSCOM_SiteAbstract {
    protected static $_default_application = 'Index';

    abstract public static function initialize();

    public static function getDefaultApplication() {
      return self::$_default_application;
    }
  }
?>
