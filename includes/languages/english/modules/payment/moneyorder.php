<?php
/*
  $Id: moneyorder.php,v 1.7 2004/07/22 21:58:11 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', 'Check/Money Order');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION', 'Make Payable To:&nbsp;%s<br><br>Send To:<br>' . nl2br(STORE_NAME_ADDRESS) . '<br><br>' . 'Your order will not ship until we receive payment.');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', 'Make Payable To: %s' . "\n\n" . 'Send To:' . "\n" . STORE_NAME_ADDRESS . "\n\n" . 'Your order will not ship until we receive payment.');
?>
