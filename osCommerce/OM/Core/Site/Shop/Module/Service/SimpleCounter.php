<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\DateTime;

  class SimpleCounter implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcounter = $OSCOM_PDO->query('select startdate, counter from :table_counter');
      $Qcounter->execute();

      $result = $Qcounter->fetchAll();

      if ( count($result) > 0 ) {
        $OSCOM_PDO->exec('update :table_counter set counter = counter+1');
      } else {
        $Qcounterupdate = $OSCOM_PDO->prepare('insert into :table_counter (startdate, counter) values (:start_date, 1)');
        $Qcounterupdate->bindValue(':start_date', DateTime::getNow());
        $Qcounterupdate->execute();
      }

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
