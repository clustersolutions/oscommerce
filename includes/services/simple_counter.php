<?php
/*
  $Id:simple_counter.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_simple_counter {
    var $title = 'Simple Counter',
        $description = 'Count the number of page requests made.',
        $uninstallable = true,
        $depends,
        $preceeds;

    function start() {
      global $osC_Database, $messageStack;

      $Qcounter = $osC_Database->query('select startdate, counter from :table_counter');
      $Qcounter->bindTable(':table_counter', TABLE_COUNTER);
      $Qcounter->execute();

      if ($Qcounter->numberOfRows()) {
        $counter_startdate = $Qcounter->value('startdate');
        $counter_now = $Qcounter->valueInt('counter') + 1;

        $Qcounterupdate = $osC_Database->query('update :table_counter set counter = counter+1');
        $Qcounterupdate->bindTable(':table_counter', TABLE_COUNTER);
        $Qcounterupdate->execute();

        $Qcounterupdate->freeResult();
      } else {
        $counter_startdate = osC_DateTime::getNow();
        $counter_now = 1;

        $Qcounterupdate = $osC_Database->query('insert into :table_counter (startdate, counter) values (:start_date, 1)');
        $Qcounterupdate->bindTable(':table_counter', TABLE_COUNTER);
        $Qcounterupdate->bindValue(':start_date', $counter_startdate);
        $Qcounterupdate->execute();

        $Qcounterupdate->freeResult();
      }

      $Qcounter->freeResult();

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      return false;
    }

    function remove() {
      return false;
    }

    function keys() {
      return false;
    }
  }
?>
