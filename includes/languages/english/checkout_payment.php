<?php
/*
  $Id: checkout_payment.php,v 1.19 2003/12/04 12:45:46 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'Checkout');
define('NAVBAR_TITLE_2', 'Payment Method');

define('HEADING_TITLE', 'Payment Information');

define('TABLE_HEADING_BILLING_ADDRESS', 'Billing Address');
define('TEXT_SELECTED_BILLING_DESTINATION', 'Please choose from your address book where you would like the invoice to be sent to.');
define('TITLE_BILLING_ADDRESS', 'Billing Address:');

define('TABLE_HEADING_CONDITIONS', 'Terms and Conditions');
define('TEXT_CONDITIONS_DESCRIPTION', 'Please acknowledge the terms and conditions bound to this order by ticking the following box. The terms and conditions can be read <a href="' . tep_href_link(FILENAME_CONDITIONS, '', 'SSL') . '"><u>here</u></a>.');
define('TEXT_CONDITIONS_CONFIRM', 'I have read and agreed to the terms and conditions bound to this order.');

define('TABLE_HEADING_PAYMENT_METHOD', 'Payment Method');
define('TEXT_SELECT_PAYMENT_METHOD', 'Please select the preferred payment method to use on this order.');
define('TITLE_PLEASE_SELECT', 'Please Select');
define('TEXT_ENTER_PAYMENT_INFORMATION', 'This is currently the only payment method available to use on this order.');

define('TABLE_HEADING_COMMENTS', 'Add Comments About Your Order');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'Continue Checkout Procedure');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', 'to confirm this order.');
?>
