<?php
/*
  $Id: simple_counter.php,v 1.2 2004/04/13 07:30:31 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

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
      $Qcounter->bindRaw(':table_counter', TABLE_COUNTER);
      $Qcounter->execute();

      if ($Qcounter->numberOfRows()) {
        $counter_startdate = $Qcounter->value('startdate');
        $counter_now = $Qcounter->valueInt('counter') + 1;

        $Qcounterupdate = $osC_Database->query('update :table_counter set counter = counter+1');
        $Qcounterupdate->bindRaw(':table_counter', TABLE_COUNTER);
        $Qcounterupdate->execute();

        $Qcounterupdate->freeResult();
      } else {
        $counter_startdate = date('Ymd');
        $counter_now = 1;

        $Qcounterupdate = $osC_Database->query('insert into :table_counter (startdate, counter) values (:start_date, 1)');
        $Qcounterupdate->bindRaw(':table_counter', TABLE_COUNTER);
        $Qcounterupdate->bindValue(':start_date', $counter_startdate);
        $Qcounterupdate->execute();

        $Qcounterupdate->freeResult();
      }

      $Qcounter->freeResult();

      $counter_startdate_formatted = strftime(DATE_FORMAT_LONG, mktime(0, 0, 0, substr($counter_startdate, 4, 2), substr($counter_startdate, -2), substr($counter_startdate, 0, 4)));

      $messageStack->add('counter', number_format($counter_now) . ' ' . FOOTER_TEXT_REQUESTS_SINCE . ' ' . $counter_startdate_formatted);

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
