<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ServerInfo\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetTime {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $result = $OSCOM_PDO->query('select cast(now() as timestamp(0)) as datetime')->fetch();

      return $result['datetime'];
    }
  }
?>
