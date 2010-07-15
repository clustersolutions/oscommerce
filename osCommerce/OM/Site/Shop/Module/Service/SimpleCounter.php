<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Service;

  use osCommerce\OM\Registry;
  use osCommerce\OM\DateTime;

  class SimpleCounter implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      $OSCOM_Database = Registry::get('Database');

      $Qcounter = $OSCOM_Database->query('select startdate, counter from :table_counter');
      $Qcounter->execute();

      if ( $Qcounter->numberOfRows() ) {
        $counter_startdate = $Qcounter->value('startdate');
        $counter_now = $Qcounter->valueInt('counter') + 1;

        $Qcounterupdate = $OSCOM_Database->query('update :table_counter set counter = counter+1');
        $Qcounterupdate->execute();
      } else {
        $counter_startdate = DateTime::getNow();
        $counter_now = 1;

        $Qcounterupdate = $OSCOM_Database->query('insert into :table_counter (startdate, counter) values (:start_date, 1)');
        $Qcounterupdate->bindValue(':start_date', $counter_startdate);
        $Qcounterupdate->execute();
      }

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>
