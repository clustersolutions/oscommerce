<?php
/*
  $Id: application_bottom.php,v 1.17 2004/04/13 07:34:08 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $messageStack->add('debug', 'Number of queries: ' . $osC_Database->numberOfQueries() . ' [' . $osC_Database->timeOfQueries() . 's]', 'warning');

  $osC_Services->stopServices();
?>