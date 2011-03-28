<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\Model;

  use osCommerce\OM\Core\PDO;

  class checkDB {
    public static function execute($data) {
      return PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']);
    }
  }
?>
