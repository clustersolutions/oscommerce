<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  interface OSCOM_SiteInterface {
    public static function initialize();

    public static function getDefaultApplication();

    public static function hasAccess($application);
  }
?>
