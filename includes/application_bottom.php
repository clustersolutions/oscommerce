<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $messageStack->add('debug', 'Number of queries: ' . $osC_Database->numberOfQueries() . ' [' . $osC_Database->timeOfQueries() . 's]', 'warning');

  $osC_Services->stopServices();
?>