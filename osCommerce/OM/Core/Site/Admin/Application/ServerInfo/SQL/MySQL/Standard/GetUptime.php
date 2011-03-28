<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class GetUptime {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $result = $OSCOM_PDO->query('show status like "Uptime"')->fetch();

      return intval($result['Value'] / 3600) . ':' . str_pad(intval(($result['Value'] / 60) % 60), 2, '0', STR_PAD_LEFT);
    }
  }
?>
