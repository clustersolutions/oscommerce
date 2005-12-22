<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  define('NAVBAR_TITLE_CHECKOUT', 'Checkout');
  define('NAVBAR_TITLE_CHECKOUT_SHOPPING_CART', 'Shopping Cart');
  define('NAVBAR_TITLE_CHECKOUT_SHIPPING', 'Shipping Method');
  define('NAVBAR_TITLE_CHECKOUT_SHIPPING_ADDRESS', 'Shipping Address');
  define('NAVBAR_TITLE_CHECKOUT_PAYMENT', 'Payment Method');
  define('NAVBAR_TITLE_CHECKOUT_PAYMENT_ADDRESS', 'Payment Address');
  define('NAVBAR_TITLE_CHECKOUT_CONFIRMATION', 'Confirmation');
  define('NAVBAR_TITLE_CHECKOUT_SUCCESS', 'Success!');

  define('HEADING_TITLE_CHECKOUT_SHOPPING_CART', 'Shopping Cart');
  define('HEADING_TITLE_CHECKOUT_SHIPPING', 'Shipping Method');
  define('HEADING_TITLE_CHECKOUT_SHIPPING_ADDRESS', 'Shipping Address');
  define('HEADING_TITLE_CHECKOUT_PAYMENT', 'Payment Method');
  define('HEADING_TITLE_CHECKOUT_PAYMENT_ADDRESS', 'Payment Address');
  define('HEADING_TITLE_CHECKOUT_CONFIRMATION', 'Confirmation');
  define('HEADING_TITLE_CHECKOUT_SUCCESS', 'Your Order Has Been Processed!');

  define('TABLE_HEADING_SHIPPING_ADDRESS', 'Shipping Address');
  define('TEXT_CHOOSE_SHIPPING_DESTINATION', 'Please choose from your address book where you would like the items to be delivered to.');
  define('TEXT_SELECTED_SHIPPING_DESTINATION', 'This is the currently selected shipping address where the items in this order will be delivered to.');
  define('TITLE_SHIPPING_ADDRESS', 'Shipping Address:');

  define('TITLE_PLEASE_SELECT', 'Please Select');

  define('TABLE_HEADING_SHIPPING_METHOD', 'Shipping Method');
  define('TEXT_CHOOSE_SHIPPING_METHOD', 'Please select the preferred shipping method to use on this order.');
  define('TEXT_ENTER_SHIPPING_INFORMATION', 'This is currently the only shipping method available to use on this order.');

  define('TABLE_HEADING_COMMENTS', 'Add Comments About Your Order');

  define('TABLE_HEADING_ADDRESS_BOOK_ENTRIES', 'Address Book Entries');
  define('TEXT_SELECT_OTHER_SHIPPING_DESTINATION', 'Please select the preferred shipping address if the items in this order are to be delivered elsewhere.');
  define('TEXT_SELECT_OTHER_PAYMENT_DESTINATION', 'Please select the preferred billing address if the invoice to this order is to be delivered elsewhere.');
  define('TITLE_PLEASE_SELECT', 'Please Select');

  define('TABLE_HEADING_NEW_SHIPPING_ADDRESS', 'New Shipping Address');
  define('TABLE_HEADING_NEW_PAYMENT_ADDRESS', 'New Billing Address');
  define('TEXT_CREATE_NEW_SHIPPING_ADDRESS', 'Please use the following form to create a new shipping address to use for this order.');
  define('TEXT_CREATE_NEW_PAYMENT_ADDRESS', 'Please use the following form to create a new billing address to use for this order.');

  define('TABLE_HEADING_PAYMENT_ADDRESS', 'Billing Address');
  define('TEXT_SELECTED_PAYMENT_DESTINATION', 'This is the currently selected billing address where the invoice to this order will be delivered to.');
  define('TITLE_PAYMENT_ADDRESS', 'Billing Address:');

  define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'Continue Checkout Procedure');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_SHIPPING', 'to select the preferred shipping method.');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_PAYMENT', 'to select the preferred payment method.');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_CONFIRMATION', 'to confirm this order.');

  define('TABLE_HEADING_REMOVE', 'Remove');
  define('TABLE_HEADING_QUANTITY', 'Qty.');
  define('TABLE_HEADING_MODEL', 'Model');
  define('TABLE_HEADING_PRODUCTS', 'Product(s)');
  define('TABLE_HEADING_TOTAL', 'Total');
  define('TEXT_CART_EMPTY', 'Your Shopping Cart is empty!');
  define('SUB_TITLE_SUB_TOTAL', 'Sub-Total:');
  define('SUB_TITLE_TOTAL', 'Total:');

  define('OUT_OF_STOCK_CANT_CHECKOUT', 'Products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' don\'t exist in desired quantity in our stock.<br />Please alter the quantity of products marked with (' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '), Thank you');
  define('OUT_OF_STOCK_CAN_CHECKOUT', 'Products marked with ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' don\'t exist in desired quantity in our stock.<br />You can buy them anyway and check the quantity we have in stock for immediate delivery in the checkout process.');

  define('TABLE_HEADING_BILLING_ADDRESS', 'Billing Address');
  define('TEXT_SELECTED_BILLING_DESTINATION', 'Please choose from your address book where you would like the invoice to be sent to.');
  define('TITLE_BILLING_ADDRESS', 'Billing Address:');

  define('TABLE_HEADING_CONDITIONS', 'Terms and Conditions');
  define('TEXT_CONDITIONS_DESCRIPTION', 'Please acknowledge the terms and conditions bound to this order by ticking the following box. The terms and conditions can be read <a href="' . tep_href_link(FILENAME_INFO, 'conditions', 'SSL') . '"><u>here</u></a>.');
  define('TEXT_CONDITIONS_CONFIRM', 'I have read and agreed to the terms and conditions bound to this order.');

  define('TABLE_HEADING_PAYMENT_METHOD', 'Payment Method');
  define('TEXT_SELECT_PAYMENT_METHOD', 'Please select the preferred payment method to use on this order.');
  define('TEXT_ENTER_PAYMENT_INFORMATION', 'This is currently the only payment method available to use on this order.');

  define('HEADING_DELIVERY_ADDRESS', 'Delivery Address');
  define('HEADING_SHIPPING_METHOD', 'Shipping Method');
  define('HEADING_PRODUCTS', 'Products');
  define('HEADING_TAX', 'Tax');
  define('HEADING_TOTAL', 'Total');
  define('HEADING_BILLING_INFORMATION', 'Billing Information');
  define('HEADING_BILLING_ADDRESS', 'Billing Address');
  define('HEADING_PAYMENT_METHOD', 'Payment Method');
  define('HEADING_PAYMENT_INFORMATION', 'Payment Information');
  define('HEADING_ORDER_COMMENTS', 'Comments About Your Order');

  define('TEXT_EDIT', 'Edit');

  define('EMAIL_TEXT_SUBJECT', 'Order Process');
  define('EMAIL_TEXT_ORDER_NUMBER', 'Order Number:');
  define('EMAIL_TEXT_INVOICE_URL', 'Detailed Invoice:');
  define('EMAIL_TEXT_DATE_ORDERED', 'Date Ordered:');
  define('EMAIL_TEXT_PRODUCTS', 'Products');
  define('EMAIL_TEXT_SUBTOTAL', 'Sub-Total:');
  define('EMAIL_TEXT_TAX', 'Tax:        ');
  define('EMAIL_TEXT_SHIPPING', 'Shipping: ');
  define('EMAIL_TEXT_TOTAL', 'Total:    ');
  define('EMAIL_TEXT_DELIVERY_ADDRESS', 'Delivery Address');
  define('EMAIL_TEXT_BILLING_ADDRESS', 'Billing Address');
  define('EMAIL_TEXT_PAYMENT_METHOD', 'Payment Method');

  define('EMAIL_SEPARATOR', '------------------------------------------------------');
  define('TEXT_EMAIL_VIA', 'via');

  define('TEXT_SUCCESS', 'Your order has been successfully processed! Your products will arrive at their destination within 2-5 working days.');
  define('TEXT_NOTIFY_PRODUCTS', 'Please notify me of updates to the products I have selected below:');
  define('TEXT_SEE_ORDERS', 'You can view your order history by going to the <a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'My Account\'</a> page and by clicking on <a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders', 'SSL') . '">\'History\'</a>.');
  define('TEXT_CONTACT_STORE_OWNER', 'Please direct any questions you have to the <a href="' . tep_href_link(FILENAME_INFO, 'contact') . '">store owner</a>.');
  define('TEXT_THANKS_FOR_SHOPPING', 'Thanks for shopping with us online!');

  define('TABLE_HEADING_DOWNLOAD_DATE', 'Expiry date: ');
  define('TABLE_HEADING_DOWNLOAD_COUNT', ' downloads remaining');
  define('HEADING_DOWNLOAD', 'Download your products here:');
  define('FOOTER_DOWNLOAD', 'You can also download your products at a later time at \'%s\'');
?>
