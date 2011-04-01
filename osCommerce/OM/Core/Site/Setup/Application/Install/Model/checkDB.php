<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Setup\Application\Install\Model;

  use osCommerce\OM\Core\PDO;

  class checkDB {
    public static function execute($data) {
      return PDO::initialize($data['server'], $data['username'], $data['password'], $data['database'], $data['port'], $data['class']);
    }
  }
?>
