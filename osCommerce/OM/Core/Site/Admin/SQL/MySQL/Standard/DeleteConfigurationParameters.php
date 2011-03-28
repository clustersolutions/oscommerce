<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class DeleteConfigurationParameters {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( !is_array($data) ) {
        $data = array($data);
      }

      $error = false;
      $in_transaction = false;

      if ( count($data) > 1 ) {
        $OSCOM_PDO->beginTransaction();

        $in_transaction = true;
      }

      $Qcfg = $OSCOM_PDO->prepare('delete from :table_configuration where configuration_key = :configuration_key');

      foreach ( $data as $key ) {
        $Qcfg->bindValue(':configuration_key', $key);
        $Qcfg->execute();

        if ( $Qcfg->isError() ) {
          if ( $in_transaction === true ) {
            $OSCOM_PDO->rollBack();
          }

          $error = true;

          break;
        }
      }

      if ( ($error === false) && ($in_transaction === true) ) {
        $OSCOM_PDO->commit();
      }

      return !$error;
    }
  }
?>
