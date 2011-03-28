<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\PaymentModules\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Save {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $error = false;

      $OSCOM_PDO->beginTransaction();

      foreach ( $data['configuration'] as $key => $value ) {
        $Qupdate = $OSCOM_PDO->prepare('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qupdate->bindValue(':configuration_value', is_array($data['configuration'][$key]) ? implode(',', $data['configuration'][$key]) : $value);
        $Qupdate->bindValue(':configuration_key', $key);
        $Qupdate->execute();

        if ( $Qupdate->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $OSCOM_PDO->commit();

        return true;
      }

      $OSCOM_PDO->rollBack();

      return false;
    }
  }
?>
