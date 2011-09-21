<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session\Database\SQL\ANSI;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Delete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qsession = $OSCOM_PDO->prepare('delete from :table_sessions where id = :id');
      $Qsession->bindValue(':id', $data['id']);
      $Qsession->execute();

      return ( $Qsession->rowCount() === 1 );
    }
  }
?>
