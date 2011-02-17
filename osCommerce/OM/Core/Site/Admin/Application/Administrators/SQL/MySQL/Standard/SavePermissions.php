<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Administrators\Administrators;

  class SavePermissions { // HPDL Albert would proudly say "Hey, hey, hey, it's a faattt SQL module!"; abstraction needed
    public static function execute($data) {
      $OSCOM_Database = Registry::get('PDO');

      $error = false;

      $OSCOM_Database->beginTransaction();

      if ( ($data['mode'] == Administrators::ACCESS_MODE_ADD) || ($data['mode'] == Administrators::ACCESS_MODE_SET) ) {
        foreach ( $data['modules'] as $module ) {
          $execute = true;

          if ( $module != '*' ) {
            $Qcheck = $OSCOM_Database->prepare('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindInt(':administrators_id', $data['id']);
            $Qcheck->bindValue(':module', '*');
            $Qcheck->execute();

            if ( $Qcheck->fetch() !== false ) {
              $execute = false;
            }
          }

          if ( $execute === true ) {
            $Qcheck = $OSCOM_Database->prepare('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindInt(':administrators_id', $data['id']);
            $Qcheck->bindValue(':module', $module);
            $Qcheck->execute();

            if ( $Qcheck->fetch() === false ) {
              $Qinsert = $OSCOM_Database->prepare('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
              $Qinsert->bindInt(':administrators_id', $data['id']);
              $Qinsert->bindValue(':module', $module);
              $Qinsert->execute();

              if ( $Qinsert->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        if ( ($data['mode'] == Administrators::ACCESS_MODE_REMOVE) || ($data['mode'] == Administrators::ACCESS_MODE_SET) || in_array('*', $data['modules']) ) {
          if ( !empty($data['modules']) ) {
            $sql_query = 'delete from :table_administrators_access where administrators_id = :administrators_id';

            if ( $data['mode'] == Administrators::ACCESS_MODE_REMOVE ) {
              if ( !in_array('*', $data['modules']) ) {
                $sql_query .= ' and module in ("' . implode('", "', $data['modules']) . '")'; // HPDL create bindRaw()?
              }
            } else {
              $sql_query .= ' and module not in ("' . implode('", "', $data['modules']) . '")'; // HPDL create bindRaw()?
            }

            $Qdel = $OSCOM_Database->prepare($sql_query);
            $Qdel->bindInt(':administrators_id', $data['id']);
            $Qdel->execute();

            if ( $Qdel->isError() ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        $OSCOM_Database->commit();

        return true;
      }

      $OSCOM_Database->rollBack();

      return false;
    }
  }
?>
