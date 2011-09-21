<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session\Database\SQL\ANSI;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( OSCOM::callDB('Session\Database\Check', array('id' => $data['id']), 'Core') ) {
        $sql_query = 'update :table_sessions set expiry = :expiry, value = :value where id = :id';
      } else {
        $sql_query = 'insert into :table_sessions values (:id, :expiry, :value)';
      }

      $Qsession = $OSCOM_PDO->prepare($sql_query);
      $Qsession->bindValue(':id', $data['id']);
      $Qsession->bindInt(':expiry', $data['expiry']);
      $Qsession->bindValue(':value', $data['value']);
      $Qsession->execute();

      return ( ($Qsession->rowCount() === 1) || !$Qsession->isError() );
    }
  }
?>
